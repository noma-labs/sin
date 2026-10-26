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

-- Celibe and nubile come from the sesso, vedovo from the death of the coniuge, the mamme and the celibe di elezione from the tipo of the famiglia:
-- only sacerdote and separato are left.
DELETE persone_stati FROM persone_stati
JOIN stati ON stati.id = persone_stati.stato_id
WHERE stati.stato IN ('CEL', 'NUB', 'VED', 'CDE', 'MAV', 'MAM', 'MAN');

DELETE FROM stati WHERE stato IN ('CEL', 'NUB', 'VED', 'CDE', 'MAV', 'MAM', 'MAN');
