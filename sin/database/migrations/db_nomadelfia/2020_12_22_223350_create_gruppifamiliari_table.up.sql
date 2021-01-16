--
-- Struttura della tabella `gruppi_familiari`
--

CREATE TABLE `gruppi_familiari` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Id Gruppo Familiare',
  `nome` varchar(30) NOT NULL COMMENT 'Nome del Gruppo familiare',
  `descrizione` varchar(200) DEFAULT NULL,
  `borgata` varchar(200) NULL COMMENT 'Borgata',
  `ubicazione` varchar(500) NULL COMMENT 'Ubicazione Gruppo',
  `data_creazione` date COMMENT 'Data Nascita Gruppo',
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Struttura della tabella `gruppi_familiari_capogruppi`
--

CREATE TABLE `gruppi_familiari_capogruppi` (
  `gruppo_familiare_id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `data_inizio_incarico` date NOT NULL,
  `data_fine_incarico` date DEFAULT NULL,
  `note` varchar(100) NOT NULL,
  `stato` tinyint(1) NOT NULL,
  PRIMARY KEY (`gruppo_familiare_id`,`persona_id`,`data_inizio_incarico`),
  UNIQUE KEY `gruppo_familiare_id` (`gruppo_familiare_id`,`persona_id`,`data_inizio_incarico`,`data_fine_incarico`,`stato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mantiene lo storico dei capogruppi';

--
-- RELAZIONI PER TABELLA `gruppi_familiari_capogruppi`:
--   `gruppo_familiare_id`
--       `gruppi_familiari` -> `id`
--   `persona_id`
--       `persone` -> `id`
--
ALTER TABLE `gruppi_familiari_capogruppi`
  ADD CONSTRAINT `gruppi_familiari_capogruppi_ibfk_1` FOREIGN KEY (`gruppo_familiare_id`) REFERENCES `gruppi_familiari` (`id`),
  ADD CONSTRAINT `gruppi_familiari_capogruppi_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);
--
-- Struttura della tabella `gruppi_persone`
--

CREATE TABLE `gruppi_persone` (
  `gruppo_famigliare_id` int(10) NOT NULL,
  `persona_id` int(10) NOT NULL,
  `stato` enum('0','1') NOT NULL,
  `data_entrata_gruppo` date NOT NULL,
  `data_uscita_gruppo` date DEFAULT NULL,
  PRIMARY KEY (`gruppo_famigliare_id`,`persona_id`,`stato`,`data_entrata_gruppo`) USING BTREE,
  KEY `persona_id` (`persona_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- RELAZIONI PER TABELLA `gruppi_persone`:
--   `gruppo_famigliare_id`
--       `gruppi_familiari` -> `id`
--   `persona_id`
--       `persone` -> `id`
--

ALTER TABLE `gruppi_persone`
  ADD CONSTRAINT `gruppi_persone_ibfk_1` FOREIGN KEY (`gruppo_famigliare_id`) REFERENCES `gruppi_familiari` (`id`),
  ADD CONSTRAINT `gruppi_persone_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);