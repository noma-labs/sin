-- Run the migrations

-- The date a persona enters a famiglia while already in the comunità (e.g. a figlio accolto moved to another famiglia).
-- It is empty when the date is derived: the birth for a figlio nato, the data_creazione of the famiglia for a
-- capofamiglia or a moglie, the entrata in the comunità for the first famiglia of a figlio accolto or aggregato.
ALTER TABLE `famiglie_persone` ADD COLUMN `data_entrata` date NULL DEFAULT NULL AFTER `posizione_famiglia`;
