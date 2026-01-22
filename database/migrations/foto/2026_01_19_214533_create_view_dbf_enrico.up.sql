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
ADD COLUMN id VARCHAR(255);


UPDATE dbf_all
SET id = CONCAT_WS('|',source,datnum, anum, data);
