-- Run the migrations

ALTER TABLE db_nomadelfia.persone_posizioni DROP FOREIGN KEY persone_posizioni_ibfk_1;
ALTER TABLE db_nomadelfia.persone_posizioni DROP FOREIGN KEY persone_posizioni_ibfk_2;

ALTER TABLE db_nomadelfia.persone_posizioni DROP PRIMARY KEY;
ALTER TABLE db_nomadelfia.persone_posizioni DROP INDEX IF EXISTS  `posizione_id`;
ALTER table persone_posizioni ADD uuid MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY;

ALTER TABLE db_nomadelfia.persone_posizioni
 ADD CONSTRAINT `persone_posizioni_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
 ADD CONSTRAINT `persone_posizioni_ibfk_2` FOREIGN KEY (`posizione_id`) REFERENCES `posizioni` (`id`);