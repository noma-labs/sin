--
-- Struttura della tabella `posizioni`
--

CREATE TABLE `posizioni` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `abbreviato` varchar(4) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(200) DEFAULT NULL,
  `ordinamento` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Struttura della tabella `persone_posizioni`
--

CREATE TABLE `persone_posizioni` (
  `persona_id` int(10) NOT NULL COMMENT 'Id Persone',
  `posizione_id` int(10) NOT NULL COMMENT 'Id Posizione',
  `data_inizio` date NOT NULL COMMENT 'inizio posizione',
  `data_fine` date DEFAULT NULL COMMENT 'fine posizione',
  `stato` enum('0','1') NOT NULL,
  PRIMARY KEY (`persona_id`,`posizione_id`,`data_inizio`) USING BTREE,
  KEY `posizione_id` (`posizione_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- RELAZIONI PER TABELLA `persone_posizioni`:
--   `persona_id`
--       `persone` -> `id`
--   `posizione_id`
--       `posizioni` -> `id`
--

ALTER TABLE `persone_posizioni`
  ADD CONSTRAINT `persone_posizioni_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
  ADD CONSTRAINT `persone_posizioni_ibfk_2` FOREIGN KEY (`posizione_id`) REFERENCES `posizioni` (`id`);