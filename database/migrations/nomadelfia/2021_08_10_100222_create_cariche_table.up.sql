-- Run the migrations

-- Cariche costituzionali

CREATE TABLE `cariche` (
   `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT'Id carica',
   `nome` varchar(50) NOT NULL,
   `num`  int(10) NOT NULL DEFAULT 1 COMMENT'Numero di persone con questa carica',
   `org` enum('associazione','solidarieta','fondazione','agricola', 'culturale') NOT NULL,
   `descrizione` varchar(200) DEFAULT NULL COMMENT 'Descrizione',
   `ord`  int(10) NOT NULL COMMENT'ordine progressivo per ordinare la carica per ogni org',
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `persone_cariche` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
 `persona_id` int(10) NOT NULL COMMENT 'Id Persone',
 `cariche_id` int(10) NOT NULL COMMENT 'Id Posizione',
 `data_inizio` date NOT NULL COMMENT 'inizio posizione',
 `data_fine` date DEFAULT NULL COMMENT 'fine posizione'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `persone_cariche`
    ADD CONSTRAINT `persone_cariche_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`),
    ADD CONSTRAINT `persone_cariche_ibfk_2` FOREIGN KEY (`cariche_id`) REFERENCES `cariche` (`id`);