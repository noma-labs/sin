-- Run the migrations

-- The celibe di elezione heads a famiglia without coniuge, like a mamma: it becomes a tipo of the famiglia.
ALTER TABLE famiglie
MODIFY `tipo` enum('CON','MAV','MAM','MAN','CDE') NULL DEFAULT NULL COMMENT 'CON coniugi, MAV mamma di vocazione, MAM nomadelfa mamma, MAN mamma nubile, CDE celibe di elezione';

-- The class DeleteStatiDerivati refuses to run when a celibe di elezione is not capofamiglia of one famiglia without coniuge.
UPDATE famiglie
JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
JOIN persone_stati ON persone_stati.persona_id = famiglie_persone.persona_id AND persone_stati.stato = '1'
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'CDE'
SET famiglie.tipo = 'CDE'
WHERE famiglie.tipo IS NULL;

-- Vedovo comes from the death of the coniuge in the famiglia: only those stati VED are deleted.
-- The vedovo/a without coniuge in the famiglia (entered Nomadelfia already vedovo/a) keeps the stato VED, the only place of the vedovanza.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id AND stati.stato = 'VED'
WHERE EXISTS (
    SELECT 1 FROM famiglie_persone
    JOIN famiglie_persone coniuge ON coniuge.famiglia_id = famiglie_persone.famiglia_id
        AND coniuge.persona_id <> famiglie_persone.persona_id
        AND coniuge.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
    JOIN persone defunto ON defunto.id = coniuge.persona_id AND defunto.data_decesso IS NOT NULL
    WHERE famiglie_persone.persona_id = persone_stati.persona_id
    AND famiglie_persone.posizione_famiglia IN ('CAPO FAMIGLIA', 'MOGLIE')
);

-- Celibe and nubile come from the sesso, the mamme and the celibe di elezione from the tipo of the famiglia:
-- only sacerdote, separato and vedovo are left.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato IN ('CEL', 'NUB', 'CDE', 'MAV', 'MAM', 'MAN');

DELETE FROM stati WHERE stato IN ('CEL', 'NUB', 'CDE', 'MAV', 'MAM', 'MAN');
