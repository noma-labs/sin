-- Struttura della tabella `categorie`

CREATE TABLE `categorie` (
  `id` int(10) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `descrizione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);


-- Struttura della tabella `persone_categorie`

CREATE TABLE `persone_categorie` (
  `persona_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date DEFAULT NULL,
  `stato` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indici per le tabelle `persone_categorie`
--
ALTER TABLE `persone_categorie`
  ADD UNIQUE KEY `persona_id` (`persona_id`,`categoria_id`,`stato`,`data_inizio`) USING BTREE,
  ADD KEY `categoria_id` (`categoria_id`);

--
-- RELAZIONI PER TABELLA `persone_categorie`:
--   `persona_id`
--       `persone` -> `id`
--   `categoria_id`
--       `categorie` -> `id`
ALTER TABLE `persone_categorie`
  ADD CONSTRAINT `persone_categorie_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
  ADD CONSTRAINT `persone_categorie_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorie` (`id`);


