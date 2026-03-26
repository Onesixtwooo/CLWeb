<?php

namespace App\Console\Commands;

use App\Services\FacebookService;
use Illuminate\Console\Command;

class FetchFacebookPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:fetch-posts {--limit=10 : Number of posts to fetch} {--use-db : Use database configs instead of global config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch recent posts from Facebook page(s) and create articles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $useDb = $this->option('use-db');

        $facebookService = new FacebookService();

        if ($useDb) {
            $this->info("Fetching posts from all configured Facebook pages (database)...");
            $created = $facebookService->processAllConfiguredPages();
        } else {
            $this->info("Fetching {$limit} recent Facebook posts (global config)...");
            
            // Debug: Show what credentials we're using
            $token = \App\Models\Setting::get('facebook_access_token');
            $pageId = \App\Models\Setting::get('facebook_page_id');
            $this->info("Using Page ID: " . ($pageId ? substr($pageId, 0, 10) . "..." : "MISSING"));
            $this->info("Using Access Token: " . ($token ? "Present (length: " . strlen($token) . ")" : "MISSING"));
            
            $created = $facebookService->processPosts($limit);
        }

        $this->info("Processed {$created} new articles from Facebook posts.");

        return Command::SUCCESS;
    }
}
