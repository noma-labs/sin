CREATE TABLE `popolazione` (
       `persona_id` int(11) PRIMARY KEY,
       `data_entrata` date NOT NULL,
       `data_uscita` date DEFAULT NULL,
       `created_at` timestamp  DEFAULT NOW(),
       `updated_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE `popolazione`
    ADD CONSTRAINT `popolazione_persona_fk` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);


-- Inserisce le persone della popolazione attuale
INSERT INTO popolazione (persona_id, data_entrata, data_uscita)
SELECT persone.id, persone_categorie.data_inizio, persone_categorie.data_fine
FROM persone
INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' and persone_categorie.stato = '1';


-- inserisce le persone uscite da nomadelfia
-- aggiorna la data di uscita se la persona è presente
INSERT INTO popolazione (persona_id, data_entrata, data_uscita)
SELECT persone.id,  persone_categorie.data_inizio, persone_categorie.data_fine
FROM persone
INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
WHERE persone_categorie.categoria_id = 4 AND persone.stato = '0' and persone_categorie.stato = '0'
ON DUPLICATE KEY UPDATE popolazione.data_uscita = persone_categorie.data_inizio;