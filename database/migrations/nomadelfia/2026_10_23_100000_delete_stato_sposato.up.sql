-- Run the migrations

-- The marriage date is the data_creazione of the famiglia: the class DeleteStatoSposato checks that every date of a stato sposato is stored there.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato = 'SPO';

DELETE FROM stati WHERE stato = 'SPO';
