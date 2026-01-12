CREATE TABLE `dbf_dia120_enrico` (
  `datnum` varchar(10) NOT NULL,
  `anum` varchar(8) NOT NULL,
  `cddvd` varchar(10) NOT NULL,
  `hdint` varchar(10) NOT NULL,
  `hdext` varchar(10) NOT NULL,
  `sc` varchar(2) NOT NULL,
  `fi` varchar(2) NOT NULL,
  `tp` varchar(2) NOT NULL,
  `nfo` varchar(6) NOT NULL,
  `data` varchar(10) NULL, -- using varcahr instead of date because some entries have missing date format
  `localita` text NOT NULL,
  `argomento` text NOT NULL,
  `descrizione` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
