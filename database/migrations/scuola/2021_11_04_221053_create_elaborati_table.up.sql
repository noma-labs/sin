
CREATE TABLE `elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `anno_scolastico`            varchar(128) NOT NULL,
    `titolo`            varchar(128) NOT NULL,
    `classi`            varchar(128) DEFAULT NULL  COMMENT 'Indicare le classi separate da virgole.',

    -- media info
    `file_path`         varchar(256) DEFAULT NULL,
    `file_mime_type`    varchar(16) DEFAULT NULL,
    `file_size`         bigint DEFAULT NULL,
    `file_hash`         varchar(64) NOT NULL UNIQUE,

    -- other
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
