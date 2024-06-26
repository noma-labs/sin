
CREATE TABLE `elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `anno_scolastico`            varchar(128) NOT NULL,
    `titolo`            varchar(128) NOT NULL,
    `classe`            varchar(128) DEFAULT NULL  COMMENT 'Indicare le classi separate da virgole.',

    `collocazione`      varchar(128) DEFAULT NULL,
    -- `formato`           varchar(32) DEFAULT NULL COMMENT 'Formato del libro in cm. 24x43',
    `sommario`           TEXT DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `elaborati_alunni`
(
    `elaborato_id`      int(10) NOT NULL,
    `alunno_id`         int(10) NOT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `elaborati_media`
(
    `elaborato_id`      int(10) NOT NULL,
    `file_name`         varchar(256) NOT NULL,
    `mime_type`         varchar(16) NOT NULL,
    `size`              bigint NOT NULL,
    `file_hash`         varchar(64) DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
