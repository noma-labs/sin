CREATE TABLE IF NOT EXISTS `alimentazione` (
    `id` int(10) NOT NULL,
    `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `alimentazione`
--

INSERT INTO `alimentazione` (`id`, `nome`) VALUES
    (1, 'BENZINA'),
    (2, 'DIESEL'),
    (3, 'ELETTRICA'),
    (4, 'GPL'),
    (5, 'METANO'),
    (6, 'DIESEL-METANO'),
    (7, 'DIESEL-GPL'),
    (8, 'BENZINA-METANO'),
    (9, 'BENZINA-GPL');

-- --------------------------------------------------------

--
-- Struttura della tabella `colori`
--

CREATE TABLE IF NOT EXISTS `colori` (
    `ofco_iden` int(10) NOT NULL,
    `ofco_nome` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `colori`
--

INSERT INTO `colori` (`ofco_iden`, `ofco_nome`) VALUES
    (1, 'NERO'),
    (2, 'AZURRO'),
    (3, 'BIANCO'),
    (4, 'ROSSO');

-- --------------------------------------------------------

--
-- Struttura della tabella `gomme_veicolo`
--

CREATE TABLE IF NOT EXISTS `gomme_veicolo` (
    `gomme_id` int(10) UNSIGNED NOT NULL,
    `veicolo_id` int(11) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `impiego` (
    `id` int(10) NOT NULL,
    `nome` varchar(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `impiego` (`id`, `nome`) VALUES
    (1, 'interno'),
    (2, 'grosseto'),
    (3, 'Viaggi Lunghi'),
    (4, 'personale'),
    (5, 'roma'),
    (6, 'Autobus');


CREATE TABLE IF NOT EXISTS `incidenti` (
    `ofin_veic` int(10) NOT NULL,
    `ofin_ident` int(10) NOT NULL,
    `ofin_desc` varchar(500) NOT NULL,
    `ofin_data` int(10) NOT NULL,
    `ofin_idpe` int(10) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `libretto_circolazione` (
    `ofli_iden` int(10) NOT NULL,
    `ofli_desc` varchar(300) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `manutenzione_programmata` (
    `id` int(10) UNSIGNED NOT NULL,
    `manutenzione_id` int(10) UNSIGNED NOT NULL,
    `veicolo_id` int(11) NOT NULL,
    `meccanico_id` int(11) NOT NULL,
    `kilometri` int(11) NOT NULL,
    `data` date NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struttura della tabella `marca`
--

CREATE TABLE IF NOT EXISTS `marca` (
    `id` int(10) NOT NULL,
    `nome` varchar(30) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `marca`
--

INSERT INTO `marca` (`id`, `nome`) VALUES
    (1, 'FIAT'),
    (2, 'LANCIA'),
    (3, 'NISSAN'),
    (4, 'OPEL'),
    (5, 'RENAULT'),
    (6, 'SKODA'),
    (7, 'CITROEN'),
    (8, 'VOLKSWAGEN'),
    (9, 'ALFA ROMEO'),
    (10, 'LAND ROVER'),
    (11, 'PEGEUT'),
    (12, 'FORD'),
    (13, 'TATA'),
    (14, 'TOYOTA'),
    (15, 'MERCEDES BENZ'),
    (16, 'IVECO'),
    (17, 'HONDA'),
    (18, 'KYMCO'),
    (19, 'PIAGGIO'),
    (20, 'SETRA'),
    (21, 'APRILIA'),
    (22, 'YAMAHA ');


CREATE TABLE IF NOT EXISTS `modello` (
    `id` int(10) NOT NULL,
    `marca_id` int(10) NOT NULL,
    `nome` varchar(40) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `modello`
--

INSERT INTO `modello` (`id`, `marca_id`, `nome`) VALUES
    (1, 1, 'BRAVO'),
    (2, 7, 'C3'),
    (3, 5, 'CLIO'),
    (4, 16, 'DAILY'),
    (5, 1, 'DOBLO'),
    (6, 1, 'DUCATO'),
    (7, 1, 'PUNTO'),
    (8, 1, 'IDEA'),
    (9, 2, 'DELTA'),
    (10, 8, 'LUPO'),
    (11, 1, 'MAREA'),
    (12, 3, 'PICK-UP'),
    (13, 4, 'ASTRA'),
    (14, 1, 'PALIO'),
    (15, 1, 'PANDA'),
    (16, 5, 'MEGANE'),
    (17, 1, 'SCUDO'),
    (18, 6, 'FABIA'),
    (19, 1, 'STILO'),
    (20, 1, '500 X'),
    (21, 1, 'TIPO'),
    (22, 1, 'TEMPRA'),
    (23, 4, 'CORSA'),
    (24, 1, 'INNOCENTI'),
    (25, 1, 'COMPAGNOLA'),
    (26, 9, '146'),
    (27, 1, 'FIORINO'),
    (28, 10, 'DISCOVERY'),
    (29, 11, '207'),
    (30, 8, 'LT35'),
    (31, 12, 'TRANSIT'),
    (32, 1, 'MULTIPLA'),
    (33, 13, 'TELCOLINE'),
    (34, 3, 'DELTA'),
    (35, 4, 'ALGKFVQ'),
    (36, 7, 'GLKGN'),
    (37, 18, 'SDF'),
    (40, 15, 'AAAAAA'),
    (41, 2, 'SDF'),
    (44, 17, 'AGFAG'),
    (45, 16, 'FAFF'),
    (46, 16, 'KAJGN'),
    (47, 18, 'AG'),
    (48, 16, 'FA'),
    (49, 16, 'AFAF'),
    (50, 20, 'AGQ'),
    (51, 20, 'AG'),
    (52, 16, '11'),
    (56, 1, 'STILO BLU'),
    (57, 4, 'MERIVA'),
    (58, 7, 'JUMPY'),
    (59, 1, 'ULYSSE'),
    (60, 17, 'ONDA SH'),
    (61, 12, 'FOCUS'),
    (62, 14, 'VESPONE'),
    (63, 1, 'LEONARDO 150'),
    (64, 15, 'SPRINTER TRAVEL'),
    (65, 1, 'BRAVO NERA'),
    (66, 7, 'ZX'),
    (67, 10, 'FREELANDER'),
    (68, 7, 'CITROEN'),
    (69, 1, 'TALENTO'),
    (70, 1, 'DOBLO'),
    (71, 1, 'DOBLÃ² MERCI 1.6'),
    (72, 1, 'GRANDE PUNTO BIANCA'),
    (73, 17, 'FORESIGHT 150 ROSSO'),
    (74, 1, 'CROMA'),
    (75, 22, 'N MAX'),
    (76, 2, 'PHEDRA'),
    (77, 3, 'TERRANO'),
    (78, 1, 'DUCATO MAXI 2.3');

CREATE TABLE IF NOT EXISTS `prenotazioni` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `veicolo_id` int(11) DEFAULT NULL,
  `data_partenza` date NOT NULL,
  `ora_partenza` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_arrivo` date NOT NULL,
  `ora_arrivo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meccanico_id` int(11) NOT NULL,
  `uso_id` int(11) NOT NULL,
  `note` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `destinazione` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Grosseto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tipologia` (
  `id` int(10) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `tipologia` (`id`, `nome`) VALUES
    (1, 'Autovettura'),
    (2, 'Autocarro'),
    (3, 'Autobus'),
    (4, 'Camper'),
    (5, 'Ciclomotore'),
    (6, 'Furgoncino'),
    (7, 'Furgone'),
    (8, 'Mezzo agricolo'),
    (9, 'Motocarro'),
    (10, 'Motociclo'),
    (11, 'Rimorchio'),
    (12, 'Trattore'),
    (13, 'Veicolo edile');

CREATE TABLE IF NOT EXISTS `tipo_filtro` (
  `id` int(10) UNSIGNED NOT NULL,
  `codice` varchar(191) NOT NULL,
  `tipo` enum('aria','gasolio','olio','ac') NOT NULL,
  `note` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tipo_filtro` (`id`, `codice`, `tipo`, `note`) VALUES
(1, 'UFI 2513900', 'olio', ''),
(2, 'MOPAR 51974227', 'aria', ''),
(3, 'UFI 2605200', 'gasolio', ''),
(4, 'BOSCH M5017', 'ac', 'No Gas R134'),
(5, 'S 3281', 'aria', ''),
(7, 'N 9656', 'gasolio', ''),
(8, 'M 2038', 'ac', ''),
(9, 'UFI 2605200', 'olio', ''),
(14, 'BOSCH R2304', 'ac', ''),
(15, 'JAPANPARTS FA238S', 'aria', ''),
(16, 'JAPANPARTS FO213S', 'olio', ''),
(17, 'N 4440', 'gasolio', ''),
(18, 'S 2365', 'aria', ''),
(19, 'S 3160', 'aria', ''),
(20, 'S 0051', 'aria', ''),
(21, 'S 0015', 'aria', ''),
(22, 'S 3332', 'aria', ''),
(24, 'MANN C40001', 'aria', ''),
(25, 'S 0194', 'aria', ''),
(26, 'S 3307', 'aria', ''),
(27, 'S 0346', 'aria', ''),
(28, 'S 0049', 'aria', ''),
(29, 'S 3790', 'aria', ''),
(30, 'S 0059', 'aria', ''),
(31, 'P 3354', 'olio', ''),
(32, 'P 9238', 'olio', ''),
(33, 'P 3336', 'olio', ''),
(34, 'P 7083', 'olio', ''),
(35, 'P 7024', 'olio', ''),
(36, 'P 7096', 'olio', ''),
(37, 'P 3300', 'olio', ''),
(38, 'P 7108', 'olio', ''),
(39, 'P 3351', 'olio', ''),
(40, 'P 3201', 'olio', ''),
(41, 'N 4293', 'gasolio', ''),
(42, 'N 7006', 'gasolio', ''),
(43, 'N 7013', 'gasolio', ''),
(44, 'N 9657', 'gasolio', ''),
(45, 'N 2099', 'gasolio', ''),
(46, 'N 2045', 'gasolio', ''),
(47, 'CNHI5802050393', 'gasolio', 'Gasolio 176 â‚¬'),
(48, 'N 2155', 'gasolio', ''),
(49, 'N 2076', 'gasolio', ''),
(51, 'N 4314', 'gasolio', ''),
(52, 'N 4105', 'gasolio', ''),
(53, 'N 0001', 'gasolio', ''),
(54, 'N 6508', 'gasolio', ''),
(55, 'DELPHI HDF 924', 'gasolio', ''),
(56, 'N 2049', 'gasolio', ''),
(58, 'N 6373', 'gasolio', ''),
(59, 'N 0000', 'gasolio', ''),
(61, 'N 4106', 'gasolio', ''),
(62, 'N 0016', 'gasolio', ''),
(63, 'N 0013', 'gasolio', ''),
(64, 'N 2048', 'gasolio', ''),
(65, 'N 2062', 'gasolio', ''),
(66, 'S 0217', 'aria', ''),
(67, 'P 7073', 'olio', ''),
(68, 'P 9194', 'olio', ''),
(69, 'P 3252', 'olio', ''),
(70, 'P 9256', 'olio', ''),
(72, 'P 9192', 'olio', ''),
(73, 'P 3355', 'olio', ''),
(74, 'P 3261', 'olio', ''),
(75, 'UFI 2321702', 'olio', ''),
(76, 'S 3532', 'aria', ''),
(77, 'S 3686', 'aria', ''),
(78, 'S 0002', 'aria', ''),
(79, 'S 3327', 'aria', ''),
(80, 'S 3712', 'aria', ''),
(81, 'S 3055', 'aria', ''),
(82, 'UFI 3093330', 'aria', ''),
(83, 'S 3259', 'aria', ''),
(84, 'S 2200', 'aria', ''),
(85, 'S 0041', 'aria', ''),
(86, 'BOSCH M2003', 'ac', ''),
(87, 'BOSCH M2079', 'ac', ''),
(88, 'BOSCH M2039', 'ac', ''),
(89, 'BOSCH M2258', 'ac', ''),
(90, 'MANN CU 22029', 'ac', ''),
(91, 'BOSCH R2377', 'ac', ''),
(92, 'BOSCH M2072', 'ac', ''),
(93, 'BOSCH M2065', 'ac', ''),
(94, 'BOSCH M2042', 'ac', ''),
(95, 'BOSCH M2188', 'ac', ''),
(96, 'BOSCH M2206', 'ac', ''),
(97, 'BOSCH M2012', 'ac', ''),
(99, 'BOSCH M2091', 'ac', ''),
(100, 'BOSCH M2541', 'ac', ''),
(103, 'S 3265', 'aria', ''),
(105, 'BOSCH M2228', 'ac', ''),
(106, 'UFI 3062900', 'aria', ''),
(107, 'UFI 2608000', 'gasolio', ''),
(108, 'FC 109 S', 'gasolio', ''),
(109, 'FO 110 S', 'olio', ''),
(110, 'FA 101 S', 'aria', ''),
(111, 'FO 114 S', 'olio', ''),
(112, 'S 3588', 'aria', ''),
(113, 'N 2095', 'gasolio', ''),
(114, 'UFI 2500100', 'olio', ''),
(116, 'H 1206 - 264807', 'aria', ''),
(117, 'H 1013', 'gasolio', ''),
(118, 'UFI 2735600', 'aria', ''),
(119, 'N 2054', 'gasolio', ''),
(120, 'N 2862', 'gasolio', ''),
(121, 'ADP 152207', 'aria', '');


CREATE TABLE IF NOT EXISTS `tipo_gomme` (
  `id` int(10) UNSIGNED NOT NULL,
  `codice` varchar(191) NOT NULL,
  `note` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `tipo_gomme` (`id`, `codice`, `note`) VALUES
(1, '195/70 R15', ''),
(2, '170/75 R17', 'prova'),
(3, '215/55 R17 94V', ''),
(4, '215/60 R16 95H', ''),
(5, '195/60 R15 88T', ''),
(6, '185/65 R15 88T', ''),
(7, '205/55 R16 87T', ''),
(8, '205/60 R16 92V', ''),
(9, '205/65 R16 95V', ''),
(12, '225/50 R17 94V', ''),
(13, '235/45 R18 94W', ''),
(14, '235/40 R19 92W', ''),
(15, '205/60 R16 92T M+S', ''),
(16, '215/50 R17 91T M+S', ''),
(17, '195/60 R15 88H', ''),
(18, '175/65 R14 82H', ''),
(19, '185/60 R14 82H', ''),
(20, '205/50 R15 86V', ''),
(21, '185/55 R15 81H', ''),
(22, '195/50 R15 82V', ''),
(23, '185/60 R14 82T M+S', ''),
(24, '195/50 R15 82H M+S', ''),
(25, '175/65 R 14 82Q M+S', ''),
(26, '185/60 R15 84H', ''),
(27, '195/50 R16 84V', ''),
(28, '175/65R 14 82T', ''),
(33, '175/70 R13 82T', ''),
(34, '225/65 R16 (112R)S-S', ''),
(35, '235/65 R16 115R', ''),
(36, '225/65 R16=B', ''),
(37, '235/65 R16=A', ''),
(39, '235/65 R16 115 R (112Q) S.S', ''),
(40, '225/75 R16C (118/116) S-D', ''),
(41, '7.00 R16C (117/116L) S-D', ''),
(42, '195/60 R16C 99/97T', ''),
(43, '195/65 R15 95T', ''),
(44, '175/70 R14 REINFORCED 88T', ''),
(45, '175/70 R15C REINFORCED 97S', ''),
(46, '215/70 R15 C 109/107Q', ''),
(47, '195/70 R15C 103/101Q', ''),
(48, '215/70 R15C (109/107S) S-S', ''),
(49, '225/70 R15C 112/110S', ''),
(50, '225/70 R15C 112/110R M+S', ''),
(51, '205/75 R16C 110/108Q', ''),
(52, '215/75 R16C 113/111Q', ''),
(53, '205/55 R16 91H', ''),
(54, '225/70 R15 C 112/110R', ''),
(55, '215/70 R15 C 109/107S', ''),
(57, '205/70 R15C 106/104Q', ''),
(58, '195/60 R15 83H', ''),
(59, '185/65 R14 83Q M+S', ''),
(60, '185/65 R14 86T', ''),
(61, '215/60 R16 99T', ''),
(62, '215/65 R15 96H', ''),
(63, '175/65 R15 84T', ''),
(64, '195/55 R16 87H', ''),
(65, '204/45 R17 88V', ''),
(66, '185/55 R14 79H', ''),
(67, '195/45 R15 78H', ''),
(68, '205 R16 C 8PR', ''),
(69, '235/75 R15 (106S)', ''),
(70, '165/70 R 14 81T', ''),
(72, '185/55 R15 82V', ''),
(73, '215/45 R17 87V', ''),
(74, '215/45 ZR17 87W', ''),
(75, '215/45 R17 87W', ''),
(76, '215/45 ZR17', ''),
(77, '195/65 R15 91H', ''),
(78, '205/55 R16 91V', ''),
(79, '205/55 R16 87W', ''),
(80, '215/45 R17 91H M+S', ''),
(81, '225/75 R16 C (116/114) S-D', ''),
(82, '7.00 R16 C(117/116N)', ''),
(83, '225/75 R17.5 (126/125M) S-D', ''),
(84, '215/75 R17.5 (124/123M)', ''),
(85, '315/80 R22.5 (154/149J) S-D', ''),
(86, '295/80 R22.5 (152/147J)', ''),
(87, '295/80 R22.5 (152/148J) S-D', ''),
(88, '205/75 R16 C (110/108R) S-D', ''),
(89, '315/80 R22.5 (152/148G) S. D.', ''),
(90, '315/60 R22.5 (152/148G)', ''),
(91, 'VEDI LIBRETTO', ''),
(92, '315/80 R212.5 (154/150G) S. D.', ''),
(93, '195/80 R15 96S', ''),
(94, '215/65 R16 98T', ''),
(95, '225/55 R17 97H', ''),
(96, '235/50 R18 97H', ''),
(97, '215/65 R16C 109/107T', ''),
(98, '215/65 R16C 106/104H', ''),
(99, '215/60 R17 C 109/107H', ''),
(100, '215/60 R17C 109/107H', ''),
(101, '225/45 R17 94V', ''),
(102, '225/40 R18 92W', ''),
(103, '205/50 R16 91V', ''),
(108, '225/45 R17 91V', ''),
(109, '215/70 R 15 C 110/112 S', ''),
(110, '215/70 R15C 110/112S', ''),
(111, '215/70 R15 C 110/112S', ''),
(112, '225/70 R15 C 110/112S', ''),
(113, '225/70 R15 C 110/112R', '');


CREATE TABLE IF NOT EXISTS `tipo_manutenzione` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(191) NOT NULL,
  `frequenza` int(11) DEFAULT NULL,
  `unita` enum('km','anni') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tipo_olio` (
  `id` int(10) UNSIGNED NOT NULL,
  `codice` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `tipo_olio` (`id`, `codice`, `note`) VALUES
(1, '5W/30', ''),
(2, '10W/40', ''),
(3, '15/W40', ''),
(4, '0W/20', '');


CREATE TABLE IF NOT EXISTS `usi` (
  `ofus_iden` int(10) NOT NULL,
  `ofus_nome` varchar(30) NOT NULL,
  `ofus_abbr` varchar(30) NOT NULL,
  `ordinamento` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `usi` (`ofus_iden`, `ofus_nome`, `ofus_abbr`, `ordinamento`) VALUES
(1, 'Personale', 'PER', 3),
(2, 'Comunitario', 'COM', 2),
(3, 'Scuola', 'SCU', 4),
(4, 'Familiare', 'FAM', 5),
(5, 'Stazione', 'STA', 6),
(6, 'Mediche', 'MED', 1);


CREATE TABLE IF NOT EXISTS `veicolo` (
  `id` int(10) NOT NULL COMMENT 'identificativo veicolo',
  `nome` varchar(100) NOT NULL COMMENT 'nome interno del vaicolo',
  `targa` varchar(30) DEFAULT NULL COMMENT 'targa del veicolo',
  `modello_id` int(10) NOT NULL COMMENT 'modello del veicolo',
  `colore` int(10) DEFAULT NULL COMMENT 'Colore del veicolo',
  `impiego_id` int(10) NOT NULL COMMENT 'impiego del veicolo nella comunitÃ ',
  `tipologia_id` int(10) NOT NULL COMMENT 'tipologia di veicolo',
  `alimentazione_id` int(10) NOT NULL COMMENT 'tipo di carburante',
  `num_posti` int(3) NOT NULL COMMENT 'numero di posti',
  `prenotabile` tinyint(1) NOT NULL DEFAULT '0',
  `filtro_olio` int(10) UNSIGNED DEFAULT NULL,
  `filtro_gasolio` int(10) UNSIGNED DEFAULT NULL,
  `filtro_aria` int(10) UNSIGNED DEFAULT NULL,
  `filtro_aria_condizionata` int(10) UNSIGNED DEFAULT NULL,
  `olio_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `litri_olio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura stand-in per le viste `v_clienti_meccanica`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE IF NOT EXISTS `v_clienti_meccanica` (
`id` int(10)
,`nominativo` varchar(100)
,`data_nascita` date
,`cliente_con_patente` varchar(3)
);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `v_lavoratori_meccanica`
-- (Vedi sotto per la vista effettiva)
--
CREATE TABLE IF NOT EXISTS `v_lavoratori_meccanica` (
`azienda_id` int(10)
,`persona_id` int(10)
,`nominativo` varchar(100)
,`stato` enum('Attivo','Non Attivo','Sospeso')
,`mansione` enum('RESPONSABILE AZIENDA','LAVORATORE')
,`data_inizio_azienda` date
,`data_fine_azienda` date
);


--
-- Struttura per vista `v_clienti_meccanica`
--
DROP TABLE IF EXISTS `v_clienti_meccanica`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clienti_meccanica`  AS  select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,(select distinct (case `db_patente`.`persone_patenti`.`numero_patente` when '' then '' else 'CP ' end) from `db_patente`.`persone_patenti` where ((`db_patente`.`persone_patenti`.`numero_patente` is not null) and (`db_patente`.`persone_patenti`.`persona_id` = `db_nomadelfia`.`persone`.`id`))) AS `cliente_con_patente` from `db_nomadelfia`.`persone` where (isnull(`db_nomadelfia`.`persone`.`data_decesso`) and (`db_nomadelfia`.`persone`.`data_nascita` <= (sysdate() - interval 200 year_month))) ;

-- --------------------------------------------------------

--
-- Struttura per vista `v_lavoratori_meccanica`
--
DROP TABLE IF EXISTS `v_lavoratori_meccanica`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_lavoratori_meccanica`  AS  select `db_nomadelfia`.`aziende_persone`.`azienda_id` AS `azienda_id`,`db_nomadelfia`.`aziende_persone`.`persona_id` AS `persona_id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`aziende_persone`.`stato` AS `stato`,`db_nomadelfia`.`aziende_persone`.`mansione` AS `mansione`,`db_nomadelfia`.`aziende_persone`.`data_inizio_azienda` AS `data_inizio_azienda`,`db_nomadelfia`.`aziende_persone`.`data_fine_azienda` AS `data_fine_azienda` from (`db_nomadelfia`.`aziende_persone` join `db_nomadelfia`.`persone`) where ((`db_nomadelfia`.`aziende_persone`.`azienda_id` = 1) and (`db_nomadelfia`.`persone`.`id` = `db_nomadelfia`.`aziende_persone`.`persona_id`) and (`db_nomadelfia`.`persone`.`id` <> 127)) order by `db_nomadelfia`.`aziende_persone`.`mansione` ;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `alimentazione`
--
ALTER TABLE `alimentazione`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `gomme_veicolo`
--
ALTER TABLE `gomme_veicolo`
  ADD UNIQUE KEY `gomme_veicolo_gomme_id_veicolo_id_unique` (`gomme_id`,`veicolo_id`),
  ADD KEY `gomme_veicolo_veicolo_id_foreign` (`veicolo_id`);

--
-- Indici per le tabelle `impiego`
--
ALTER TABLE `impiego`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `manutenzione_programmata`
--
ALTER TABLE `manutenzione_programmata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manutenzione_programmata_manutenzione_id_foreign` (`manutenzione_id`),
  ADD KEY `manutenzione_programmata_veicolo_id_foreign` (`veicolo_id`),
  ADD KEY `manutenzione_programmata_meccanico_id_foreign` (`meccanico_id`);

--
-- Indici per le tabelle `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `modello`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `meccanico_id` (`meccanico_id`),
  ADD KEY `uso_id` (`uso_id`),
  ADD KEY `veicolo_id` (`veicolo_id`),
  ADD KEY `destinazione` (`destinazione`);

--
-- Indici per le tabelle `tipologia`
--
ALTER TABLE `tipologia`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `tipo_filtro`
--
ALTER TABLE `tipo_filtro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_filtro_codice_tipo_unique` (`codice`,`tipo`);

--
-- Indici per le tabelle `tipo_gomme`
--
ALTER TABLE `tipo_gomme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_gomme_codice_unique` (`codice`);

--
-- Indici per le tabelle `tipo_manutenzione`
--
ALTER TABLE `tipo_manutenzione`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `tipo_olio`
--
ALTER TABLE `tipo_olio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_olio_codice_unique` (`codice`);

--
-- Indici per le tabelle `usi`
--
ALTER TABLE `usi`
  ADD PRIMARY KEY (`ofus_iden`);

--
-- Indici per le tabelle `veicolo`
--
ALTER TABLE `veicolo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `impiego_mezzo` (`impiego_id`),
  ADD KEY `modello_mezzo` (`modello_id`),
  ADD KEY `tipologia_veicolo` (`tipologia_id`),
  ADD KEY `alimentazione_id` (`alimentazione_id`),
  ADD KEY `veicolo_filtro_olio_foreign` (`filtro_olio`),
  ADD KEY `veicolo_filtro_gasolio_foreign` (`filtro_gasolio`),
  ADD KEY `veicolo_filtro_aria_foreign` (`filtro_aria`),
  ADD KEY `veicolo_filtro_aria_condizionata_foreign` (`filtro_aria_condizionata`),
  ADD KEY `veicolo_olio_id_foreign` (`olio_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `alimentazione`
--
ALTER TABLE `alimentazione`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `impiego`
--
ALTER TABLE `impiego`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `manutenzione_programmata`
--
ALTER TABLE `manutenzione_programmata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `marca`
--
ALTER TABLE `marca`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;


-- AUTO_INCREMENT per la tabella `modello`
--
ALTER TABLE `modello`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipo_filtro`
--
ALTER TABLE `tipo_filtro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT per la tabella `tipo_gomme`
--
ALTER TABLE `tipo_gomme`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT per la tabella `tipo_manutenzione`
--
ALTER TABLE `tipo_manutenzione`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipo_olio`
--
ALTER TABLE `tipo_olio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `usi`
--
ALTER TABLE `usi`
  MODIFY `ofus_iden` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `veicolo`
--
ALTER TABLE `veicolo`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'identificativo veicolo';

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `gomme_veicolo`
--
ALTER TABLE `gomme_veicolo`
  ADD CONSTRAINT `gomme_veicolo_gomme_id_foreign` FOREIGN KEY (`gomme_id`) REFERENCES `tipo_gomme` (`id`),
  ADD CONSTRAINT `gomme_veicolo_veicolo_id_foreign` FOREIGN KEY (`veicolo_id`) REFERENCES `veicolo` (`id`);

--
-- Limiti per la tabella `manutenzione_programmata`
--
ALTER TABLE `manutenzione_programmata`
  ADD CONSTRAINT `manutenzione_programmata_manutenzione_id_foreign` FOREIGN KEY (`manutenzione_id`) REFERENCES `tipo_manutenzione` (`id`),
  ADD CONSTRAINT `manutenzione_programmata_meccanico_id_foreign` FOREIGN KEY (`meccanico_id`) REFERENCES `db_nomadelfia`.`persone` (`id`),
  ADD CONSTRAINT `manutenzione_programmata_veicolo_id_foreign` FOREIGN KEY (`veicolo_id`) REFERENCES `veicolo` (`id`);

--
-- Limiti per la tabella `prenotazioni`
--
ALTER TABLE `prenotazioni`
  ADD CONSTRAINT `prenotazioni_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `db_nomadelfia`.`persone` (`id`),
  ADD CONSTRAINT `prenotazioni_ibfk_2` FOREIGN KEY (`meccanico_id`) REFERENCES `db_nomadelfia`.`persone` (`id`),
  ADD CONSTRAINT `prenotazioni_ibfk_3` FOREIGN KEY (`uso_id`) REFERENCES `usi` (`ofus_iden`),
  ADD CONSTRAINT `prenotazioni_ibfk_4` FOREIGN KEY (`veicolo_id`) REFERENCES `veicolo` (`id`);

--
-- Limiti per la tabella `veicolo`
--
ALTER TABLE `veicolo`
  ADD CONSTRAINT `impiego_mezzo` FOREIGN KEY (`impiego_id`) REFERENCES `impiego` (`id`),
  ADD CONSTRAINT `modello_mezzo` FOREIGN KEY (`modello_id`) REFERENCES `modello` (`id`),
  ADD CONSTRAINT `tipologia_veicolo` FOREIGN KEY (`tipologia_id`) REFERENCES `tipologia` (`id`),
  ADD CONSTRAINT `veicolo_filtro_aria_condizionata_foreign` FOREIGN KEY (`filtro_aria_condizionata`) REFERENCES `tipo_filtro` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `veicolo_filtro_aria_foreign` FOREIGN KEY (`filtro_aria`) REFERENCES `tipo_filtro` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `veicolo_filtro_gasolio_foreign` FOREIGN KEY (`filtro_gasolio`) REFERENCES `tipo_filtro` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `veicolo_filtro_olio_foreign` FOREIGN KEY (`filtro_olio`) REFERENCES `tipo_filtro` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `veicolo_ibfk_1` FOREIGN KEY (`alimentazione_id`) REFERENCES `alimentazione` (`id`),
  ADD CONSTRAINT `veicolo_olio_id_foreign` FOREIGN KEY (`olio_id`) REFERENCES `tipo_olio` (`id`) ON DELETE SET NULL;
COMMIT;

