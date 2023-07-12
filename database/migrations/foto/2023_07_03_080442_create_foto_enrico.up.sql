
CREATE TABLE `foto_enrico` (
     `id` int(11) NOT NULL,
     `data` date NOT NULL,
     `datnum` varchar(10) NOT NULL,
     `localita` varchar(50) NOT NULL,
     `argomento` varchar(100) NOT NULL,
     `descrizione` varchar(200) NOT NULL,
     `anum` int(8) NOT NULL,
     `cddvd` varchar(10) NOT NULL,
     `hdint` varchar(10) NOT NULL,
     `hdext` varchar(10) NOT NULL,
     `sc` varchar(2) NOT NULL,
     `fi` varchar(2) NOT NULL,
     `tp` varchar(2) NOT NULL,
     `nfo` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tipo` (
    `NR` int(11) NOT NULL,
    `FI` varchar(5) NOT NULL COMMENT 'File di partenza. E.g., FO=FotoDigitale',
    `TP` varchar(5) NOT NULL COMMENT 'Tipo di pellicola',
    `DESCRIZ` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tipo`
--

INSERT INTO `tipo` (`NR`, `FI`, `TP`, `DESCRIZ`) VALUES
         (1, 'FO', 'BB', 'NEG. B/N 135 (24X36) ORIGINALE'),
         (2, 'FO', 'RB', 'NEG. B/N 135 (24X36) RIPRODUZIONE'),
         (3, 'FO', 'BA', 'NEG. B/N 120 (6X4,5-6X6-6X7-6X9) ORIGINALE'),
         (4, 'FO', 'RA', 'NEG. B/N 120 (6X4,5-6X6-6X7-6X9) RIPRODUZIONE'),
         (5, 'FO', 'FC', 'NEG. B/N 135 (24X36) RIPRODUZIONE FOTOGRAMMA CINEMATOGRAFICO'),
         (6, 'FO', 'CB', 'NEG. COL 135 (24X36) ORIGINALE'),
         (7, 'FO', 'GH', 'NEG. COL 135 (24X36) RIPRODUZIONE'),
         (9, 'SL', 'DT', 'DIA. COL 135 (24X26) ORIGINALE - INTELAIATA'),
         (10, 'SL', 'DS', 'DIA. COL 135 (24X36) ORIGINALE - IN STRISCIA'),
         (11, 'SL', 'RD', 'DIA. COL 135 (24X36) RIPRODUZIONE - INTELAIATA'),
         (12, 'SL', 'RS', 'DIA. COL 135 (24X36) RIPRODUZIONE - IN STRISCIA'),
         (13, 'SL', 'DR', 'DIA. COL 135 (24X36) DUPLICATO - INTELAIATO'),
         (14, 'FO', 'CX', 'NEG. COL 110 (13X17) ORIGINALE'),
         (15, 'FO', 'CA', 'NEG. COL 120 (6X4,5-6X6-6X7-6X9) ORIGINALE'),
         (16, 'FO', 'CY', 'NEG. COL     (32X41)             ORIGINALE'),
         (17, 'FO', 'EE', 'NEG. COL 120 (6X4,5-6X6-6X7-6X9) RIPRODUZIONE'),
         (18, 'FO', 'LM', 'NEG. COL GRANDE FORMATO, DA 9X12 IN POI'),
         (19, 'DI', '64', 'DIA. COL 120 (6X4,5) ORIGINALE, IN STRISCIA'),
         (20, 'DI', '66', 'DIA. COL 120 (6X6)   ORIGINALE, IN STRISCIA'),
         (21, 'DI', '67', 'DIA. COL 120 (6X7)   ORIGINALE, IN STRISCIA'),
         (22, 'DI', '69', 'DIA. COL 120 (6X9)   ORIGINALE, IN STRISCIA'),
         (23, 'DI', 'GF', 'DIA. COL GRANDE FORMATO, SINGOLA'),
         (24, 'DG', 'FD', 'FOTO DIGITALI'),
         (25, 'DG', 'SF', 'FOTO SCANS CHE ABBIAMO (ALCUNE RIPRODOTTE IN PELLICOLA)'),
         (26, 'FO', 'SN', 'NEG. SCANSIONATI'),
         (27, 'DG', 'SD', 'DIA. SCANSIONATE'),
         (28, 'DG', 'SM', 'MISTO, FOTO, NEG. DIA, SCANSIONATE'),
         (29, 'DG', 'FT', 'FOTOGRAMMI DA TELECAMERA'),
         (30, 'FO', 'FS', 'FOTO SCANS NON RIPR CHE NON ABB (NUM S0001 E SEGUENTI)'),
         (31, 'DG', 'FP', 'FOTOTESSERE POLAROID SCANSIONATE'),
         (33, 'FO', 'BC', 'NEGATIVI B/N E COLORE'),
         (34, 'FO', 'DZ', 'DIAPOSITIVE DI TUTTI I FORMATI'),
         (35, 'DG', 'MS', 'MISTO SCANSIONI EST E SCANSIONI INTERNE');

--
-- Indici per le tabelle `album`
--
-- ALTER TABLE `album`
--     ADD PRIMARY KEY (`data`,`datnum`),
--   ADD UNIQUE KEY `data` (`data`,`datnum`),
--   ADD KEY `localita` (`localita`),
--   ADD KEY `argomento` (`argomento`),
--   ADD KEY `descrizione` (`descrizione`),
--   ADD KEY `anum` (`anum`);
-- COMMIT;

