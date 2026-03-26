<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CollegeDepartment extends Model
{
    protected $fillable = [
        'college_slug',
        'name',
        'email',
        'phone',
        'details',
        'logo',
        'program_description',
        'graduate_outcomes',
        'graduate_outcomes_title',
        'graduate_outcomes_image',
        'banner_image',
        'banner_images',
        'card_image',
        'social_facebook',
        'social_x',
        'social_youtube',
        'social_linkedin',
        'social_instagram',
        'social_other',
        'overview_title',
        'overview_body',
        'faculty_title',
        'faculty_body',
        'faculty_is_visible',
        'objectives_is_visible',
        'objectives_title',
        'objectives_body',
        'sort_order',
        'awards_is_visible',
        'research_is_visible',
        'research_title',
        'research_body',
        'extension_is_visible',
        'extension_title',
        'extension_body',
        'training_is_visible',
        'training_title',
        'training_body',
        'facilities_is_visible',
        'membership_is_visible',
        'alumni_is_visible',
        'programs_is_visible',
        'programs_title',
        'programs_body',
        'linkages_title',
        'linkages_body',
        'linkages_is_visible',
        'organizations_title',
        'organizations_body',
        'organizations_is_visible',
        'facilities_title',
        'facilities_body',
        'membership_title',
        'membership_body',
        'awards_title',
        'awards_body',
    ];

    protected $casts = [
        'banner_images' => 'array',
        'faculty_is_visible' => 'boolean',
        'awards_is_visible' => 'boolean',
        'objectives_is_visible' => 'boolean',
        'research_is_visible' => 'boolean',
        'extension_is_visible' => 'boolean',
        'training_is_visible' => 'boolean',
        'facilities_is_visible' => 'boolean',
        'membership_is_visible' => 'boolean',
        'alumni_is_visible' => 'boolean',
        'programs_is_visible' => 'boolean',
        'linkages_is_visible' => 'boolean',
        'organizations_is_visible' => 'boolean',
    ];

    public function getRouteKey(): string
    {
        return Str::slug($this->name);
    }

    public static function findByCollegeAndRouteKey(string $collegeSlug, string|int $value): ?self
    {
        $query = static::where('college_slug', $collegeSlug);

        if (is_numeric($value)) {
            return (clone $query)->find((int) $value);
        }

        $routeKey = trim((string) $value);

        return (clone $query)
            ->get()
            ->first(function (self $department) use ($routeKey) {
                return $department->name === $routeKey || Str::slug($department->name) === $routeKey;
            });
    }

    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class, 'department', 'name')
                    ->where('college_slug', $this->college_slug);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(DepartmentOutcome::class, 'department_id')->orderBy('sort_order');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(DepartmentObjective::class, 'department_id')->orderBy('sort_order');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(DepartmentAward::class, 'department_id')->orderBy('sort_order');
    }

    public function research(): HasMany
    {
        return $this->hasMany(DepartmentResearch::class, 'department_id')->orderBy('sort_order');
    }

    public function alumni(): HasMany
    {
        return $this->hasMany(DepartmentAlumnus::class, 'department_id')->orderBy('sort_order');
    }

    public function extension(): HasMany
    {
        return $this->hasMany(DepartmentExtension::class, 'department_id')->orderBy('sort_order');
    }

    public function training(): HasMany
    {
        return $this->hasMany(DepartmentTraining::class, 'department_id')->orderBy('sort_order');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(DepartmentFacility::class, 'department_id')->orderBy('sort_order');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(DepartmentProgram::class, 'department_id')->orderBy('sort_order');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CollegeMembership::class, 'department_id')->orderBy('sort_order');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(CollegeOrganization::class, 'department_id')->orderBy('sort_order');
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(DepartmentCurriculum::class, 'department_id')->orderBy('sort_order');
    }

    public function linkages(): HasMany
    {
        return $this->hasMany(DepartmentLinkage::class, 'department_id')->orderBy('sort_order');
    }

    /**
     * Get a specific section content.
     */
    public function getSection(string $sectionSlug): ?array
    {
        if ($sectionSlug === 'overview') {
            return [
                'title' => $this->overview_title ?? 'Overview',
                'body' => $this->overview_body ?? '',
                'program_description' => $this->program_description,
                'graduate_outcomes' => $this->graduate_outcomes,
                'graduate_outcomes_title' => $this->graduate_outcomes_title,
                'graduate_outcomes_image' => $this->graduate_outcomes_image,
                'banner_image' => $this->banner_image,
                'banner_images' => $this->banner_images,
                'card_image' => $this->card_image,
                'social_facebook' => $this->social_facebook,
                'social_x' => $this->social_x,
                'social_youtube' => $this->social_youtube,
                'social_linkedin' => $this->social_linkedin,
                'social_instagram' => $this->social_instagram,
                'social_other' => $this->social_other,
                'email' => $this->email,
                'phone' => $this->phone,
            ];
        }

        if ($sectionSlug === 'faculty') {
            return [
                'title' => $this->faculty_title ?? 'Faculty',
                'body' => $this->faculty_body ?? '',
                'items' => $this->faculty->toArray(),
                'is_visible' => (bool) ($this->faculty_is_visible ?? true),
            ];
        }

        $relationalSections = [
            'awards' => ['relation' => 'awards', 'visibility' => 'awards_is_visible'],
            'research' => ['relation' => 'research', 'visibility' => 'research_is_visible'],
            'extension' => ['relation' => 'extension', 'visibility' => 'extension_is_visible'],
            'training' => ['relation' => 'training', 'visibility' => 'training_is_visible'],
            'programs' => ['relation' => 'programs', 'visibility' => 'programs_is_visible'],
        ];

        if (isset($relationalSections[$sectionSlug])) {
            $info = $relationalSections[$sectionSlug];
            $data = [
                'items' => $this->{$info['relation']}->toArray(),
                'is_visible' => (bool) $this->{$info['visibility']},
            ];

            if ($sectionSlug === 'awards') {
                $data['title'] = $this->awards_title ?? 'Student & Faculty Awards';
                $data['body'] = $this->awards_body;
            }

            if ($sectionSlug === 'research') {
                $data['title'] = $this->research_title ?? 'Research';
                $data['body'] = $this->research_body;
            }

            if ($sectionSlug === 'extension') {
                $data['title'] = $this->extension_title ?? 'Extension Services';
                $data['body'] = $this->extension_body;
            }

            if ($sectionSlug === 'training') {
                $data['title'] = $this->training_title ?? 'Training & Workshops';
                $data['body'] = $this->training_body;
            }

            if ($sectionSlug === 'programs') {
                $data['title'] = $this->programs_title ?? 'Programs';
                $data['body'] = $this->programs_body;
            }

            return $data;
        }

        if ($sectionSlug === 'objectives') {
            return [
                'title' => $this->objectives_title ?? 'Objectives',
                'body' => $this->objectives_body ?? '',
                'items' => $this->objectives->toArray(),
                'curriculum' => $this->curricula->toArray(),
                'is_visible' => (bool) ($this->objectives_is_visible ?? true),
            ];
        }

        if ($sectionSlug === 'linkages') {
            return [
                'title' => $this->linkages_title ?? 'Linkages',
                'body' => $this->linkages_body ?? '',
                'items' => $this->linkages->toArray(),
                'is_visible' => (bool) $this->linkages_is_visible,
            ];
        }

        if ($sectionSlug === 'facilities') {
            return [
                'title' => $this->facilities_title ?? 'Facilities',
                'body' => $this->facilities_body ?? '',
                'items' => $this->facilities->toArray(),
                'is_visible' => (bool) $this->facilities_is_visible,
            ];
        }

        if ($sectionSlug === 'membership') {
            return [
                'title' => $this->membership_title ?? 'Memberships',
                'body' => $this->membership_body ?? '',
                'items' => $this->memberships->toArray(),
                'is_visible' => (bool) $this->membership_is_visible,
            ];
        }

        if ($sectionSlug === 'organizations') {
            return [
                'title' => $this->organizations_title ?? 'Student Organizations',
                'body' => $this->organizations_body ?? '',
                'items' => $this->organizations->toArray(),
                'is_visible' => (bool) ($this->organizations_is_visible ?? true),
            ];
        }

        if ($sectionSlug === 'alumni') {
            return [
                'title' => $this->alumni_title ?? 'Testimonials',
                'body' => $this->alumni_body ?? '',
                'items' => $this->alumni->toArray(),
                'is_visible' => (bool) $this->alumni_is_visible,
            ];
        }

        return null;
    }

    /**
     * Set a specific section content.
     */
    public function setSection(string $sectionSlug, array $content): void
    {
        if ($sectionSlug === 'overview') {
             $this->program_description = $content['program_description'] ?? null;
             $this->graduate_outcomes = $content['graduate_outcomes'] ?? null;
             $this->graduate_outcomes_title = $content['graduate_outcomes_title'] ?? null;
             $this->graduate_outcomes_image = $content['graduate_outcomes_image'] ?? null;
             $this->banner_image = $content['banner_image'] ?? null;
             $this->banner_images = $content['banner_images'] ?? null;
             $this->card_image = $content['card_image'] ?? null;
             $this->social_facebook = $content['social_facebook'] ?? null;
             $this->social_x = $content['social_x'] ?? null;
             $this->social_youtube = $content['social_youtube'] ?? null;
             $this->social_linkedin = $content['social_linkedin'] ?? null;
             $this->social_instagram = $content['social_instagram'] ?? null;
             $this->social_other = $content['social_other'] ?? null;
             $this->overview_title = $content['title'] ?? 'Overview';
             $this->overview_body = $content['body'] ?? null;
             $this->email = $content['email'] ?? null;
             $this->phone = $content['phone'] ?? null;
             return;
        }

        if ($sectionSlug === 'linkages') {
            $this->linkages_title = $content['title'] ?? 'Linkages';
            $this->linkages_body = $content['body'] ?? null;
            $this->linkages_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'faculty') {
            $this->faculty_title = $content['title'] ?? 'Faculty';
            $this->faculty_body = $content['body'] ?? null;
            $this->faculty_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'facilities') {
            $this->facilities_title = $content['title'] ?? 'Facilities';
            $this->facilities_body = $content['body'] ?? null;
            $this->facilities_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'membership') {
            $this->membership_title = $content['title'] ?? 'Memberships';
            $this->membership_body = $content['body'] ?? null;
            $this->membership_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'organizations') {
            $this->organizations_title = $content['title'] ?? 'Student Organizations';
            $this->organizations_body = $content['body'] ?? null;
            $this->organizations_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'objectives') {
            $this->objectives_title = $content['title'] ?? 'Objectives';
            $this->objectives_body = $content['body'] ?? null;
            $this->objectives_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'awards') {
            $this->awards_title = $content['title'] ?? null;
            $this->awards_body = $content['body'] ?? null;
            return;
        }

        if ($sectionSlug === 'research') {
            $this->research_title = $content['title'] ?? 'Research';
            $this->research_body = $content['body'] ?? null;
            $this->research_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'extension') {
            $this->extension_title = $content['title'] ?? 'Extension Services';
            $this->extension_body = $content['body'] ?? null;
            $this->extension_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'training') {
            $this->training_title = $content['title'] ?? 'Training & Workshops';
            $this->training_body = $content['body'] ?? null;
            $this->training_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'programs') {
            $this->programs_title = $content['title'] ?? 'Programs';
            $this->programs_body = $content['body'] ?? null;
            $this->programs_is_visible = $content['is_visible'] ?? true;
            return;
        }

        if ($sectionSlug === 'alumni') {
            $this->alumni_title = $content['title'] ?? 'Testimonials';
            $this->alumni_body = $content['body'] ?? null;
            $this->alumni_is_visible = $content['is_visible'] ?? true;
            return;
        }
    }
}
