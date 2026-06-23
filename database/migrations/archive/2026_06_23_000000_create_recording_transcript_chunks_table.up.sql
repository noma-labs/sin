CREATE TABLE `recording_transcript_chunks` (
    `recording_transcript_id` int(10) NOT NULL,
    `chunk_index` int(10) NOT NULL,
    `content` longtext NOT NULL,
    `embedding` JSON DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`recording_transcript_id`, `chunk_index`),
    CONSTRAINT `fk_recording_transcript_chunks_transcript_id` FOREIGN KEY (`recording_transcript_id`) REFERENCES `recording_transcripts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
