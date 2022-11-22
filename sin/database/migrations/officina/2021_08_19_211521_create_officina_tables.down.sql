SET FOREIGN_KEY_CHECKS=0;
DROP VIEW IF EXISTS `v_lavoratori_meccanica`;
DROP VIEW IF EXISTS `v_clienti_meccanica`;

-- DROP TABLE IF EXISTS `migrations`;

DROP TABLE IF EXISTS `alimentazione`;
DROP TABLE IF EXISTS `colori`;
DROP TABLE IF EXISTS `gomme_veicolo`;
DROP TABLE IF EXISTS `impiego`;
DROP TABLE IF EXISTS `incidenti`;

DROP TABLE IF EXISTS `libretto_circolazione`;
DROP TABLE IF EXISTS `manutenzione_programmata`;
DROP TABLE IF EXISTS `marca`;
DROP TABLE IF EXISTS `modello`;
DROP TABLE IF EXISTS `prenotazioni`;
DROP TABLE IF EXISTS `tipologia`;
DROP TABLE IF EXISTS `tipo_filtro`;
DROP TABLE IF EXISTS `tipo_gomme`;
DROP TABLE IF EXISTS `tipo_manutenzione`;
DROP TABLE IF EXISTS `tipo_olio`;
DROP TABLE IF EXISTS `usi`;
DROP TABLE IF EXISTS `veicolo`;


SET FOREIGN_KEY_CHECKS=1;
