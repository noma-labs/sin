CREATE OR REPLACE VIEW v_colloc_split
AS select  `archivio_biblioteca`.`libro`.`collocazione` AS `COLLOCAZIONE`,substr(`archivio_biblioteca`.`libro`.`collocazione`,1,3) AS `lettere`,cast(substr(`archivio_biblioteca`.`libro`.`collocazione`,4,3) as unsigned) AS `numeri`
    from `archivio_biblioteca`.`libro`;
