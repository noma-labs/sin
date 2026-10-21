-- Run the migrations

-- The stato civile (celibe, nubile, sposato, vedovo) is derived from the sesso, the famiglia and the death of the coniuge.
-- persone_stati keeps only the stati that cannot be derived: SAC, MAV, MAM, MAN, SEP.

-- The date of the stato sposato becomes the marriage date of the famiglia (the earliest one if the spouses have different dates).
-- Only famiglie with a moglie, and only spouses who are capofamiglia or moglie of a single famiglia.
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
        SELECT 1 FROM famiglie_persone moglie
        WHERE moglie.famiglia_id = famiglie_persone.famiglia_id AND moglie.posizione_famiglia = 'MOGLIE'
    )
    GROUP BY famiglie_persone.famiglia_id
) sposati ON sposati.famiglia_id = famiglie.id
SET famiglie.data_creazione = sposati.data_matrimonio;

-- Reopen the stati closed only because a celibe/nubile/sposato/vedovo stato was assigned after them.
UPDATE persone_stati kept
JOIN stati kept_stato ON kept_stato.id = kept.stato_id AND kept_stato.stato IN ('SAC', 'MAV', 'MAM', 'MAN', 'SEP')
JOIN persone_stati derived ON derived.persona_id = kept.persona_id AND derived.data_inizio = kept.data_fine
JOIN stati derived_stato ON derived_stato.id = derived.stato_id AND derived_stato.stato IN ('CEL', 'NUB', 'SPO', 'VED', 'CDE')
JOIN persone ON persone.id = kept.persona_id AND persone.data_decesso IS NULL
LEFT JOIN persone_stati already_active ON already_active.persona_id = kept.persona_id AND already_active.stato_id = kept.stato_id AND already_active.stato = '1'
SET kept.stato = '1', kept.data_fine = NULL
WHERE kept.stato = '0' AND already_active.persona_id IS NULL;

DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato IN ('CEL', 'NUB', 'SPO', 'VED', 'CDE');

DELETE FROM stati WHERE stato IN ('CEL', 'NUB', 'SPO', 'VED', 'CDE');
