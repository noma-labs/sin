-- Struttura della tabella `persone`

CREATE TABLE `persone` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nominativo` varchar(100) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `cognome` varchar(30) NOT NULL,
  `id_arch_pietro` int(10) NOT NULL,
  `id_alfa_enrico` int(10)  DEFAULT NULL,
  `provincia_nascita` varchar(64) DEFAULT NULL,
  `data_nascita` date NOT NULL,
  `sesso` enum('F','M') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dispense` enum('0','1') DEFAULT NULL,
  `guardia` enum('0','1') DEFAULT NULL,
  `sigla_biancheria` varchar(30) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `data_decesso` date DEFAULT NULL,
  `numero_elenco` varchar(32) DEFAULT NULL UNIQUE,
  `biografia` TEXT DEFAULT NULL,
  `cf` varchar(32) DEFAULT NULL,
  `origine` enum('interno', 'accolto', 'figlio_famiglia', 'esterno') NOT NULL,
   PRIMARY KEY (`id`,`nominativo`) USING BTREE,
   UNIQUE KEY `nominativo` (`nominativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle `persone`
--
-- ALTER TABLE `persone`
--     ADD UNIQUE KEY `nominativo` (`nominativo`)
