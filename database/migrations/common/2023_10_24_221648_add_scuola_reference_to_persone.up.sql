-- Run the migrations

ALTER TABLE `db_scuola`.`alunni_classi`
    ADD FOREIGN KEY (`persona_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `db_scuola`.`anno`
    ADD FOREIGN KEY (`responsabile_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `db_scuola`.`coordinatori_classi`
    ADD FOREIGN KEY (`coordinatore_id`) REFERENCES `db_nomadelfia`.`persone` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
