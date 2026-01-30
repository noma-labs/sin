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

-- add referential integrity constraint linking photos.dbf_id to dbf_all.id
ALTER TABLE photos
ADD CONSTRAINT fk_photos_dbf_id FOREIGN KEY (dbf_id)
REFERENCES dbf_all(id);


-- procedure to connect the dbf_all entries to photos table based on a range of datnum values

-- add precopumted column to photos to speed up matching
ALTER TABLE photos
ADD COLUMN parsed_strip CHAR(6) AS (
    IF(LOCATE('-', file_name) > 0,
       LEFT(file_name, 5),
       LEFT(file_name, 6))
) STORED;

CREATE INDEX idx_parsed_strip ON photos(parsed_strip);

CREATE PROCEDURE update_photos_by_range (
    IN p_from_datnum INT,
    IN p_to_datnum   INT
)
BEGIN

    UPDATE photos p
    JOIN (
        WITH RECURSIVE seq AS (
            SELECT
                d.id,
                CAST(d.datnum AS UNSIGNED) + 1 AS val,
                CAST(d.anum AS UNSIGNED) AS max_val
            FROM dbf_all d
            WHERE d.source = 'foto'
              AND d.datnum REGEXP '^[0-9]+$'
              AND d.anum   REGEXP '^[0-9]+$'
              AND CAST(d.datnum AS UNSIGNED) < CAST(d.anum AS UNSIGNED)
              AND CAST(d.datnum AS UNSIGNED)
                    BETWEEN p_from_datnum AND p_to_datnum

            UNION ALL

            SELECT
                id,
                val + 1,
                max_val
            FROM seq
            WHERE val < max_val
        )
        SELECT
            id,
            LPAD(val, 5, '0') AS expanded_datnum
        FROM seq
    ) x on x.expanded_datnum = p.parsed_strip
    SET
        p.dbf_id = x.id,
        p.updated_at = NOW()
    WHERE p.dbf_id IS NULL
      AND p.directory NOT LIKE '%DIA%';

END;

-- updating photos
-- foto analogica:from 0-46106

-- CALL update_photos_by_range(600,615);
-- CALL update_photos_by_range(1000,2000); 151872
-- CALL update_photos_by_range(2000,3000); 149379
-- CALL update_photos_by_range(3000,5000); 144712
-- CALL update_photos_by_range(5000,10000); 131353
-- CALL update_photos_by_range(10000,20000); 99626
-- CALL update_photos_by_range(20000,40000); 43586
-- CALL update_photos_by_range(40000,50000); 31182


CREATE PROCEDURE update_slides_by_range (
    IN p_from_datnum INT,
    IN p_to_datnum   INT
)
BEGIN

    UPDATE photos p
    JOIN (
        WITH RECURSIVE seq AS (
            SELECT
                d.id,
                CAST(d.datnum AS UNSIGNED) AS val,
                CAST(d.anum AS UNSIGNED) AS max_val
            FROM dbf_all d
            WHERE d.source = 'slide'
              AND d.datnum REGEXP '^[0-9]+$'
              AND d.anum   REGEXP '^[0-9]+$'
              AND CAST(d.datnum AS UNSIGNED) <= CAST(d.anum AS UNSIGNED)
              AND CAST(d.datnum AS UNSIGNED)
                    BETWEEN p_from_datnum AND p_to_datnum

            UNION ALL

            SELECT
                id,
                val + 1,
                max_val
            FROM seq
            WHERE val < max_val
        )
        SELECT
            id,
            LPAD(val, 5, '0') AS expanded_datnum
        FROM seq
    ) x on x.expanded_datnum = p.parsed_strip
    SET
        p.dbf_id = x.id,
        p.updated_at = NOW()
    WHERE p.dbf_id IS NULL
      AND p.directory LIKE '%Dia 24x36%';

END;

-- count missing:
-- SELECT count(*) FROM `photos` WHERE `dbf_id` IS NULL AND `directory` LIKE '%DIA%' ORDER BY `dbf_id` DESC;
-- CALL update_slides_by_range(0,150);  3585
-- CALL update_slides_by_range(150,5000);  621
-- CALL update_slides_by_range(5000, 10200); 621

CREATE PROCEDURE update_dia120_by_range (
    IN p_from_datnum INT,
    IN p_to_datnum   INT
)
BEGIN

    UPDATE photos p
    JOIN (
        WITH RECURSIVE seq AS (
            SELECT
                d.id,
                CAST(d.datnum AS UNSIGNED)  AS val,
                CAST(d.anum AS UNSIGNED) AS max_val
            FROM dbf_all d
            WHERE d.source = 'dia120'
              AND d.datnum REGEXP '^[0-9]+$'
              AND d.anum   REGEXP '^[0-9]+$'
              AND CAST(d.datnum AS UNSIGNED) <= CAST(d.anum AS UNSIGNED)
              AND CAST(d.datnum AS UNSIGNED)
                    BETWEEN p_from_datnum AND p_to_datnum

            UNION ALL

            SELECT
                id,
                val + 1,
                max_val
            FROM seq
            WHERE val < max_val
        )
        SELECT
            id,
            LPAD(val, 5, '0') AS expanded_datnum
        FROM seq
    ) x on x.expanded_datnum = p.parsed_strip
    SET
        p.dbf_id = x.id,
        p.updated_at = NOW()
    WHERE p.dbf_id IS NULL
      AND p.directory LIKE '%DIA120%';

END;
-- SELECT count(*) FROM `photos` WHERE `dbf_id` IS NULL AND `directory` LIKE '%DIA120%' ORDER BY `dbf_id` DESC;
-- 428 missing entries before
-- CALL update_dia120_by_range(10000, 10550);  136

-- TODO: there is an overlapping of slides and dia120 in the range 10000-10200
-- - CALl update_slides_by_range(5000, 10200)
-- - CALL update_dia120_by_range(10000, 10550)
-- need to
-- - remove the dbd_id form the photos that are in the overlapping range
-- - re-run the two procedures for the overlapping range separately

-- UPDATE`photos`
-- SET dbf_id = NULL
-- WHERE `dbf_id` IS NOT NULL AND directory LIKE '%DIA%' and parsed_strip > '10000' and  parsed_strip < '10300'
-- ORDER BY `photos`.`file_name` DESC;

-- ALTER TABLE photos
-- ADD COLUMN parsed_strip_part2 CHAR(6);

-- UPDATE photos
-- SET parsed_strip_part2 =
--     CASE
--         -- if filename contains '-', take string after dash
--         WHEN LOCATE('-', file_name) > 0 THEN
--             CASE
--                 WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(LOWER(file_name), '.jpg', 1), '-', -1) REGEXP '^[0-9]+$'
--                 THEN SUBSTRING_INDEX(SUBSTRING_INDEX(LOWER(file_name), '.jpg', 1), '-', -1)
--                 ELSE NULL
--             END
--         -- else, take string from 7th character to dot, only if numeric
--         ELSE
--             CASE
--                 WHEN SUBSTRING_INDEX(SUBSTRING(LOWER(file_name), 7), '.jpg', 1) REGEXP '^[0-9]+$'
--                 THEN SUBSTRING_INDEX(SUBSTRING(LOWER(file_name), 7), '.jpg', 1)
--                 ELSE NULL
--             END
--     END;

-- CREATE INDEX idx_parsed_strip_part2 ON photos (parsed_strip_part2);

-- -- removed wrong mampping for anum like "10-20"
-- UPDATE photos p
-- JOIN dbf_all d ON p.dbf_id = d.id
-- SET p.dbf_id = NULL
-- WHERE d.anum LIKE '%-%'
--   AND p.dbf_id IS NOT NULL;

-- -- update dia120 photos with ranges in anum like "10-20"
-- UPDATE photos p
-- JOIN (
--     WITH RECURSIVE ranges AS (
--         SELECT
--             d.id,
--             d.datnum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', 1) AS UNSIGNED) AS anum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', -1) AS UNSIGNED) AS max_anum
--         FROM dbf_all d
--         WHERE d.anum REGEXP '^[0-9]+-[0-9]+$'
--           AND d.source = 'dia120'

--         UNION ALL

--         SELECT
--             id,
--             datnum,
--             anum + 1,
--             max_anum
--         FROM ranges
--         WHERE anum < max_anum
--     )
--     SELECT id, datnum, anum
--     FROM ranges
-- ) r
--   ON p.parsed_strip = r.datnum
--  AND CAST(REGEXP_REPLACE(p.parsed_strip_part2, '[^0-9]', '') AS UNSIGNED) = r.anum
-- SET
--     p.dbf_id = r.id,
--     p.updated_at = NOW()
-- WHERE p.dbf_id IS NULL
--   AND p.directory LIKE '%DIA120%';
-- -- 11 righe

-- UPDATE photos p
-- JOIN (
--     WITH RECURSIVE ranges AS (
--         SELECT
--             d.id,
--             d.datnum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', 1) AS UNSIGNED) AS anum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', -1) AS UNSIGNED) AS max_anum
--         FROM dbf_all d
--         WHERE d.anum REGEXP '^[0-9]+-[0-9]+$'
--           AND d.source = 'slide'

--         UNION ALL

--         SELECT
--             id,
--             datnum,
--             anum + 1,
--             max_anum
--         FROM ranges
--         WHERE anum < max_anum
--     )
--     SELECT id, datnum, anum
--     FROM ranges
-- ) r
--   ON p.parsed_strip = r.datnum
--  AND CAST(REGEXP_REPLACE(p.parsed_strip_part2, '[^0-9]', '') AS UNSIGNED) = r.anum
-- SET
--     p.dbf_id = r.id,
--     p.updated_at = NOW()
-- WHERE p.dbf_id IS NULL
--   AND p.directory LIKE '%Dia 24x36%';
-- --  501 righe modificate. (La query ha impiegato 0,5075 secondi.)

-- UPDATE photos p
-- JOIN (
--     WITH RECURSIVE ranges AS (
--         SELECT
--             d.id,
--             d.datnum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', 1) AS UNSIGNED) AS anum,
--             CAST(SUBSTRING_INDEX(d.anum, '-', -1) AS UNSIGNED) AS max_anum
--         FROM dbf_all d
--         WHERE d.anum REGEXP '^[0-9]+-[0-9]+$'
--           AND d.source = 'foto'

--         UNION ALL

--         SELECT
--             id,
--             datnum,
--             anum + 1,
--             max_anum
--         FROM ranges
--         WHERE anum < max_anum
--     )
--     SELECT id, datnum, anum
--     FROM ranges
-- ) r
--   ON p.parsed_strip = r.datnum
--  AND CAST(REGEXP_REPLACE(p.parsed_strip_part2, '[^0-9]', '') AS UNSIGNED) = r.anum
-- SET
--     p.dbf_id = r.id,
--     p.updated_at = NOW()
-- WHERE p.dbf_id IS NULL
--   AND p.directory NOT LIKE '%DIA%';
-- -- 3453 righe modificate. (La query ha impiegato 2,3726 secondi.)

