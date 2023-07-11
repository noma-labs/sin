CREATE TABLE `photos`
(
    `uid`                 varchar(255) NOT NULL,
    `sha`                 varchar(255) NOT NULL,
    `source_file`         varchar(255) NOT NULL,
    `directory`           varchar(255)          DEFAULT NULL,
    `folder_title`        varchar(255)          DEFAULT NULL COMMENT 'Parent folder name of the photo',
    `file_size`           bigint(20)            DEFAULT NULL,
    `file_name`           varchar(255)          DEFAULT NULL,
    `file_type`           varchar(16)           DEFAULT NULL,
    `file_type_extension` varchar(16)           DEFAULT NULL,
    `image_height`        int(11)               DEFAULT NULL,
    `image_width`         int(11)               DEFAULT NULL,
    `keywords`            text                  DEFAULT NULL,
    `region_info`         JSON                  DEFAULT NULL,
    `subject`             text                  DEFAULT NULL,
    `taken_at`            datetime              DEFAULT NULL COMMENT 'the create date of the photo set by the camera',
    `created_at`          datetime(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `updated_at`          datetime(6)  NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


-- CREATE TABLE `photos_albums` (
--  `photo_uid` varbinary(42) NOT NULL,
--  `album_uid` varbinary(42) NOT NULL,
--  `created_at` datetime DEFAULT NULL,
--  `updated_at` datetime DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
--
-- ALTER TABLE `photos_albums`
--     ADD PRIMARY KEY (`photo_uid`,`album_uid`),
--   ADD KEY `idx_photos_albums_album_uid` (`album_uid`);

#
# CREATE VIEW v_directory AS SELECT directory, count(*)
#                            FROM `photos`
#                            group by directory;

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


-- SELECT data, datnum, argomento, nfo
-- FROM `foto_enrico`
-- WHERE datnum like '%ZZZAY%'
-- ORDER BY `data` , datnum DESC;