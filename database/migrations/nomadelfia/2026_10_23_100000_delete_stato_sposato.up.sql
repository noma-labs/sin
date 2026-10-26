-- Run the migrations

-- Sposato comes from the coniuge in the famiglia, and the marriage date is the data_creazione of the famiglia:
-- the class DeleteStatoSposato checks that the date of every stato sposato deleted is stored there.
-- The sposato/a without coniuge in the famiglia (the coniuge lives outside Nomadelfia) keeps the stato SPO, the only place of the matrimonio.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
WHERE EXISTS (
    SELECT 1 FROM famiglie_persone
    JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
        AND coniuge.persona_id <> famiglie_persone.persona_id
        AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
    WHERE famiglie_persone.persona_id = persone_stati.persona_id
    AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
);
