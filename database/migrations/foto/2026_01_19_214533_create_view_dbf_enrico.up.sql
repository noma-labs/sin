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
ADD COLUMN id VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


UPDATE dbf_all
SET id = CONCAT_WS('|', source, datnum, anum, data);

ALTER TABLE photos
ADD COLUMN dbf_id VARCHAR(255) DEFAULT NULL AFTER id;

-- 9256 righe modificate. (La query ha impiegato 3,6046 secondi.)
UPDATE photos p
JOIN (
    SELECT id, datnum
    FROM dbf_all
    WHERE source = 'dia120' or source = 'slide'
    GROUP BY id, datnum
    HAVING COUNT(*) = 1
) d ON d.datnum = IF(LOCATE('-', p.file_name) > 0, LEFT(p.file_name, 5), LEFT(p.file_name, 6))
SET p.dbf_id = d.id
WHERE p.directory LIKE '%DIA%';

-- 223766 righe modificate. (La query ha impiegato 46,7334 secondi.)
UPDATE photos p
JOIN (
    SELECT id, datnum
    FROM dbf_all
    WHERE source = 'foto'
    GROUP BY id, datnum
    HAVING COUNT(*) = 1
) d ON d.datnum = IF(LOCATE('-', p.file_name) > 0, LEFT(p.file_name, 5), LEFT(p.file_name, 6))
SET p.dbf_id = d.id
WHERE p.directory NOT LIKE '%DIA%';


--TODO it takes too much time.
ALTER TABLE photos
ADD INDEX idx_dbf_id (dbf_id);

ALTER TABLE dbf_all
ADD INDEX idx_id (id);
