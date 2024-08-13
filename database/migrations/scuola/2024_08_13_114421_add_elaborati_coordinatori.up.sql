
CREATE TABLE `elaborati_coordinatori`
(
    `elaborato_id`      int(10) NOT NULL,
    `coordinatore_id`       int(10) NOT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

ALTER TABLE `elaborati_coordinatori` ADD PRIMARY KEY (`elaborato_id`,`coordinatore_id`);

ALTER TABLE `elaborati_coordinatori` ADD FOREIGN KEY (`coordinatore_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `elaborati_coordinatori` ADD FOREIGN KEY (`elaborato_id`) REFERENCES `db_scuola`.`elaborati` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
