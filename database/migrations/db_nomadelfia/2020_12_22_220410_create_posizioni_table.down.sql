-- Reverse the migrations


SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `posizioni`;
DROP TABLE IF EXISTS `persone_posizioni`;

SET FOREIGN_KEY_CHECKS=1;
