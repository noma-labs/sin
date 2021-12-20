
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

--
ALTER TABLE `incarichi_persone`
    ADD CONSTRAINT `incarichi_persone_ibfk_1` FOREIGN KEY (`incarico_id`) REFERENCES `incarichi` (`id`),
    ADD CONSTRAINT `incarichi_persone_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);