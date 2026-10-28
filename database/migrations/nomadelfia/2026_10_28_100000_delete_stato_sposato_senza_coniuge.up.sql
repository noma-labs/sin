-- Run the migrations

-- Sposato comes from the tipo coniugi of the famiglia, and the marriage date is the data_creazione of the famiglia:
-- the class DeleteStatoSposatoSenzaConiuge checks that the date of every stato sposato is stored there.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato = 'SPO';

DELETE FROM stati WHERE stato = 'SPO';
