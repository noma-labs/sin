-- Struttura della tabella `categorie`

CREATE TABLE `categorie` (
  `id` int(10) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `descrizione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
-- RELAZIONI PER TABELLA `persone_categorie`:
--   `persona_id`
--       `persone` -> `id`
--   `categoria_id`
--       `categorie` -> `id`
--