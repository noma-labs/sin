-- Struttura della tabella `persone`

CREATE TABLE `persone` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nominativo` varchar(100) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `cognome` varchar(30) NOT NULL,
  `id_arch_pietro` int(10) NOT NULL,
  `id_arch_enrico` int(10) NOT NULL,
  `provincia_nascita` varchar(30) DEFAULT NULL,
  `data_nascita` date NOT NULL,
  `sesso` enum('F','M') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
--   `stato` enum('0','1') NOT NULL DEFAULT '1',
  `dispense` enum('0','1') DEFAULT NULL,
  `guardia` enum('0','1') DEFAULT NULL,
  `sigla_biancheria` varchar(30) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `data_decesso` date DEFAULT NULL,
   PRIMARY KEY (`id`,`nominativo`) USING BTREE,
   UNIQUE KEY `nominativo` (`nominativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle `persone`
--
--ALTER TABLE `persone`
--  ADD
--  ADD UNIQUE KEY `nominativo` (`nominativo`),
--  ADD UNIQUE KEY `nominativo_2` (`nominativo`),
--  ADD KEY `amm_nomin` (`nominativo`),
--  ADD KEY `id_arch_enrico` (`id_arch_enrico`),
--  ADD KEY `categoria_id` (`categoria_id`),
--  ADD KEY `data_nascita_persona` (`data_nascita`);

--
-- Limiti per la tabella `persone`
--
--ALTER TABLE `persone`
--  ADD CONSTRAINT `persone_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorie` (`id`),
--  ADD CONSTRAINT `persone_ibfk_2` FOREIGN KEY (`id_arch_enrico`) REFERENCES `archivio_enrico` (`ID`);