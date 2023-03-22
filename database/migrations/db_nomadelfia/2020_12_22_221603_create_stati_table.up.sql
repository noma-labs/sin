--
-- Struttura della tabella `stati`
--

CREATE TABLE `stati` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `stato` varchar(3) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(200) DEFAULT NULL,
  UNIQUE KEY `stato` (`stato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Struttura della tabella `persone_stati`
--

CREATE TABLE `persone_stati` (
  `persona_id` int(11) NOT NULL,
  `stato_id` int(11) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date DEFAULT NULL,
  `stato` enum('0','1') NOT NULL,
  PRIMARY KEY (`persona_id`,`stato_id`,`data_inizio`) USING BTREE,
  UNIQUE KEY `persona_id` (`persona_id`,`stato_id`,`stato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELAZIONI PER TABELLA `persone_stati`:
--   `persona_id`
--       `persone` -> `id`
--   `stato_id`
--       `stati` -> `id`
--

ALTER TABLE `persone_stati`
  ADD CONSTRAINT `persone_stati_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
  ADD CONSTRAINT `persone_stati_ibfk_2` FOREIGN KEY (`stato_id`) REFERENCES `stati` (`id`);