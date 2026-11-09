-- Run the migrations

ALTER TABLE `elaborati`
    ADD COLUMN `tipo` enum('lettera','tema','disegno','compito','poesia','fascicolo','ricerca','diario') NULL DEFAULT NULL COMMENT 'Tipo di elaborato' AFTER `titolo`;

-- The argomenti of an elaborato, each with its materia (optional): an elaborato of a classe can have argomenti of several materie.
CREATE TABLE `elaborati_argomenti`
(
    `id`           int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `elaborato_id` int(10) NOT NULL,
    `argomento`    varchar(128) NOT NULL,
    `materia`      varchar(64) NULL DEFAULT NULL COMMENT 'Materia scolastica dell''argomento',
    `created_at`   timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   timestamp NULL DEFAULT NULL,
    UNIQUE KEY `elaborati_argomenti_unico` (`elaborato_id`, `argomento`, `materia`),
    FOREIGN KEY (`elaborato_id`) REFERENCES `db_scuola`.`elaborati` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
