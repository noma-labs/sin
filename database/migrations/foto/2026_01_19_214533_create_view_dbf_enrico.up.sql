-- change nfo to int
ALTER TABLE dbf_dia120_enrico
MODIFY COLUMN nfo INT;
-- treat empty strings as 0
UPDATE dbf_foto_enrico
SET nfo = 0
WHERE nfo = '';
ALTER TABLE dbf_foto_enrico
MODIFY COLUMN nfo INT;

ALTER TABLE dbf_slide_enrico
MODIFY COLUMN nfo INT;

-- create a unified table
CREATE TABLE dbf_all AS
SELECT
  'dia120' AS source,
  datnum,
  anum,
  cddvd,
  hdint,
  hdext,
  sc,
  fi,
  tp,
  nfo,
  data,
  localita,
  argomento,
  descrizione
FROM dbf_dia120_enrico

UNION ALL

SELECT
  'slide' AS source,
  datnum,
  anum,
  cddvd,
  hdint,
  hdext,
  sc,
  fi,
  tp,
  nfo,
  data,
  localita,
  argomento,
  descrizione
FROM dbf_slide_enrico

UNION ALL

SELECT
  'foto' AS source,
  datnum,
  anum,
  cddvd,
  hdint,
  hdext,
  sc,
  fi,
  tp,
  nfo,
  data,
  localita,
  argomento,
  descrizione
FROM dbf_foto_enrico;

ALTER TABLE dbf_all
ADD COLUMN id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

ALTER TABLE dbf_all
ADD COLUMN fingerprint BINARY(32) DEFAULT NULL AFTER id;

UPDATE dbf_all
SET fingerprint = UNHEX(SHA2(CONCAT_WS('|', source, datnum, anum, data), 256));

ALTER TABLE photos
ADD COLUMN dbf_id INT;

-- 42617 righe modificate. (La query ha impiegato 6,5896 secondi.)
UPDATE photos p
JOIN (
    -- slect only unique datnum entries to avoid ambiguous matches
    SELECT fingerprint, id, datnum
    FROM dbf_all
    WHERE source = 'dia120' or source = 'slide'
    GROUP BY fingerprint, id,  datnum
    HAVING COUNT(*) = 1
) d ON d.datnum = IF(LOCATE('-', p.file_name) > 0, LEFT(p.file_name, 5), LEFT(p.file_name, 6))
SET p.dbf_id = d.id
WHERE p.directory LIKE '%DIA%';

-- 224277 righe modificate. (La query ha impiegato 28,9701 secondi.)
UPDATE photos p
JOIN (
    SELECT fingerprint, id, datnum
    FROM dbf_all
    WHERE source = 'foto'
    GROUP BY fingerprint, id,  datnum
    HAVING COUNT(*) = 1
) d ON d.datnum = IF(LOCATE('-', p.file_name) > 0, LEFT(p.file_name, 5), LEFT(p.file_name, 6))
SET p.dbf_id = d.id
WHERE p.directory NOT LIKE '%DIA%';

-- add indexes to speed up future queries
ALTER TABLE photos
ADD INDEX idx_dbf_id (dbf_id);
