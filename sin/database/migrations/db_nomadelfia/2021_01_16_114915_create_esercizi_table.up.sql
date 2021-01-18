

CREATE TABLE `esercizi_spirituali` (
  `id` int(10) PRIMARY KEY AUTO_INCREMENT,
  `turno` ENUM('no-esercizi', '1-turno', '2-turno', '3-turno', 'abetone-bimbi', "abetone-grandi") NOT NULL,
  -- numero turno (1, 2, 3 casa, 4 (abetone -13 anni-21 anni), 5 (bimbi 6 - 12anni), oppure 0 non fa esercizi
  `responsabile_id` int(10),
  `data_inizio` DATE  NULL DEFAULT NULL,
  `data_fine` DATE NULL DEFAULT NULL,
  `luogo` varchar(50),
  `descrizione` varchar(100),
  `stato` ENUM('0','1') NOT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Limiti per la tabella `esercizi_spirituali`
--
ALTER TABLE `esercizi_spirituali` ADD CONSTRAINT `esercizi_responsabile_1` FOREIGN KEY (`responsabile_id`) REFERENCES `persone` (`id`);

-- Una persona può partecipare più di un esercizio spirituale nello stesso anno.
CREATE TABLE `persone_esercizi` (
  `persona_id` int(10) NOT NULL,
  `esercizi_id` int(10) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`persona_id`,`esercizi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Limiti per la tabella `persone_esercizi`
--
ALTER TABLE `persone_esercizi`
  ADD CONSTRAINT `persone_esercizi_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
  ADD CONSTRAINT `persone_esercizi_2` FOREIGN KEY (`esercizi_id`) REFERENCES `esercizi_spirituali` (`id`);

