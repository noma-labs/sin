CREATE TABLE `photos` (
      `sha` varchar(255) NOT NULL,
      `source_file` varchar(255) NOT NULL,
      `directory` varchar(255) DEFAULT NULL,
      `file_size` bigint(20)  DEFAULT NULL,
      `file_name` varchar(255)  DEFAULT NULL,
      `file_type` varchar(16) DEFAULT NULL,
      `file_type_extension` varchar(16) DEFAULT NULL,
      `image_height` int(11)  DEFAULT NULL,
      `image_width` int(11)  DEFAULT NULL,
      `keywords`  text DEFAULT NULL,
      `region_info` JSON DEFAULT NULL,
      `subject` text DEFAULT NULL,
      `taken_at` datetime DEFAULT NULL COMMENT 'the create date of the photo set by the camera',
      `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(),
      `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for table `photos`
--
-- ALTER TABLE `photos`
--     ADD UNIQUE KEY `photo_idx` (`sha`);
-- COMMIT;

-- find duplicate foto by sha
-- with dup AS (
-- SELECT sha, count(*) as c
-- FROM `photos`
-- group by sha
-- having c > 1
--     ) SELECT directory, file_name, subject
--       from photos
--       where sha IN (SELECT sha from dup)
--       order by sha, directory;


-- with num As(
--     SELECT data, datnum, argomento
--     FROM `foto_enrico`
--     WHERE `data` > '2022-01-01' and data <= '2022-12-31'
--     ORDER BY `data` , datnum DESC
-- ) select * from num order by data;