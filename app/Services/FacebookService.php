<?php

namespace App\Services;

use App\Models\Article;
use App\Models\CollegeDepartment;
use App\Models\CollegeOrganization;
use App\Models\FacebookConfig;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    protected string $graphApiUrl = 'https://graph.facebook.com/v18.0';

    protected string $postFields = 'id,message,created_time,permalink_url,attachments{url,title,description,type,media,target,subattachments},full_picture';

    /**
     * Fetch posts from a specific Facebook page using a config
     */
    public function fetchPostsFromConfig(FacebookConfig $config): array
    {
        try {
            $response = Http::get("{$this->graphApiUrl}/{$config->page_id}/posts", [
                'access_token' => $config->access_token,
                'fields' => $this->postFields,
                'limit' => $config->fetch_limit,
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error("Facebook API error for {$config->page_name}: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Facebook API exception for {$config->page_name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch recent posts from the Facebook page (using global config)
     */
    public function fetchPosts(int $limit = 10): array
    {
        $accessToken = config('services.facebook.access_token') ?: Setting::get('facebook_access_token');
        $pageId = config('services.facebook.page_id') ?: Setting::get('facebook_page_id');

        if (!$accessToken || !$pageId) {
            Log::error('Facebook global config missing - no access token or page id');
            return [];
        }

        try {
            Log::info("Fetching Facebook posts for page: {$pageId}");
            $response = Http::get("{$this->graphApiUrl}/{$pageId}/posts", [
                'access_token' => $accessToken,
                'fields' => $this->postFields,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $posts = $response->json()['data'] ?? [];
                Log::info('Facebook API returned ' . count($posts) . ' posts');
                return $posts;
            }

            Log::error('Facebook API error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Facebook API exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create an article from a Facebook post if it does not exist yet.
     */
    public function createArticleFromPost(array $post, ?FacebookConfig $config = null): ?Article
    {
        $slug = $this->slugForPostId($post['id'] ?? null);
        if (!$slug) {
            return null;
        }

        if (Article::where('slug', $slug)->exists()) {
            return null;
        }

        return $this->upsertArticleFromPost($post, $config);
    }

    /**
     * Process recent posts from database configs
     */
    public function processAllConfiguredPages(): int
    {
        $configs = FacebookConfig::active()->get();
        $created = 0;

        foreach ($configs as $config) {
            $posts = $this->fetchPostsFromConfig($config);
            foreach ($posts as $post) {
                if ($this->createArticleFromPost($post, $config)) {
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Process recent Facebook posts (legacy - uses global config)
     */
    public function processPosts(int $limit = 10): int
    {
        $posts = $this->fetchPosts($limit);
        $created = 0;

        foreach ($posts as $post) {
            if ($this->createArticleFromPost($post)) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Process a Facebook webhook payload and sync matching articles.
     */
    public function processWebhookPayload(array $payload): int
    {
        if (($payload['object'] ?? null) !== 'page') {
            Log::info('Facebook webhook ignored: unsupported object.', ['object' => $payload['object'] ?? null]);
            return 0;
        }

        $created = 0;

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) !== 'feed') {
                    continue;
                }

                $value = $change['value'] ?? [];
                $verb = strtolower((string) ($value['verb'] ?? ''));

                if ($verb === 'remove') {
                    continue;
                }

                if (array_key_exists('published', $value) && (int) $value['published'] !== 1) {
                    continue;
                }

                $config = $this->resolveConfigForWebhookEntry($entry, $value);
                $post = $this->resolvePostFromWebhookChange($entry, $value, $config);

                if (!$post) {
                    Log::warning('Facebook webhook change skipped: post could not be resolved.', [
                        'entry_id' => $entry['id'] ?? null,
                        'post_id' => $value['post_id'] ?? null,
                        'from_id' => $value['from']['id'] ?? null,
                    ]);
                    continue;
                }

                $slug = $this->slugForPostId($post['id'] ?? null);
                $alreadyExists = $slug ? Article::where('slug', $slug)->exists() : false;
                $article = $this->upsertArticleFromPost($post, $config);

                if ($article && !$alreadyExists) {
                    $created++;
                }
            }
        }

        return $created;
    }

    protected function upsertArticleFromPost(array $post, ?FacebookConfig $config = null): ?Article
    {
        $postId = $post['id'] ?? null;
        $slug = $this->slugForPostId($postId);

        if (!$slug) {
            return null;
        }

        $message = $this->extractPostMessage($post);
        $lines = preg_split('/\r\n|\r|\n/', trim($message)) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), static fn ($line) => $line !== ''));

        $defaultTitle = $this->defaultTitleForPost($post, $config);
        $title = $lines[0] ?? $defaultTitle;
        if (mb_strlen($title) > 100) {
            $title = mb_substr($title, 0, 97) . '...';
        }

        $bodyText = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : ($message !== '' ? $message : null);
        $body = $this->formatBodyForStorage($bodyText);

        $images = $this->extractImages($post);
        $banner = $images[0] ?? null;

        $articleData = [
            'title' => $title,
            'slug' => $slug,
            'type' => 'news',
            'body' => $body,
            'banner' => $banner,
            'images' => $images,
            'category' => $config?->article_category ?? 'Facebook Post',
            'author' => $config?->article_author ?? ($post['from']['name'] ?? 'CLSU Facebook Page'),
            'published_at' => !empty($post['created_time']) ? Carbon::parse($post['created_time']) : now(),
            'user_id' => $this->resolveArticleUserId(),
            'college_slug' => $this->resolveCollegeSlug($config),
        ];

        $article = Article::where('slug', $slug)->first();

        if ($article) {
            $article->update($articleData);
            Log::info("Updated article from Facebook post: {$article->title}");
            return $article->fresh();
        }

        $article = Article::create($articleData);
        Log::info("Created article from Facebook post: {$article->title}");

        return $article;
    }

    protected function resolvePostFromWebhookChange(array $entry, array $value, ?FacebookConfig $config = null): ?array
    {
        $post = [
            'id' => $value['post_id'] ?? null,
            'message' => $value['message'] ?? null,
            'created_time' => !empty($value['created_time']) ? Carbon::createFromTimestamp((int) $value['created_time'])->toIso8601String() : null,
            'from' => $value['from'] ?? [],
        ];

        if (!empty($value['link'])) {
            $post['permalink_url'] = $value['link'];
        }

        $needsLookup = empty($post['id']) || (empty($post['message']) && empty($value['link']));

        if ($needsLookup && !empty($value['post_id'])) {
            $fetchedPost = $this->fetchSinglePost($value['post_id'], $config);
            if ($fetchedPost) {
                return $fetchedPost;
            }
        }

        if (!empty($post['id'])) {
            return $post;
        }

        return null;
    }

    protected function fetchSinglePost(string $postId, ?FacebookConfig $config = null): ?array
    {
        $accessToken = $config?->access_token ?: (config('services.facebook.access_token') ?: Setting::get('facebook_access_token'));

        if (!$accessToken) {
            Log::warning('Facebook single-post fetch skipped: no access token available.', ['post_id' => $postId]);
            return null;
        }

        try {
            $response = Http::get("{$this->graphApiUrl}/{$postId}", [
                'access_token' => $accessToken,
                'fields' => $this->postFields,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Facebook single-post API error: ' . $response->body(), ['post_id' => $postId]);
            return null;
        } catch (\Exception $e) {
            Log::error('Facebook single-post API exception: ' . $e->getMessage(), ['post_id' => $postId]);
            return null;
        }
    }

    protected function resolveConfigForWebhookEntry(array $entry, array $value): ?FacebookConfig
    {
        $pageCandidates = array_values(array_filter(array_unique([
            $entry['id'] ?? null,
            $value['from']['id'] ?? null,
            $this->extractPageIdFromPostId($value['post_id'] ?? null),
        ])));

        if (empty($pageCandidates)) {
            return null;
        }

        return FacebookConfig::active()
            ->whereIn('page_id', $pageCandidates)
            ->orderBy('id')
            ->first();
    }

    protected function resolveCollegeSlug(?FacebookConfig $config): ?string
    {
        if (!$config) {
            return null;
        }

        if ($config->entity_type === 'college') {
            return $config->entity_id;
        }

        if ($config->entity_type === 'department') {
            return CollegeDepartment::find($config->entity_id)?->college_slug;
        }

        if ($config->entity_type === 'organization') {
            return CollegeOrganization::find($config->entity_id)?->college_slug;
        }

        return null;
    }

    protected function resolveArticleUserId(): ?int
    {
        return User::query()
            ->whereIn('role', [User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            ->orderBy('id')
            ->value('id');
    }

    protected function extractPostMessage(array $post): string
    {
        $parts = [];

        if (!empty($post['message'])) {
            $parts[] = trim((string) $post['message']);
        }

        foreach ($post['attachments']['data'] ?? [] as $attachment) {
            foreach (['title', 'description'] as $key) {
                if (!empty($attachment[$key])) {
                    $parts[] = trim((string) $attachment[$key]);
                }
            }
        }

        $parts = array_values(array_unique(array_filter($parts, static fn ($value) => $value !== '')));

        return implode("\n\n", $parts);
    }

    protected function defaultTitleForPost(array $post, ?FacebookConfig $config): string
    {
        $source = $config?->page_name
            ?? ($post['from']['name'] ?? null)
            ?? 'Facebook';

        return 'Facebook post from ' . $source;
    }

    protected function formatBodyForStorage(?string $body): ?string
    {
        if ($body === null) {
            return null;
        }

        $body = trim($body);
        if ($body === '') {
            return null;
        }

        $paragraphs = preg_split("/\R{2,}/", $body) ?: [];
        $paragraphs = array_values(array_filter(array_map('trim', $paragraphs), static fn ($paragraph) => $paragraph !== ''));

        if (empty($paragraphs)) {
            return null;
        }

        return implode('', array_map(function (string $paragraph): string {
            return '<p>' . nl2br(e($paragraph)) . '</p>';
        }, $paragraphs));
    }

    protected function extractImages(array $post): array
    {
        $images = [];

        if (!empty($post['full_picture'])) {
            $images[] = $post['full_picture'];
        }

        foreach ($post['attachments']['data'] ?? [] as $attachment) {
            $this->collectAttachmentImages($attachment, $images);
        }

        return array_values(array_unique(array_filter($images)));
    }

    protected function collectAttachmentImages(array $attachment, array &$images): void
    {
        if (!empty($attachment['media']['image']['src'])) {
            $images[] = $attachment['media']['image']['src'];
        }

        if (!empty($attachment['url'])) {
            $images[] = $attachment['url'];
        }

        foreach ($attachment['subattachments']['data'] ?? [] as $subattachment) {
            $this->collectAttachmentImages($subattachment, $images);
        }
    }

    protected function slugForPostId(?string $postId): ?string
    {
        if (!$postId) {
            return null;
        }

        return 'fb-' . $postId;
    }

    protected function extractPageIdFromPostId(?string $postId): ?string
    {
        if (!$postId || !str_contains($postId, '_')) {
            return null;
        }

        return explode('_', $postId, 2)[0];
    }
}
