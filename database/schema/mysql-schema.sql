-- Generated from the current Laravel MySQL schema.
-- This file replaces the historical PHP migration chain for fresh installs.
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `college_accreditations`;
DROP TABLE IF EXISTS `facility_images`;
DROP TABLE IF EXISTS `department_trainings`;
DROP TABLE IF EXISTS `department_research`;
DROP TABLE IF EXISTS `department_programs`;
DROP TABLE IF EXISTS `department_outcomes`;
DROP TABLE IF EXISTS `department_objectives`;
DROP TABLE IF EXISTS `department_linkages`;
DROP TABLE IF EXISTS `department_facilities`;
DROP TABLE IF EXISTS `department_extensions`;
DROP TABLE IF EXISTS `department_curricula`;
DROP TABLE IF EXISTS `department_awards`;
DROP TABLE IF EXISTS `department_alumni`;
DROP TABLE IF EXISTS `college_retros`;
DROP TABLE IF EXISTS `college_organizations`;
DROP TABLE IF EXISTS `college_memberships`;
DROP TABLE IF EXISTS `institute_staff`;
DROP TABLE IF EXISTS `institute_research`;
DROP TABLE IF EXISTS `institute_goals`;
DROP TABLE IF EXISTS `institute_facilities`;
DROP TABLE IF EXISTS `institute_extensions`;
DROP TABLE IF EXISTS `faculty`;
DROP TABLE IF EXISTS `facilities`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `college_videos`;
DROP TABLE IF EXISTS `college_departments`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `scholarships`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `facebook_configs`;
DROP TABLE IF EXISTS `colleges`;
DROP TABLE IF EXISTS `college_trainings`;
DROP TABLE IF EXISTS `college_testimonials`;
DROP TABLE IF EXISTS `college_sections`;
DROP TABLE IF EXISTS `college_institutes`;
DROP TABLE IF EXISTS `college_faqs`;
DROP TABLE IF EXISTS `college_extensions`;
DROP TABLE IF EXISTS `college_downloads`;
DROP TABLE IF EXISTS `college_contacts`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_links`)),
  PRIMARY KEY (`id`),
  KEY `college_contacts_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_downloads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` bigint(20) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `publish_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_downloads_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `publish_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_extensions_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_faqs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_faqs_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_institutes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `program_description` text DEFAULT NULL,
  `graduate_outcomes` text DEFAULT NULL,
  `graduate_outcomes_title` varchar(255) DEFAULT NULL,
  `graduate_outcomes_image` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `banner_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`banner_images`)),
  `card_image` varchar(255) DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_x` varchar(255) DEFAULT NULL,
  `social_youtube` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_other` varchar(255) DEFAULT NULL,
  `overview_title` varchar(255) DEFAULT NULL,
  `overview_body` text DEFAULT NULL,
  `history` text DEFAULT NULL,
  `awards_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `research_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `extension_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `training_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `facilities_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `alumni_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `programs_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `college_slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_institutes_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) NOT NULL,
  `section_slug` varchar(80) NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `publish_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `college_sections_college_slug_section_slug_unique` (`college_slug`,`section_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `quote` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_testimonials_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_trainings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `is_draft` tinyint(1) NOT NULL DEFAULT 0,
  `publish_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_trainings_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `colleges` (
  `slug` varchar(80) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `about_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`about_images`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `facebook_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `page_id` varchar(255) NOT NULL,
  `access_token` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `fetch_limit` int(11) NOT NULL DEFAULT 5,
  `article_category` varchar(255) DEFAULT NULL,
  `article_author` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facebook_configs_entity_type_entity_id_unique` (`entity_type`,`entity_id`),
  KEY `facebook_configs_entity_type_index` (`entity_type`),
  KEY `facebook_configs_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scholarships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `process` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `added_by` varchar(20) NOT NULL DEFAULT 'admin',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scholarships_college_slug_index` (`college_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `key` varchar(120) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(20) NOT NULL DEFAULT 'editor',
  `college_slug` varchar(80) DEFAULT NULL,
  `department` varchar(120) DEFAULT NULL,
  `organization_id` bigint(20) unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `body` longtext DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `college_slug` varchar(80) DEFAULT NULL,
  `department_name` varchar(180) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `banner_dark` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `announcements_slug_unique` (`slug`),
  KEY `announcements_user_id_foreign` (`user_id`),
  CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `body` longtext DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `banner_dark` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `college_slug` varchar(80) DEFAULT NULL,
  `department_name` varchar(180) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_user_id_foreign` (`user_id`),
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `overview_title` varchar(255) DEFAULT 'Overview',
  `overview_body` longtext DEFAULT NULL,
  `overview_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `faculty_title` varchar(255) DEFAULT NULL,
  `faculty_body` longtext DEFAULT NULL,
  `faculty_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `objectives_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `objectives_title` varchar(255) DEFAULT NULL,
  `objectives_body` text DEFAULT NULL,
  `curriculum_title` varchar(255) DEFAULT NULL,
  `curriculum_body` longtext DEFAULT NULL,
  `linkages_title` varchar(255) DEFAULT NULL,
  `linkages_body` text DEFAULT NULL,
  `linkages_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `organizations_title` varchar(255) DEFAULT NULL,
  `organizations_body` text DEFAULT NULL,
  `organizations_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `programs_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `programs_title` varchar(255) DEFAULT NULL,
  `programs_body` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `program_description` longtext DEFAULT NULL,
  `graduate_outcomes` longtext DEFAULT NULL,
  `graduate_outcomes_title` varchar(255) DEFAULT NULL,
  `graduate_outcomes_image` varchar(255) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `banner_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`banner_images`)),
  `card_image` varchar(255) DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_x` varchar(255) DEFAULT NULL,
  `social_youtube` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_other` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `awards_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `awards_title` varchar(255) DEFAULT NULL,
  `awards_body` text DEFAULT NULL,
  `research_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `research_title` varchar(255) DEFAULT NULL,
  `research_body` text DEFAULT NULL,
  `extension_is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `extension_title` varchar(255) DEFAULT NULL,
  `extension_body` text DEFAULT NULL,
  `training_is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `training_title` varchar(255) DEFAULT NULL,
  `training_body` text DEFAULT NULL,
  `facilities_is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `membership_is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `facilities_title` varchar(255) DEFAULT NULL,
  `facilities_body` text DEFAULT NULL,
  `membership_title` varchar(255) DEFAULT NULL,
  `membership_body` text DEFAULT NULL,
  `alumni_is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `alumni_title` varchar(255) DEFAULT NULL,
  `alumni_body` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `college_departments_college_slug_name_unique` (`college_slug`,`name`),
  KEY `college_departments_college_slug_index` (`college_slug`),
  CONSTRAINT `college_departments_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_videos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `video_type` enum('url','file') NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `video_file` varchar(255) DEFAULT NULL,
  `video_title` varchar(255) DEFAULT NULL,
  `video_description` text DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_videos_college_slug_foreign` (`college_slug`),
  CONSTRAINT `college_videos_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_end_date` date DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `college_slug` varchar(80) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_user_id_foreign` (`user_id`),
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `college_slug` varchar(80) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facilities_slug_unique` (`slug`),
  KEY `facilities_user_id_foreign` (`user_id`),
  CONSTRAINT `facilities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faculty` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `college_slug` varchar(80) DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty_user_id_foreign` (`user_id`),
  KEY `faculty_institute_id_foreign` (`institute_id`),
  CONSTRAINT `faculty_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `faculty_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `institute_extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institute_extensions_institute_id_foreign` (`institute_id`),
  CONSTRAINT `institute_extensions_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `institute_facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institute_facilities_institute_id_foreign` (`institute_id`),
  CONSTRAINT `institute_facilities_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `institute_goals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institute_goals_institute_id_foreign` (`institute_id`),
  CONSTRAINT `institute_goals_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `institute_research` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institute_research_institute_id_foreign` (`institute_id`),
  CONSTRAINT `institute_research_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `institute_staff` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `college_slug` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institute_staff_institute_id_foreign` (`institute_id`),
  CONSTRAINT `institute_staff_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_memberships` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `organization` varchar(255) NOT NULL,
  `membership_type` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_memberships_department_id_foreign` (`department_id`),
  KEY `college_memberships_college_slug_index` (`college_slug`),
  CONSTRAINT `college_memberships_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE,
  CONSTRAINT `college_memberships_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_organizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `acronym` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `logo` varchar(255) DEFAULT NULL,
  `adviser` varchar(255) DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_organizations_department_id_foreign` (`department_id`),
  KEY `college_organizations_college_slug_index` (`college_slug`),
  CONSTRAINT `college_organizations_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE,
  CONSTRAINT `college_organizations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_retros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_size` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stamp` varchar(255) DEFAULT NULL,
  `stamp_size` int(11) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_retros_college_slug_foreign` (`college_slug`),
  KEY `college_retros_department_id_foreign` (`department_id`),
  CONSTRAINT `college_retros_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE,
  CONSTRAINT `college_retros_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_alumni` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(80) DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `year_graduated` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_alumni_college_slug_index` (`college_slug`),
  KEY `department_alumni_department_id_foreign` (`department_id`),
  KEY `department_alumni_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_alumni_college_slug_foreign` FOREIGN KEY (`college_slug`) REFERENCES `colleges` (`slug`) ON DELETE CASCADE,
  CONSTRAINT `department_alumni_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_alumni_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_awards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_awards_department_id_foreign` (`department_id`),
  KEY `department_awards_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_awards_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_awards_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_curricula` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `courses` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_curricula_department_id_foreign` (`department_id`),
  KEY `department_curricula_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_curricula_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_curricula_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_extensions_department_id_foreign` (`department_id`),
  KEY `department_extensions_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_extensions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_extensions_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_facilities_department_id_foreign` (`department_id`),
  KEY `department_facilities_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_facilities_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_facilities_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_linkages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` enum('local','international') NOT NULL DEFAULT 'local',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_linkages_department_id_foreign` (`department_id`),
  CONSTRAINT `department_linkages_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_objectives` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `content` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_objectives_department_id_foreign` (`department_id`),
  KEY `department_objectives_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_objectives_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_objectives_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_outcomes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_outcomes_department_id_foreign` (`department_id`),
  KEY `department_outcomes_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_outcomes_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_outcomes_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `numbered_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`numbered_content`)),
  `numbering` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_programs_department_id_foreign` (`department_id`),
  KEY `department_programs_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_programs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_programs_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_research` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `completed_year` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_research_department_id_foreign` (`department_id`),
  KEY `department_research_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_research_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_research_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `department_trainings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `institute_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_trainings_department_id_foreign` (`department_id`),
  KEY `department_trainings_institute_id_foreign` (`institute_id`),
  CONSTRAINT `department_trainings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `college_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_trainings_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `college_institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `facility_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facility_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facility_images_facility_id_foreign` (`facility_id`),
  CONSTRAINT `facility_images_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `college_accreditations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_slug` varchar(255) NOT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `agency` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL,
  `valid_until` date DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `college_accreditations_program_id_foreign` (`program_id`),
  KEY `college_accreditations_college_slug_index` (`college_slug`),
  CONSTRAINT `college_accreditations_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `department_programs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_02_03_000001_add_is_admin_to_users_table', 1),
('2025_02_03_000002_create_articles_table', 1),
('2025_02_03_100000_add_role_to_users_table', 1),
('2025_02_03_120000_add_college_slug_to_users_table', 1),
('2025_02_03_140000_create_announcements_table', 1),
('2025_02_03_140001_create_events_table', 1),
('2025_02_03_140002_create_faculty_table', 1),
('2025_02_03_160000_create_college_sections_table', 1),
('2025_02_03_170000_create_colleges_table', 1),
('2025_02_03_180000_create_settings_table', 1),
('2025_02_03_190000_add_college_slug_to_articles_table', 1),
('2025_02_04_000001_add_department_to_users_table', 1),
('2026_02_04_084247_add_icon_to_colleges_table', 1),
('2026_02_05_064845_add_meta_column_to_college_sections_table', 1),
('2026_02_05_064911_migrate_departments_to_meta_column', 1),
('2026_02_09_152900_migrate_departments_to_meta_column', 1),
('2026_02_09_153300_create_college_departments_table', 1),
('2026_02_09_153301_migrate_existing_departments_to_table', 1),
('2026_02_10_000001_create_facilities_table', 1),
('2026_02_10_062307_add_department_name_to_facilities_table', 1),
('2026_02_11_012532_create_college_videos_table', 1),
('2026_02_11_024546_add_is_visible_to_college_videos_table', 1),
('2026_02_11_054943_create_college_faqs_table', 1),
('2026_02_11_063037_add_is_visible_to_college_faqs_table', 1),
('2026_02_11_065808_add_is_visible_to_college_sections_table', 1),
('2026_02_11_141513_create_college_contacts_table', 1),
('2026_02_11_143607_modify_college_contacts_table_for_custom_links', 1),
('2026_02_11_154541_create_college_institutes_table', 1),
('2026_02_12_100500_add_slug_to_facilities_table', 2),
('2026_02_12_022147_create_facility_images_table', 3),
('2026_02_12_160000_create_college_retros_table', 4),
('2026_02_12_154552_add_contact_info_to_college_departments_table', 5),
('2026_02_13_063246_add_overview_columns_to_college_departments_table', 6),
('2026_02_13_063422_add_overview_title_and_body_to_college_departments_table', 7),
('2026_02_13_115613_add_graduate_outcomes_title_to_college_departments_table', 8),
('2026_02_13_120605_create_department_outcomes_table', 9),
('2026_02_13_151108_create_department_objectives_table', 10),
('2026_02_14_160230_create_department_sections_tables', 11),
('2026_02_14_161357_migrate_curriculum_and_drop_sections_column', 12),
('2026_02_16_102511_create_department_programs_table', 13),
('2026_02_16_032330_add_year_graduated_to_department_alumni_table', 14),
('2026_02_16_143000_add_details_to_college_institutes_table', 15),
('2026_02_16_144000_add_institute_id_to_related_tables', 16),
('2026_02_18_090000_add_history_and_institute_id', 17),
('2026_02_18_010429_create_institute_data_tables', 18),
('2026_02_18_160508_add_numbering_to_department_programs_table', 19),
('2026_02_18_161014_add_numbered_items_to_department_programs_table', 20),
('2026_02_19_011219_add_images_to_articles_table', 21),
('2026_02_23_120000_add_draft_and_publish_at_to_college_sections_table', 22),
('2026_02_28_220000_create_scholarships_table', 23),
('2026_03_02_021021_create_college_testimonials_table', 24),
('2026_03_02_021022_create_college_accreditations_table', 24),
('2026_03_02_025027_add_logo_to_college_accreditations_table', 25),
('2026_03_02_044739_create_college_memberships_table', 26),
('2026_03_02_074306_create_college_organizations_table', 27),
('2026_03_04_072202_change_curriculum_courses_to_text', 28),
('2026_03_04_144926_add_image_to_announcements_table', 29),
('2026_03_06_155325_add_sections_to_college_organizations_table', 30),
('2026_03_10_142038_add_linkages_columns_to_college_departments_table', 31),
('2026_03_10_142817_create_department_linkages_table', 32),
('2026_03_11_023258_add_facilities_details_to_college_departments_table', 33),
('2026_03_11_051212_add_alumni_details_to_college_departments_table', 34),
('2026_03_13_183242_add_title_size_to_college_retros_table', 35),
('2026_03_13_185208_add_stamp_size_to_college_retros_table', 36),
('2026_03_14_131820_create_college_extensions_table', 37),
('2026_03_14_131824_create_college_trainings_table', 37),
('2026_03_17_024820_add_organization_id_to_users_table', 38),
('2026_03_17_000000_add_about_image_to_colleges_table', 39),
('2026_03_17_000001_update_about_images_to_multiple', 40),
('2026_03_17_000002_create_facebook_configs_table', 41),
('2026_03_22_000000_create_college_downloads_table', 42),
('2026_03_23_120000_add_department_id_to_college_retros_table', 43),
('2026_03_24_000000_create_default_superadmin_user', 44),
('2026_03_24_010000_add_organizations_details_to_college_departments_table', 44),
('2026_03_24_144523_add_awards_details_to_college_departments_table', 45),
('2026_03_24_152756_add_completed_year_to_department_research_table', 46),
('2026_03_25_000001_add_research_details_to_college_departments_table', 47),
('2026_03_25_000002_add_extension_details_to_college_departments_table', 48),
('2026_03_25_000003_add_training_details_to_college_departments_table', 49),
('2026_03_25_000004_add_objectives_details_to_college_departments_table', 50),
('2026_03_25_000005_make_institute_staff_assignment_optional', 51),
('2026_03_26_000006_add_membership_details_to_college_departments_table', 52),
('2026_03_26_083500_add_author_to_announcements_table', 53),
('2026_03_26_101500_add_objectives_visibility_to_college_departments_table', 54),
('2026_03_26_103000_add_programs_section_fields_to_college_departments_table', 55),
('2026_03_26_104500_add_organizations_visibility_to_college_departments_table', 56),
('2026_03_26_110500_add_faculty_details_to_college_departments_table', 57),
('2026_03_29_000001_add_overview_is_visible_to_college_departments_table', 58),
('2026_03_29_000002_add_curriculum_fields_to_college_departments_table', 59),
('2026_03_29_000003_add_department_name_to_articles_table', 60),
('2026_03_29_000004_add_department_name_to_announcements_table', 61),
('2026_03_29_000001_add_department_visibility_curriculum_and_names', 62);

INSERT INTO `colleges` (`slug`, `name`, `icon`, `about_images`, `created_at`, `updated_at`) VALUES
('agriculture', 'College of Agriculture', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('arts-and-social-sciences', 'College of Arts and Social Sciences', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('business-and-accountancy', 'College of Business and Accountancy', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('education', 'College of Education', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('engineering', 'College of Engineering', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('fisheries', 'College of Fisheries', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('home-science-and-industry', 'College of Home Science and Industry', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('veterinary-science-and-medicine', 'College of Veterinary Science and Medicine', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('science', 'College of Science', NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `updated_at` = VALUES(`updated_at`);

INSERT INTO `users` (`name`, `email`, `is_admin`, `role`, `college_slug`, `department`, `organization_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`)
SELECT 'CLSU admin', 'adminCLSU@clsu.edu', 1, 'superadmin', NULL, NULL, NULL, CURRENT_TIMESTAMP, '$2y$10$kmMkBB1Wf6dtXcm5nF9JfeQSYXpKGHCVl0/1kNCQx3f5IBBanXn5q', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
WHERE NOT EXISTS (
    SELECT 1 FROM `users` WHERE `email` = 'adminCLSU@clsu.edu'
);

SET FOREIGN_KEY_CHECKS=1;
