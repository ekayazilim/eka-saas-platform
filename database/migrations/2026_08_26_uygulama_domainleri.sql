CREATE TABLE `uygulama_domainleri` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` bigint(20) UNSIGNED NOT NULL,
    `uygulama_id` bigint(20) UNSIGNED NOT NULL,
    `host` varchar(255) NOT NULL,
    `port` int(11) NOT NULL DEFAULT 3000,
    `https` tinyint(1) NOT NULL DEFAULT 1,
    `certificate_type` enum('letsencrypt','none','custom') NOT NULL DEFAULT 'letsencrypt',
    `dokploy_domain_id` varchar(191) NOT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_uygulama_domain_host` (`host`),
    UNIQUE KEY `uq_uygulama_domain_dokploy` (`dokploy_domain_id`),
    KEY `idx_uygulama_domain_tenant` (`tenant_id`),
    KEY `idx_uygulama_domain_uygulama` (`uygulama_id`),
    CONSTRAINT `fk_uygulama_domain_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_uygulama_domain_uygulama` FOREIGN KEY (`uygulama_id`) REFERENCES `uygulamalar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;