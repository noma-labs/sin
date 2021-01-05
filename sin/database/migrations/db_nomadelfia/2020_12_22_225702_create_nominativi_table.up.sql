--
-- Struttura della tabella `nominativi_storici`
--

CREATE TABLE `nominativi_storici` (
  `persona_id` int(10) NOT NULL,
  `nominativo` varchar(100) NOT NULL,
  `stato` enum('0','1') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`persona_id`,`nominativo`) USING BTREE,
  UNIQUE KEY `nominativo_2` (`nominativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Limiti per la tabella `nominativi_storici`
--
ALTER TABLE `nominativi_storici`
  ADD CONSTRAINT `nominativi_storici_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);