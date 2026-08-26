ALTER TABLE `plans`
    ADD COLUMN `application_limit` int(11) NOT NULL DEFAULT 1 AFTER `project_limit`,
    ADD COLUMN `domain_limit` int(11) NOT NULL DEFAULT 1 AFTER `application_limit`,
    ADD COLUMN `database_limit` int(11) NOT NULL DEFAULT 0 AFTER `domain_limit`,
    ADD COLUMN `memory_limit_mb` int(11) NOT NULL DEFAULT 512 AFTER `database_limit`,
    ADD COLUMN `cpu_limit_millicores` int(11) NOT NULL DEFAULT 500 AFTER `memory_limit_mb`,
    ADD COLUMN `storage_limit_mb` int(11) NOT NULL DEFAULT 5120 AFTER `cpu_limit_millicores`,
    ADD COLUMN `allow_docker` tinyint(1) NOT NULL DEFAULT 0 AFTER `storage_limit_mb`,
    ADD COLUMN `allow_databases` tinyint(1) NOT NULL DEFAULT 0 AFTER `allow_docker`,
    ADD COLUMN `allow_custom_domain` tinyint(1) NOT NULL DEFAULT 1 AFTER `allow_databases`;

ALTER TABLE `projects`
    ADD COLUMN `dokploy_project_id` varchar(191) DEFAULT NULL AFTER `status`,
    ADD COLUMN `dokploy_environment_id` varchar(191) DEFAULT NULL AFTER `dokploy_project_id`,
    ADD COLUMN `provision_status` enum('pending','ready','error','suspended') NOT NULL DEFAULT 'pending' AFTER `dokploy_environment_id`,
    ADD COLUMN `provision_error` text DEFAULT NULL AFTER `provision_status`,
    ADD COLUMN `last_sync_at` timestamp NULL DEFAULT NULL AFTER `provision_error`,
    ADD UNIQUE KEY `uq_projects_dokploy_project_id` (`dokploy_project_id`),
    ADD KEY `idx_projects_tenant_provision` (`tenant_id`,`provision_status`);

CREATE TABLE `uygulamalar` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` bigint(20) UNSIGNED NOT NULL,
    `project_id` bigint(20) UNSIGNED NOT NULL,
    `ad` varchar(191) NOT NULL,
    `uygulama_adi` varchar(191) DEFAULT NULL,
    `platform` enum('react','nextjs','node','python','docker','static') NOT NULL DEFAULT 'react',
    `kaynak_tipi` enum('git','github','docker') NOT NULL DEFAULT 'git',
    `git_url` text DEFAULT NULL,
    `git_sahip` varchar(191) DEFAULT NULL,
    `git_repo` varchar(191) DEFAULT NULL,
    `git_dal` varchar(191) NOT NULL DEFAULT 'main',
    `git_build_yolu` varchar(191) NOT NULL DEFAULT '/',
    `github_id` varchar(191) DEFAULT NULL,
    `docker_image` varchar(255) DEFAULT NULL,
    `dokploy_application_id` varchar(191) DEFAULT NULL,
    `durum` enum('pending','ready','deploying','running','error','suspended') NOT NULL DEFAULT 'pending',
    `son_hata` text DEFAULT NULL,
    `son_deploy_at` timestamp NULL DEFAULT NULL,
    `last_sync_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_uygulamalar_dokploy_application_id` (`dokploy_application_id`),
    KEY `idx_uygulamalar_tenant` (`tenant_id`),
    KEY `idx_uygulamalar_project` (`project_id`),
    KEY `idx_uygulamalar_tenant_durum` (`tenant_id`,`durum`),
    CONSTRAINT `fk_uygulamalar_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_uygulamalar_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dagitim_kayitlari` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` bigint(20) UNSIGNED NOT NULL,
    `uygulama_id` bigint(20) UNSIGNED NOT NULL,
    `dokploy_deployment_id` varchar(191) DEFAULT NULL,
    `islem` enum('deploy','redeploy','stop','start') NOT NULL DEFAULT 'deploy',
    `durum` enum('queued','running','success','error') NOT NULL DEFAULT 'queued',
    `mesaj` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_dagitim_tenant` (`tenant_id`),
    KEY `idx_dagitim_uygulama` (`uygulama_id`),
    CONSTRAINT `fk_dagitim_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_dagitim_uygulama` FOREIGN KEY (`uygulama_id`) REFERENCES `uygulamalar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

UPDATE `plans` SET `application_limit` = 1, `domain_limit` = 1, `database_limit` = 0, `memory_limit_mb` = 512, `cpu_limit_millicores` = 500, `storage_limit_mb` = 5120, `allow_docker` = 0, `allow_databases` = 0, `allow_custom_domain` = 1 WHERE `slug` = 'free';
UPDATE `plans` SET `application_limit` = 10, `domain_limit` = 20, `database_limit` = 10, `memory_limit_mb` = 4096, `cpu_limit_millicores` = 2000, `storage_limit_mb` = 20480, `allow_docker` = 1, `allow_databases` = 1, `allow_custom_domain` = 1 WHERE `slug` = 'pro';
UPDATE `plans` SET `application_limit` = 50, `domain_limit` = 100, `database_limit` = 50, `memory_limit_mb` = 16384, `cpu_limit_millicores` = 8000, `storage_limit_mb` = 102400, `allow_docker` = 1, `allow_databases` = 1, `allow_custom_domain` = 1 WHERE `slug` = 'business';