
-- Aggiunge per tutti i figli minorenni senso uno stato familiare
-- un nuovo stato (nubile o celibe) con la data di nascita
INSERT INTO persone_stati (persona_id, stato_id, stato, data_inizio)
SELECT persone.id,  CASE persone.sesso
                        WHEN 'F' THEN 5
                        WHEN 'M' THEN 2
                        ELSE NULL
    END, '1', persone.data_nascita
FROM persone
WHERE data_nascita >= DATE_SUB(NOW(), INTERVAL 18 YEAR) and persone.id NOT IN (
    SELECT persone_stati.persona_id
    from persone_stati
);