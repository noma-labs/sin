
CREATE TABLE `incarichi` (
    `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nome` varchar(50) NOT NULL,
    `descrizione` varchar(200) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `incarichi_persone` (
    `incarico_id` int(10) NOT NULL,
    `persona_id` int(10) NOT NULL,
    `data_inizio` date NOT NULL,
    `data_fine` date DEFAULT NULL,
    `note` varchar(200) DEFAULT NULL,
    PRIMARY KEY (`incarico_id`,`persona_id`,`data_inizio`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `incarichi_persone`
    ADD CONSTRAINT `incarichi_persone_ibfk_1` FOREIGN KEY (`incarico_id`) REFERENCES `incarichi` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `incarichi_persone_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`) ON DELETE RESTRICT;

-- Copy the incarichi from the table aziende
INSERT INTO incarichi (id, nome, descrizione, created_at, updated_at)
SELECT id, nome_azienda, descrizione_azienda, created_at, updated_at FROM `aziende`
where tipo = 'incarico'
ORDER BY `aziende`.`tipo`  DESC;

-- move the incarichi persone
INSERT INTO incarichi_persone (incarico_id, persona_id, data_inizio, data_fine)
SELECT azienda_id, persona_id, data_inizio_azienda, data_fine_azienda
FROM `aziende_persone`
where azienda_id IN (SELECT id from incarichi);

-- clean aziende table and aziende persone
DELETE FROM aziende WHERE tipo = 'incarico';
DELETE FROM aziende_persone where azienda_id IN (SELECT id from incarichi);