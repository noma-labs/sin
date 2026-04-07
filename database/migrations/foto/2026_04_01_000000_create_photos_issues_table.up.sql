CREATE TABLE `photos_issues` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `photo_id` bigint(20) UNSIGNED NOT NULL,
    `persona_id` int(10) UNSIGNED DEFAULT NULL,
    `photo_persona_name` varchar(255) DEFAULT NULL COMMENT 'Original persona name from photos_people if different from persone.nome',
    `issue_type` varchar(50) NOT NULL COMMENT 'not_yet_born | already_deceased',
   `note` text DEFAULT NULL
    `resolved_at` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_photo_persona_issue` (`photo_id`, `persona_id`, `issue_type`),
    CONSTRAINT `fk_photos_issues_photo_id` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
