CREATE TABLE `audio_transcripts` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` varchar(255) NOT NULL UNIQUE,
    `title` text DEFAULT NULL,
    `description` text DEFAULT NULL,
    `recorded_date` date DEFAULT NULL,
    `file_path` varchar(500) NOT NULL,
    `content` longtext DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT KEY `ft_audio_transcripts_content` (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
