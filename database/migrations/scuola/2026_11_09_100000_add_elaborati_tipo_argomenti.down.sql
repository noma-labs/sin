-- Reverse the migrations

DROP TABLE IF EXISTS `elaborati_argomenti`;

ALTER TABLE `elaborati` DROP COLUMN `tipo`;
