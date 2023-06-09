
DROP TABLE IF EXISTS `editore`;
CREATE TABLE `editore` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `editore` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tipedi` enum('S','V','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `editore_libro`;
CREATE TABLE `editore_libro` (
  `editore_id` int(10) NOT NULL,
  `libro_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `editore_libro`
  ADD PRIMARY KEY (`libro_id`,`editore_id`) USING BTREE;

