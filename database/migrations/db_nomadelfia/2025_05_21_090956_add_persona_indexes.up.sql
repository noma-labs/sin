-- Needed by the join to get all the people appearing in a photo
CREATE INDEX idx_enrico_foto ON alfa_enrico_15_feb_23(FOTO);
CREATE INDEX idx_enrico_alias ON alfa_enrico_15_feb_23(ALIAS);


CREATE INDEX idx_persone_nome ON persone(nome);
CREATE INDEX idx_persone_cognome ON persone(cognome);
CREATE INDEX idx_persone_nominativo ON persone(nominativo);
