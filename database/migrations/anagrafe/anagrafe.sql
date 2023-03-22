
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `db_anagrafe`
--
CREATE DATABASE IF NOT EXISTS `db_anagrafe` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_anagrafe`;


CREATE TABLE `dati_personali` (
    `persona_id` int(10) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `cognome` varchar(100) NOT NULL,
    `sesso` enum('M','F') NOT NULL,
    `data_nascita` date NOT NULL,
    `provincia_nascita` varchar(60) DEFAULT NULL,
    `stato` enum('0','1') NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dati_personali`
    ADD PRIMARY KEY (`persona_id`);

--
--
ALTER TABLE `dati_personali`
    ADD CONSTRAINT `dati_personali_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `db_nomadelfia`.`persone` (`id`);
COMMIT;

