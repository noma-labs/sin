
DROP TABLE IF EXISTS `libro`;
CREATE TABLE `libro` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titolo` varchar(255) DEFAULT NULL,
  `ID_AUTORE` int(9) NOT NULL,
  `autore` varchar(255) DEFAULT NULL,
  `ID_EDITORE` int(9) NOT NULL,
  `editore` varchar(255) DEFAULT NULL,
  `collocazione` varchar(255) DEFAULT NULL,
  `classificazione_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tobe_printed` tinyint(1) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_note` text,
  `isbn` char(13) DEFAULT NULL,
  `critica` int(11) DEFAULT NULL COMMENT 'Votazione del libro da 1 a 10',
  `categoria` enum('piccoli','elementari','medie','superiori','adulti') DEFAULT NULL,
  `dimensione` text COMMENT 'Altezza per larghezza (e.g. 30cmx20cm)',
  `data_pubblicazione` text COMMENT 'Mese e anno di pubblicazione del libro (e.g. Aprile 2017).'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


