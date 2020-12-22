-- Reverse the migrations

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `persone_categorie`;

SET FOREIGN_KEY_CHECKS=1;