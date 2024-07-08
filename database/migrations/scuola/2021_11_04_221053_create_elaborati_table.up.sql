
CREATE TABLE `elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `anno_scolastico`            varchar(128) NOT NULL,
    `titolo`            varchar(128) NOT NULL,
    `classi`            varchar(128) DEFAULT NULL  COMMENT 'Indicare le classi separate da virgole.',

    -- media info
    `file_path`         varchar(256) DEFAULT NULL,
    `file_mime_type`    varchar(64) DEFAULT NULL,
    `file_size`         bigint DEFAULT NULL,
    `file_hash`         varchar(64) NOT NULL UNIQUE,

    -- other
    `collocazione`      varchar(128) DEFAULT NULL,
    -- `formato`           varchar(32) DEFAULT NULL COMMENT 'Formato del libro in cm. 24x43',
    `sommario`           TEXT DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `elaborati_studenti`
(
    `elaborato_id`      int(10) NOT NULL,
    `studente_id`       int(10) NOT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

ALTER TABLE `elaborati_studenti` ADD PRIMARY KEY (`elaborato_id`,`studente_id`);

ALTER TABLE `elaborati_studenti`
    ADD FOREIGN KEY (`studente_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
