--
-- Struttura della tabella `video`
--

DROP TABLE IF EXISTS `video`;
CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `cassetta` varchar(6) DEFAULT NULL,
  `R` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `inizio` double DEFAULT NULL,
  `fine` double DEFAULT NULL,
  `data_registrazione` datetime DEFAULT NULL,
  `V` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `Q` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `K` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `UTRASM` datetime DEFAULT NULL,
  `S` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `X` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `MIC` double DEFAULT NULL,
  `CAT` varchar(4) CHARACTER SET latin1 DEFAULT NULL,
  `MIN` double DEFAULT NULL,
  `N` varchar(1) CHARACTER SET latin1 DEFAULT NULL,
  `descrizione` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;