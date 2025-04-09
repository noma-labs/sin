-- Struttura della tabella `persone`

CREATE TABLE `persone` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nominativo` varchar(100) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `cognome` varchar(30) NOT NULL,
  `id_arch_pietro` int(10) NOT NULL,
  `id_alfa_enrico` int(10)  DEFAULT NULL COMMENT 'File ALFA di Enrico ricevuto il 15 feb 2023',
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


-- Temporary table added to have the "famiglia" field in the "persone" table
CREATE TABLE `alfa_enrico_15_feb_23` (
  `ID` int(10) NOT NULL,
  `COGNOME` varchar(255) DEFAULT NULL,
  `NOME` varchar(255) DEFAULT NULL,
  `ALIAS` varchar(255) DEFAULT NULL,
  `FOTO` varchar(255) DEFAULT NULL,
  `S` varchar(255) DEFAULT NULL,
  `FAMIGLIA` varchar(255) DEFAULT NULL,
  `NASCITA` date DEFAULT NULL,
  `COMUNE` varchar(255) DEFAULT NULL,
  `PROVINCIA` varchar(255) DEFAULT NULL,
  `REGIONE` varchar(255) DEFAULT NULL,
  `NAZIONE` varchar(255) DEFAULT NULL,
  `O` enum('A','E','F','I') DEFAULT NULL,
  `ENTRATA` date DEFAULT NULL,
  `POS` varchar(255) DEFAULT NULL,
  `POSTULA` date DEFAULT NULL,
  `EFFETT` date DEFAULT NULL,
  `STATO` varchar(255) DEFAULT NULL,
  `ORDINATO` date DEFAULT NULL,
  `MATRIMONIO` date DEFAULT NULL,
  `LSC` varchar(255) DEFAULT NULL,
  `BATTESIMO` date DEFAULT NULL,
  `CRESIMA` date DEFAULT NULL,
  `USCITA` date DEFAULT NULL,
  `USC` varchar(255) DEFAULT NULL,
  `DIMISSIONI` date DEFAULT NULL,
  `RIENTRO` date DEFAULT NULL,
  `RIUSCITA` date DEFAULT NULL,
  `RIENTROS` date DEFAULT NULL,
  `RIRIUSC` date DEFAULT NULL,
  `RIENTRO3` date DEFAULT NULL,
  `RIUSC4` date DEFAULT NULL,
  `RIENTRO4` date DEFAULT NULL,
  `DECESSO` date DEFAULT NULL,
  `FUNERALE` date DEFAULT NULL,
  `LUOGO` varchar(255) DEFAULT NULL,
  `SEPOLTURA` date DEFAULT NULL,
  `CIMITERO` varchar(255) DEFAULT NULL,
  `LOCALITA` varchar(255) DEFAULT NULL,
  `MODO` varchar(255) DEFAULT NULL,
  `CINERARIO` varchar(255) DEFAULT NULL,
  `NELEN` varchar(255) DEFAULT NULL,
  `NELEN_FORMAT` varchar(255) DEFAULT NULL COMMENT 'NELEN with trim spaces and numbers and letters inverted',
  `P` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `alfa_enrico_15_feb_23`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `alfa_enrico_15_feb_23`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `persone`
  ADD CONSTRAINT `alf_enrico_feb23_fk` FOREIGN KEY (`id_alfa_enrico`) REFERENCES `alfa_enrico_15_feb_23` (`ID`);
COMMIT;
