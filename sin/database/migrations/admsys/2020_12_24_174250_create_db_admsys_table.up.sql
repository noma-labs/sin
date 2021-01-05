--
-- Database: `db_admsys`
--

-- CREATE DATABASE db_admsys  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `persona_id` int(11) UNSIGNED NOT NULL COMMENT 'Connette l''utente loggato con la persona nell''anagrafe',
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Struttura della tabella `risorse`
--

CREATE TABLE `risorse` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `sistema_id` int(10) NOT NULL,
  PRIMARY KEY (`id`,`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `ruoli`
--

CREATE TABLE `ruoli` (
  `id` int(10)  UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `sistemi`
--

CREATE TABLE `sistemi` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `ruoli_risorse_permessi`
--

CREATE TABLE `ruoli_risorse_permessi` (
  `ruolo_id` int(10) UNSIGNED NOT NULL,
  `risorsa_id` int(10) NOT NULL,
  `visualizza` tinyint(1) NOT NULL DEFAULT '0',
  `inserisci` tinyint(1) NOT NULL DEFAULT '0',
  `elimina` tinyint(1) NOT NULL DEFAULT '0',
  `modifica` tinyint(1) NOT NULL DEFAULT '0',
  `prenota` tinyint(1) NOT NULL DEFAULT '0',
  `esporta` tinyint(1) NOT NULL DEFAULT '0',
  `svuota` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_ruolo_risorsa` (`ruolo_id`,`risorsa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Struttura della tabella `utenti_ruoli`
--

CREATE TABLE `utenti_ruoli` (   
  `utente_id` int(10) UNSIGNED NOT NULL,
  `ruolo_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`utente_id`,`ruolo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Limiti per la tabella `risorse`
--
ALTER TABLE `risorse`
  ADD CONSTRAINT `risorsa_sistema_fk` FOREIGN KEY (`sistema_id`) REFERENCES `sistemi` (`id`);

--
-- Limiti per la tabella `ruoli_risorse_permessi`
--
ALTER TABLE `ruoli_risorse_permessi`
  ADD CONSTRAINT `risorsa_fk` FOREIGN KEY (`risorsa_id`) REFERENCES `risorse` (`id`),
  ADD CONSTRAINT `ruoli_risorse_permessi_ibfk_1` FOREIGN KEY (`ruolo_id`) REFERENCES `ruoli` (`id`);

--
-- Limiti per la tabella `utenti_ruoli`
--
ALTER TABLE `utenti_ruoli`
  ADD CONSTRAINT `ruolo_fk` FOREIGN KEY (`ruolo_id`) REFERENCES `ruoli` (`id`),
  ADD CONSTRAINT `utente_fk` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`) ON DELETE CASCADE;
  