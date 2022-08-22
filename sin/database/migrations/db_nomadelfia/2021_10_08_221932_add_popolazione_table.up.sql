-- Run the migrations

CREATE TABLE `popolazione` (
    `persona_id` int(11),
    `data_entrata` date NOT NULL,
    `data_uscita` date DEFAULT NULL,
    `created_at` timestamp DEFAULT NOW(),
    `updated_at` timestamp DEFAULT NOW(),
    PRIMARY KEY (persona_id, data_entrata)
);
ALTER TABLE `popolazione` ADD CONSTRAINT `popolazione_persona_fk` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);


-- Persone uscite da nomadelfia
-- INSERT INTO popolazione(persona_id, data_entrata, data_uscita)
-- SELECT persone.id, persone_categorie.data_inizio as data_entrata, persone_categorie.data_fine as data_uscita
-- FROM persone
-- INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
-- WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND  persone_categorie.stato = '0' and persone_categorie.data_fine IS NOT NULL;
--
-- INSERT INTO popolazione(persona_id, data_entrata, data_uscita)
-- SELECT persone.id, persone_categorie.data_inizio as data_entrata, persone_categorie.data_fine as data_uscita
-- FROM persone
--          INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
-- WHERE persone_categorie.categoria_id = 1 AND persone.stato = '0' AND  persone_categorie.stato = '0' and persone_categorie.data_fine IS NOT NULL;
--
--
-- -- Persone entrare e in da nomadelfia
-- INSERT INTO popolazione(persona_id, data_entrata)
-- SELECT persone.id, persone_categorie.data_inizio as data_entrata
--  FROM persone
-- INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
--  WHERE persone_categorie.categoria_id = 1 AND persone.stato = '1' AND  persone_categorie.stato = '1'
-- -- --
