-- Struttura della tabella `persone`

CREATE TABLE `persone` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `origine` enum('interno', 'accolto', 'famiglia', 'esterno') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `persone`  ADD UNIQUE KEY `unq_persone_nome_cognome_nascita` (`nome`, `cognome`, `data_nascita`);
