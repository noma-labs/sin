

-- come data string has bad format like '03/03/-1'
ALTER TABLE dbf_foto_enrico
ADD COLUMN data_ok DATE;
UPDATE dbf_foto_enrico
SET data_ok = STR_TO_DATE(
    CONCAT(
        SUBSTRING_INDEX(data, '/', 2),  -- month/day
        '/',
        CASE
            WHEN CAST(SUBSTRING_INDEX(data, '/', -1) AS UNSIGNED) BETWEEN 0 AND 25
                THEN CONCAT('20', LPAD(SUBSTRING_INDEX(data, '/', -1), 2, '0'))
            ELSE
                CONCAT('19', LPAD(SUBSTRING_INDEX(data, '/', -1), 2, '0'))
        END
    ),
    '%m/%d/%Y'
)
WHERE data REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{2}$';
ALTER TABLE dbf_foto_enrico
CHANGE COLUMN data data_orig VARCHAR(20),
CHANGE COLUMN data_ok data DATE;

ALTER TABLE dbf_dia120_enrico
ADD COLUMN IF NOT EXISTS data_orig VARCHAR(20);
UPDATE dbf_dia120_enrico
SET data_orig = data;
ALTER TABLE dbf_dia120_enrico
ADD COLUMN IF NOT EXISTS data_ok DATE;
UPDATE dbf_dia120_enrico
SET data_ok = STR_TO_DATE(data, '%Y-%m-%d')
WHERE data REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$';
ALTER TABLE dbf_dia120_enrico
CHANGE COLUMN data data_orig VARCHAR(20),
CHANGE COLUMN data_ok data DATE;

CREATE VIEW dbf_all AS

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
