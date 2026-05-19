

CREATE TABLE `recordings` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(11) DEFAULT NULL,
  `DATA` date NOT NULL,
  `ORE` varchar(4) DEFAULT NULL,
  `LOCALITA'` varchar(30) DEFAULT NULL,
  `PR` varchar(4) DEFAULT NULL,
  `AUTORE` varchar(55) DEFAULT NULL,
  `ARGOMENTO` varchar(292) DEFAULT NULL,
  `DESTINATARI` varchar(26) DEFAULT NULL,
  `DOC` varchar(9) DEFAULT NULL,
  `RAC` tinyint(1) DEFAULT NULL,
  `CART` tinyint(1) DEFAULT NULL,
  `PDF-SCAN` tinyint(1) DEFAULT NULL,
  `ORIG.` tinyint(1) DEFAULT NULL,
  `MS-DATT` tinyint(1) DEFAULT NULL,
  `MIN-PAG.` decimal(38,0) DEFAULT NULL,
  `T` varchar(2) DEFAULT NULL,
  `TRASCRITT.` varchar(14) DEFAULT NULL,
  `CONTROLLO` varchar(4) DEFAULT NULL,
  `GENERE` varchar(16) DEFAULT NULL,
  `REPER` varchar(8) DEFAULT NULL,
  `EDITO` varchar(1) DEFAULT NULL,
  `MINIDISC` varchar(7) DEFAULT NULL,
  `MDSEG` varchar(5) DEFAULT NULL,
  `NUMER` varchar(5) DEFAULT NULL,
  `QUALITÀ - AUDIO` varchar(56) DEFAULT NULL,
  `PUBBLICABILE` varchar(3) DEFAULT NULL,
  `IMPORTANZA - DESTINAT` varchar(32) DEFAULT NULL,
  `ARGOMENTI` varchar(105) DEFAULT NULL,
  `C` varchar(1) DEFAULT NULL,
  `G` varchar(1) DEFAULT NULL,
  `S` varchar(1) DEFAULT NULL,
  `NOTE` varchar(3) DEFAULT NULL,
  `sintesi PF` varchar(2) DEFAULT NULL
);

CREATE TABLE `recording_transcripts` (
    `recording_id` int(10) DEFAULT NULL,
    `code` varchar(11) DEFAULT NULL UNIQUE,
    `heading` text NOT NULL,
    `file_path` varchar(500) NOT NULL,
    `content` longtext DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_recording_transcripts_recording_id` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE
    -- FULLTEXT KEY `ft_recording_transcripts_content` (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

