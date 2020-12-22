-- Reverse the migrations
SET FOREIGN_KEY_CHECKS=0;


DROP TABLE IF EXISTS `gruppi_familiari`;
DROP TABLE IF EXISTS `gruppi_persone`;
DROP TABLE IF EXISTS `gruppi_familiari_capogruppi`;

SET FOREIGN_KEY_CHECKS=1;
