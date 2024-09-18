CREATE OR REPLACE VIEW v_colloc_split
AS select  `archivio_biblioteca`.`libro`.`collocazione` AS `COLLOCAZIONE`,substr(`archivio_biblioteca`.`libro`.`collocazione`,1,3) AS `lettere`,cast(substr(`archivio_biblioteca`.`libro`.`collocazione`,4,3) as unsigned) AS `numeri`
    from `archivio_biblioteca`.`libro`;


CREATE OR REPLACE VIEW v_lavoratori_biblioteca
AS select `db_nomadelfia`.`aziende_persone`.`azienda_id` AS `azienda_id`,`db_nomadelfia`.`aziende_persone`.`persona_id` AS `persona_id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`aziende_persone`.`stato` AS `stato`,`db_nomadelfia`.`aziende_persone`.`mansione` AS `mansione`,`db_nomadelfia`.`aziende_persone`.`data_inizio_azienda` AS `data_inizio_azienda`,`db_nomadelfia`.`aziende_persone`.`data_fine_azienda` AS `data_fine_azienda`
    from (`db_nomadelfia`.`aziende_persone` join `db_nomadelfia`.`persone`)
    where ((`db_nomadelfia`.`aziende_persone`.`azienda_id` = 30) and (`db_nomadelfia`.`persone`.`id` = `db_nomadelfia`.`aziende_persone`.`persona_id`))
    order by `db_nomadelfia`.`aziende_persone`.`mansione`;
