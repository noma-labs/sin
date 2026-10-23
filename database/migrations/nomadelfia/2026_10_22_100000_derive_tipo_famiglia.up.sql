-- Run the migrations

-- Only the famiglie without tipo: a tipo set by hand is kept. Each step skips the famiglie a previous step already set.

-- The famiglie with both coniugi, also a vedova heading the famiglia next to the deceased husband.
UPDATE famiglie
SET tipo = 'CON'
WHERE tipo IS NULL
AND id IN (
    SELECT famiglia_id FROM famiglie_persone
    WHERE posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
    GROUP BY famiglia_id
    HAVING COUNT(DISTINCT persona_id) > 1
);

-- The famiglie headed by a mamma: the active stato first, then the closed one (e.g. closed at the death of the mamma).
-- The class DeriveTipoFamiglia refuses to run when a mamma has more than one stato or more than one famiglia.
UPDATE famiglie
JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
JOIN persone_stati ON persone_stati.persona_id = famiglie_persone.persona_id AND persone_stati.stato = '1'
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
SET famiglie.tipo = stati.stato
WHERE famiglie.tipo IS NULL;

UPDATE famiglie
JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
JOIN persone_stati ON persone_stati.persona_id = famiglie_persone.persona_id AND persone_stati.stato = '0'
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato IN ('MAV', 'MAM', 'MAN')
SET famiglie.tipo = stati.stato
WHERE famiglie.tipo IS NULL;

-- The famiglie with the capofamiglia alone who is sposato/a: the coniuge lives outside Nomadelfia.
UPDATE famiglie
JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
JOIN persone_stati ON persone_stati.persona_id = famiglie_persone.persona_id
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'SPO'
SET famiglie.tipo = 'CON'
WHERE famiglie.tipo IS NULL;
