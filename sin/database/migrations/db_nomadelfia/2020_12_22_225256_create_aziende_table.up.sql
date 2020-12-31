-- Run the migrations


--
-- Struttura della tabella `aziende`
--

CREATE TABLE `aziende` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT'Id Azienda',
  `nome_azienda` varchar(50) NOT NULL COMMENT 'nome',
  `descrizione_azienda` varchar(200) DEFAULT NULL COMMENT 'Descrizione',
  `data_azienda` date NOT NULL COMMENT 'Data creazione azienda',
  `tipo` enum('ufficio','azienda','magazzino') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Struttura della tabella `aziende_persone`
--

CREATE TABLE `aziende_persone` (
  `azienda_id` int(10) NOT NULL,
  `persona_id` int(10) NOT NULL,
  `stato` enum('Attivo','Non Attivo','Sospeso') DEFAULT 'Attivo',
  `data_inizio_azienda` date NOT NULL,
  `data_fine_azienda` date DEFAULT NULL,
  `mansione` enum('RESPONSABILE AZIENDA','LAVORATORE') NOT NULL,
  PRIMARY KEY (`azienda_id`,`persona_id`,`data_inizio_azienda`) USING BTREE,
  KEY `persona_id` (`persona_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Limiti per la tabella `aziende_persone`
--
ALTER TABLE `aziende_persone`
  ADD CONSTRAINT `aziende_persone_ibfk_1` FOREIGN KEY (`azienda_id`) REFERENCES `aziende` (`id`),
  ADD CONSTRAINT `aziende_persone_ibfk_2` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);