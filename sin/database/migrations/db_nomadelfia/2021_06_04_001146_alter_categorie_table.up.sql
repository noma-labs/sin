-- Run the migrations
ALTER TABLE db_nomadelfia.persone_categorie DROP FOREIGN KEY persone_categorie_ibfk_1;
ALTER TABLE db_nomadelfia.persone_categorie DROP FOREIGN KEY persone_categorie_ibfk_2;

ALTER table persone_categorie ADD uuid MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY;

ALTER TABLE `persone_categorie`
  ADD CONSTRAINT `persone_categorie_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
  ADD CONSTRAINT `persone_categorie_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorie` (`id`);
