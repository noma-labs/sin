-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 11, 2021 alle 16:31
-- Versione del server: 10.1.37-MariaDB
-- Versione PHP: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_anagrafe`
--
CREATE DATABASE IF NOT EXISTS `db_anagrafe` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_anagrafe`;

-- --------------------------------------------------------

--
-- Struttura della tabella `dati_personali`
--

CREATE TABLE `dati_personali` (
    `persona_id` int(10) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `cognome` varchar(100) NOT NULL,
    `sesso` enum('M','F') NOT NULL,
    `data_nascita` date NOT NULL,
    `provincia_nascita` varchar(60) DEFAULT NULL,
    `stato` enum('0','1') NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `dati_personali`
--
ALTER TABLE `dati_personali`
    ADD PRIMARY KEY (`persona_id`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `dati_personali`
--
ALTER TABLE `dati_personali`
    ADD CONSTRAINT `dati_personali_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `db_nomadelfia`.`persone` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
