-- Run the migrations

ALTER TABLE `famiglie_persone` CHANGE `data_entrata` `deleteme_data_entrata` DATE NULL DEFAULT NULL;

ALTER TABLE `famiglie_persone` CHANGE `data_uscita` `deleteme_data_uscita` DATE NULL DEFAULT NULL;

DROP TABLE `famiglie_posizioni`;
