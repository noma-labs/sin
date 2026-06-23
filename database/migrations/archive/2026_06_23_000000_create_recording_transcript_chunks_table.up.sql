CREATE TABLE `recording_transcript_chunks` (
    `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `recording_transcript_id` int(10) NOT NULL,
    `chunk_index` int(10) NOT NULL,
    `content` longtext NOT NULL,
    `embedding` JSON DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_recording_transcript_chunks_transcript_id` FOREIGN KEY (`recording_transcript_id`) REFERENCES `recording_transcripts` (`id`) ON DELETE CASCADE,
    KEY `idx_recording_transcript_id_chunk_index` (`recording_transcript_id`, `chunk_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
