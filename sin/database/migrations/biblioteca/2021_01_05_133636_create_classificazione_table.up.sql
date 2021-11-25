
DROP TABLE IF EXISTS `classificazione`;
CREATE TABLE `classificazione` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `descrizione` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `classificazione` ADD UNIQUE KEY `descrizione_2` (`descrizione`);


