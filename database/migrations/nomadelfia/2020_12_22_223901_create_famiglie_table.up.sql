--
-- Struttura della tabella `famiglie`
--

CREATE TABLE `famiglie` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome_famiglia` varchar(100) NOT NULL,
  `data_creazione` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`,`nome_famiglia`) USING BTREE,
  UNIQUE KEY `famiglia` (`nome_famiglia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `famiglie_posizioni`
--

CREATE TABLE `famiglie_posizioni` (
  `famiglia_id` int(10) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date DEFAULT NULL,
  `stato` enum('0','1') NOT NULL,
  `note` varchar(200) NOT NULL,
  PRIMARY KEY (`famiglia_id`,`data_inizio`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Limiti per la tabella `famiglie_posizioni`
--
ALTER TABLE `famiglie_posizioni`
  ADD CONSTRAINT `famiglie_posizioni_ibfk_1` FOREIGN KEY (`famiglia_id`) REFERENCES `famiglie` (`id`);
 

--
-- RELAZIONI PER TABELLA `famiglie_persone`:
--   `famiglia_id`
--       `famiglie` -> `id`
--   `persona_id`
--       `persone` -> `id`
--

CREATE TABLE `famiglie_persone` (
  `famiglia_id` int(10) NOT NULL,
  `persona_id` int(10) NOT NULL,
  `data_entrata` date DEFAULT NULL,
  `data_uscita` date DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `posizione_famiglia` enum('CAPO FAMIGLIA','MOGLIE','FIGLIO NATO','FIGLIO ACCOLTO','SINGLE') NOT NULL,
  `stato` enum('0','1') NOT NULL,
   PRIMARY KEY (`famiglia_id`,`persona_id`),
   UNIQUE KEY `famiglia_id` (`famiglia_id`,`persona_id`,`posizione_famiglia`,`stato`) USING BTREE,
   KEY `persona_id` (`persona_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Limiti per la tabella `famiglie_persone`
--
ALTER TABLE `famiglie_persone`
  ADD CONSTRAINT `famiglie_persone_ibfk_1` FOREIGN KEY (`famiglia_id`) REFERENCES `famiglie` (`id`),
  ADD CONSTRAINT `famiglie_persone_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);