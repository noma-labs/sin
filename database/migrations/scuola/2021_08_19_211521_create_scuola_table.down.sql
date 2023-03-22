-- Reverse the migrations

SET FOREIGN_KEY_CHECKS=0;


DROP TABLE IF EXISTS `anno`;
DROP TABLE IF EXISTS `tipo`;
DROP TABLE IF EXISTS `alunni_classi`;
DROP TABLE IF EXISTS `classi`;
DROP TABLE IF EXISTS `coordinatori_classi`;

SET FOREIGN_KEY_CHECKS=1;
