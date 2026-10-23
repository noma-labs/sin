-- Run the migrations

-- The tipo of the famiglia says whether a capofamiglia without coniuge is married (CONIUGI, the coniuge lives outside Nomadelfia),
-- a mamma di vocazione or a mamma nubile: the stati MAV and MAN are derived from it.
ALTER TABLE famiglie
    ADD COLUMN tipo ENUM('CONIUGI', 'MAMMA DI VOCAZIONE', 'MAMMA NUBILE') NOT NULL DEFAULT 'CONIUGI' AFTER nome_famiglia;

-- The famiglia headed by a mamma di vocazione or a mamma nubile takes the tipo, and the date of the stato becomes its data_creazione.
-- Only active stati of capofamiglia of a single famiglia: the class DeriveMammeFromFamiglia logs the other ones.
UPDATE famiglie
JOIN (
    SELECT famiglie_persone.famiglia_id, stati.stato, MIN(persone_stati.data_inizio) AS data_inizio
    FROM persone_stati
    JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAN')
    JOIN famiglie_persone ON famiglie_persone.persona_id = persone_stati.persona_id
        AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
    WHERE persone_stati.stato = '1'
    AND persone_stati.persona_id IN (
        SELECT persona_id FROM famiglie_persone
        WHERE posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        GROUP BY persona_id
        HAVING COUNT(*) = 1
    )
    GROUP BY famiglie_persone.famiglia_id, stati.stato
) mamme ON mamme.famiglia_id = famiglie.id
SET famiglie.tipo = IF(mamme.stato = 'MAV', 'MAMMA DI VOCAZIONE', 'MAMMA NUBILE'),
    famiglie.data_creazione = mamme.data_inizio;

DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato IN ('MAV', 'MAN');

DELETE FROM stati WHERE stato IN ('MAV', 'MAN');
