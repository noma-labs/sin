--
CREATE INDEX  IF NOT EXISTS idx_persone_datadecesso ON persone (data_decesso);
ANALYZE TABLE persone;

CREATE INDEX  IF NOT EXISTS idx_popolazione_datauscita ON popolazione (data_uscita);
ANALYZE TABLE popolazione;

