-- Reverse the migrations

-- The active stati MAV and MAN are rebuilt from the tipo of the famiglia; the closed ones are lost: restore them from a database dump.
INSERT INTO stati (stato, nome) VALUES
    ('MAV', 'MAMMA DI VOCAZIONE'),
    ('MAN', 'MAMMA NUBILE');

INSERT INTO persone_stati (persona_id, stato_id, data_inizio, stato)
SELECT famiglie_persone.persona_id, stati.id, famiglie.data_creazione, '1'
FROM famiglie
JOIN famiglie_persone ON famiglie_persone.famiglia_id = famiglie.id AND famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA'
JOIN stati ON stati.stato = IF(famiglie.tipo = 'MAMMA DI VOCAZIONE', 'MAV', 'MAN')
WHERE famiglie.tipo IN ('MAMMA DI VOCAZIONE', 'MAMMA NUBILE');

ALTER TABLE famiglie DROP COLUMN tipo;
