
CREATE TABLE `elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `collocazione`      varchar(6) DEFAULT NULL COMMENT 'Collocazione nel formato ABC123',
    `anno_scolastico`   varchar(9) DEFAULT NULL COMMENT 'Anno scolastico di riferimento nel formato YYYY/YYYY',
    `titolo`            varchar(128) DEFAULT NULL,
    `classi`            varchar(128) DEFAULT NULL COMMENT 'Classi separate da virgole oppure lavoro personale.',

    -- media info
    `file_path`         varchar(256) DEFAULT NULL,
    `file_mime_type`    varchar(64) DEFAULT NULL,
    `file_size`         bigint DEFAULT NULL,
    `file_hash`         varchar(64) DEFAULT NULL UNIQUE,
    `cover_path`        varchar(256) DEFAULT NULL, -- path to cover image in png format

    -- other
    `dimensione`           varchar(8) DEFAULT NULL COMMENT 'Formato del libro (larghazza X Altezza) in cm. 24x43',
    `rilegatura`        varchar(32) DEFAULT NULL COMMENT 'Rilegatura del libro',
    `note`              TEXT DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL,

    -- Needed only to import additional data from libro table, delete after import
    `libro_id`         int(10) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE `elaborati_studenti`
(
    `elaborato_id`      int(10) NOT NULL,
    `studente_id`       int(10) NOT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

ALTER TABLE `elaborati_studenti` ADD PRIMARY KEY (`elaborato_id`,`studente_id`);

ALTER TABLE `elaborati_studenti` ADD FOREIGN KEY (`studente_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `elaborati_studenti` ADD FOREIGN KEY (`elaborato_id`) REFERENCES `db_scuola`.`elaborati` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
