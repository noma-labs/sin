-- Run the migrations

-- A persona can be aggregato to a famiglia, next to the figli nati and accolti.
ALTER TABLE `famiglie_persone`
    MODIFY COLUMN `posizione_famiglia` enum('CAPO FAMIGLIA','MOGLIE','FIGLIO NATO','FIGLIO ACCOLTO','SINGLE','AGGREGATO') NOT NULL;
