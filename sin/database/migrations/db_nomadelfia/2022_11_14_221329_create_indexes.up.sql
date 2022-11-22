--
-- Index vonvention
--     <prefix>_<table_name>_<column_name>…
-- The prefix indicates the index type. In MySQL, I prefer:
--     idx_  regular index
--     unq_  UNIQUE
--     ftx_  FULLTEXT
--     gis_  SPATIAL

CREATE INDEX  IF NOT EXISTS idx_persone_datadecesso ON persone (data_decesso);
ANALYZE TABLE persone;

CREATE INDEX  IF NOT EXISTS idx_popolazione_datauscita ON popolazione (data_uscita);
ANALYZE TABLE popolazione;

