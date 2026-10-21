-- Run the migrations

-- The stato civile (celibe, nubile, sposato, vedovo) is derived from the sesso, the famiglia and the death of the coniuge.
-- persone_stati keeps only the stati that cannot be derived: SAC, MAV, MAM, MAN, SEP.

-- The date of the stato sposato becomes the marriage date of the famiglia (the earliest one if the spouses have different dates).
-- Only famiglie with both spouses (a vedova heading the famiglia is capofamiglia next to the deceased husband),
-- and only spouses who are capofamiglia or moglie of a single famiglia.
UPDATE famiglie
JOIN (
    SELECT famiglie_persone.famiglia_id, MIN(persone_stati.data_inizio) AS data_matrimonio
    FROM famiglie_persone
    JOIN persone_stati ON persone_stati.persona_id = famiglie_persone.persona_id
    JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
    WHERE famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
    AND famiglie_persone.persona_id IN (
        SELECT persona_id FROM famiglie_persone
        WHERE posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
        GROUP BY persona_id
        HAVING COUNT(*) = 1
    )
    AND EXISTS (
        SELECT 1 FROM famiglie_persone coniuge
        WHERE coniuge.famiglia_id = famiglie_persone.famiglia_id
        AND coniuge.persona_id <> famiglie_persone.persona_id
        AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
    )
    GROUP BY famiglie_persone.famiglia_id
) sposati ON sposati.famiglia_id = famiglie.id
SET famiglie.data_creazione = sposati.data_matrimonio;

-- The SAC, MAV, MAM, MAN and SEP rows are not touched, the closed ones included: they stay as history with their dates.
-- The class DeriveStatoCivile refuses to run this file if a stato sposato or vedovo cannot be derived from the famiglia.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato IN ('CEL', 'NUB', 'SPO', 'VED', 'CDE');

DELETE FROM stati WHERE stato IN ('CEL', 'NUB', 'SPO', 'VED', 'CDE');
