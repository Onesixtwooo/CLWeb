<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollegeInstitute extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'details',
        'logo',
        'description',
        'photo',
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
        'sort_order',
        'college_slug',
        'awards_is_visible',
        'research_is_visible',
        'extension_is_visible',
        'training_is_visible',
        'facilities_is_visible',
        'alumni_is_visible',
        'programs_is_visible',
        'history',
    ];

    protected $casts = [
        'banner_images' => 'array',
        'sort_order' => 'integer',
        'awards_is_visible' => 'boolean',
        'research_is_visible' => 'boolean',
        'extension_is_visible' => 'boolean',
        'training_is_visible' => 'boolean',
        'facilities_is_visible' => 'boolean',
        'alumni_is_visible' => 'boolean',
        'programs_is_visible' => 'boolean',
    ];


    /**
     * Get the college that owns the institute.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class, 'college_slug', 'slug');
    }

    public function faculty(): HasMany
    {
        return $this->hasMany(Faculty::class, 'institute_id')->orderBy('sort_order');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(InstituteStaff::class, 'institute_id')->orderBy('sort_order');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(InstituteGoal::class, 'institute_id')->orderBy('sort_order');
    }

    public function research(): HasMany
    {
        return $this->hasMany(InstituteResearch::class, 'institute_id')->orderBy('sort_order');
    }

    public function extension(): HasMany
    {
        return $this->hasMany(InstituteExtension::class, 'institute_id')->orderBy('sort_order');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(InstituteFacility::class, 'institute_id')->orderBy('sort_order');
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(DepartmentOutcome::class, 'institute_id')->orderBy('sort_order');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(DepartmentObjective::class, 'institute_id')->orderBy('sort_order');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(DepartmentAward::class, 'institute_id')->orderBy('sort_order');
    }

    public function alumni(): HasMany
    {
        return $this->hasMany(DepartmentAlumnus::class, 'institute_id')->orderBy('sort_order');
    }

    public function training(): HasMany
    {
        return $this->hasMany(DepartmentTraining::class, 'institute_id')->orderBy('sort_order');
    }


    public function programs(): HasMany
    {
        return $this->hasMany(DepartmentProgram::class, 'institute_id')->orderBy('sort_order');
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(DepartmentCurriculum::class, 'institute_id')->orderBy('sort_order');
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
                'email' => $this->email,
                'phone' => $this->phone,
                'social_youtube' => $this->social_youtube,
                'social_linkedin' => $this->social_linkedin,
                'social_instagram' => $this->social_instagram,
                'social_other' => $this->social_other,
            ];
        }

        $relationalSections = [
            'awards' => ['relation' => 'awards', 'visibility' => 'awards_is_visible'],
            'research' => ['relation' => 'research', 'visibility' => 'research_is_visible'],
            'extension' => ['relation' => 'extension', 'visibility' => 'extension_is_visible'],
            'training' => ['relation' => 'training', 'visibility' => 'training_is_visible'],
            'facilities' => ['relation' => 'facilities', 'visibility' => 'facilities_is_visible'],
            'alumni' => ['relation' => 'alumni', 'visibility' => 'alumni_is_visible'],
            'programs' => ['relation' => 'programs', 'visibility' => 'programs_is_visible'],
        ];

        if (isset($relationalSections[$sectionSlug])) {
            $info = $relationalSections[$sectionSlug];
            return [
                'items' => $this->{$info['relation']}->toArray(),
                'is_visible' => (bool) $this->{$info['visibility']},
            ];
        }

        if ($sectionSlug === 'objectives') {
            return [
                'items' => $this->objectives->toArray(),
                'curriculum' => $this->curricula->toArray(),
                'is_visible' => true,
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
             $this->email = $content['email'] ?? null;
             $this->phone = $content['phone'] ?? null;
             $this->social_youtube = $content['social_youtube'] ?? null;
             $this->social_linkedin = $content['social_linkedin'] ?? null;
             $this->social_instagram = $content['social_instagram'] ?? null;
             $this->social_other = $content['social_other'] ?? null;
             $this->overview_title = $content['title'] ?? 'Overview';
             $this->overview_body = $content['body'] ?? null;
             return;
        }
    }
}
