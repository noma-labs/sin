
DROP TABLE IF EXISTS `autore`;
CREATE TABLE `autore` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id Unico Autore, esiste un reference con la tabella Autore',
  `autore` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipaut` enum('S','V','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ALTER TABLE `autore`
--   ADD KEY `autore` (`autore`),
--   ADD KEY `tipaut` (`tipaut`);

-- ALTER TABLE `autore`
--   MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Id Unico Autore, esiste un reference con la tabella Autore';

DROP TABLE IF EXISTS `autore_libro`;
CREATE TABLE `autore_libro` (
  `autore_id` int(10) UNSIGNED NOT NULL,
  `libro_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `autore_libro`
  ADD PRIMARY KEY (`autore_id`,`libro_id`);
--   ADD KEY `autore_id` (`autore_id`),
--   ADD KEY `libro_id` (`libro_id`);
