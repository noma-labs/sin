CREATE TABLE `photos` (
      `sha` varchar(255) NOT NULL,
      `source_file` varchar(255) NOT NULL,
      `directory` varchar(255) DEFAULT NULL,
      `file_size` bigint(20)  DEFAULT NULL,
      `file_type` varchar(16) DEFAULT NULL,
      `file_type_extension` varchar(16) DEFAULT NULL,
      `image_height` int(11)  DEFAULT NULL,
      `image_width` int(11)  DEFAULT NULL,
      `keywords`  text DEFAULT NULL,
      `region_info` JSON DEFAULT NULL,
      `subject` text DEFAULT NULL,
      `taken_at` datetime NOT NULL COMMENT 'the create date of the photo set by the camera',
      `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(),
      `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
