

DROP TABLE IF EXISTS `prestito`;
CREATE TABLE `prestito` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `data_inizio_prestito` date DEFAULT NULL,
  `data_fine_prestito` date DEFAULT NULL,
  `libro_id` int(11) DEFAULT NULL,
  `in_prestito` tinyint(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `bibliotecario_id` int(11) NOT NULL,
  `cliente_id` int(10) DEFAULT NULL,
  `cliente_type` varchar(191) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

