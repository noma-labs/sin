
-- Aggioran tutte le data di entrata delle perosne conla data di nascita
UPDATE persone_categorie, persone
SET persone_categorie.data_inizio = persone.data_nascita
Where persone_categorie.persona_id = persone.id AND persone_categorie.categoria_id = 1 AND persone_categorie.stato = '1';


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