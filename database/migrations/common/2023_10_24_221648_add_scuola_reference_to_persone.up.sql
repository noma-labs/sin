ALTER TABLE `alunni_classi`
    ADD FOREIGN KEY (`persona_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `anno`
    ADD FOREIGN KEY (`responsabile_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `coordinatori_classi`
    ADD FOREIGN KEY (`coordinatore_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
