-- Run the migrations

CREATE TABLE `popolazione` (
   `persona_id` int(11) PRIMARY KEY,
   `data_entrata` date NOT NULL,
   `data_uscita` date DEFAULT NULL,
   `created_at` timestamp DEFAULT NOW(),
   `updated_at` timestamp DEFAULT NOW()
);

ALTER TABLE `popolazione` ADD CONSTRAINT `popolazione_persona_fk` FOREIGN KEY (`persona_id`) REFERENCES `persone` (`id`);
