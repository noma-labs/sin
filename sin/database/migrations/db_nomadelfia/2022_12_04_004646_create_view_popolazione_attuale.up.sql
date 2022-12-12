CREATE VIEW IF NOT EXISTS v_popolazione_attuale AS
    WITH pop AS (
    SELECT persone.id, persone.nominativo, persone.nome, persone.cognome, persone.data_nascita, persone.sesso, persone.provincia_nascita, persone.numero_elenco, popolazione.data_entrata
    FROM popolazione
    JOIN persone ON persone.id = popolazione.persona_id
    WHERE popolazione.data_uscita IS NULL AND persone.data_decesso IS NULL
    ORDER BY persone.nominativo
), posizione AS (
    SELECT persone_posizioni.persona_id, max(persone_posizioni.data_inizio) as posizione_inizio, posizioni.nome as posizione
    from persone_posizioni
    join posizioni ON posizioni.id = persone_posizioni.posizione_id
    WHERE persone_posizioni.persona_id IN (SELECT id from pop) AND persone_posizioni.stato = '1' OR persone_posizioni.data_fine IS NULL
    group by persone_posizioni.persona_id
), stato AS (
    SELECT persone_stati.persona_id, max(persone_stati.data_inizio) as stato_inizio, stati.nome as stato
    FROM persone_stati
    JOIN stati On stati.id = persone_stati.stato_id
    WHERE persone_stati.persona_id IN (SELECT id from pop)
    GROUP by persone_stati.persona_id
    ORDER BY persone_stati.data_inizio DESC
), gruppo As (
    select gruppi_persone.persona_id, max(gruppi_persone.data_entrata_gruppo) as gruppo_inizio, gruppi_familiari.nome as gruppo
    from gruppi_persone
    join gruppi_familiari on gruppi_familiari.id = gruppi_persone.gruppo_famigliare_id
    WHERE gruppi_persone.persona_id IN (SELECT id from pop)  AND gruppi_persone.stato = '1'
    GROUP by gruppi_persone.persona_id
    ORDER BY gruppi_persone.data_entrata_gruppo DESC
), famiglia AS (
    SELECT famiglie_persone.persona_id, max(famiglie_persone.data_entrata) as famiglia_inizio, famiglie.nome_famiglia as famiglia, famiglie_persone.posizione_famiglia
    FROM famiglie_persone
    JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id
    WHERE famiglie_persone.persona_id IN (SELECT id from pop)
    GROUP BY famiglie_persone.persona_id
    ORDER BY famiglie_persone.data_entrata DESC
), azienda AS (
    SELECT aziende_persone.persona_id, max(aziende_persone.data_inizio_azienda) as azienda_inizio, aziende.nome_azienda as azienda
    FROM aziende_persone
    JOIN aziende ON aziende.id = aziende_persone.azienda_id
    WHERE aziende_persone.persona_id IN (SELECT id from pop)
    GROUP BY aziende_persone.persona_id
    ORDER BY aziende_persone.data_inizio_azienda DESC
), scuola AS (
    SELECT db_scuola.alunni_classi.persona_id, max(db_scuola.alunni_classi.data_inizio) as scuola_inizio, db_scuola.tipo.nome as scuola
    FROM db_scuola.alunni_classi
    JOIN db_scuola.classi ON db_scuola.classi.id = db_scuola.alunni_classi.classe_id
    JOIN db_scuola.tipo ON db_scuola.tipo.id = db_scuola.classi.tipo_id
    WHERE  db_scuola.alunni_classi.persona_id IN (SELECT id from pop) AND db_scuola.alunni_classi.data_fine IS NULL
    GROUP BY db_scuola.alunni_classi.persona_id
    ORDER BY db_scuola.alunni_classi.data_inizio DESC
)
SELECT pop.*, posizione.posizione, posizione.posizione_inizio, stato.stato, gruppo.gruppo, famiglia.famiglia, famiglia.posizione_famiglia, azienda.azienda, scuola.scuola
FROM pop
         LEFT JOIN posizione ON posizione.persona_id = pop.id
         LEFT JOIN stato ON stato.persona_id = pop.id
         LEFT JOIN gruppo ON gruppo.persona_id = pop.id
         LEFT JOIN famiglia ON famiglia.persona_id = pop.id
         LEFT JOIN azienda ON azienda.persona_id = pop.id
         LEFT JOIN scuola ON scuola.persona_id = pop.id
ORDER BY pop.nominativo;