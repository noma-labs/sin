CREATE TABLE `recording_audio` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `recording_id` int(10) DEFAULT NULL,
  `code` varchar(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(1000) NOT NULL,
  `file_size_mb` decimal(10, 2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_recording_audio_recording_id` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
