-- Run the migrations

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_foto`
--

--
-- Struttura della tabella `foto_enrico`
--

CREATE TABLE `foto_enrico` (
     `id` int(11) NOT NULL,
     `data` date NOT NULL,
     `datnum` varchar(10) NOT NULL,
     `localita` varchar(50) NOT NULL,
     `argomento` varchar(100) NOT NULL,
     `descrizione` varchar(200) NOT NULL,
     `anum` int(8) NOT NULL,
     `cddvd` varchar(10) NOT NULL,
     `hdint` varchar(10) NOT NULL,
     `hdext` varchar(10) NOT NULL,
     `sc` varchar(2) NOT NULL,
     `fi` varchar(2) NOT NULL,
     `tp` varchar(2) NOT NULL,
     `nfo` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `album`
--
-- ALTER TABLE `album`
--     ADD PRIMARY KEY (`data`,`datnum`),
--   ADD UNIQUE KEY `data` (`data`,`datnum`),
--   ADD KEY `localita` (`localita`),
--   ADD KEY `argomento` (`argomento`),
--   ADD KEY `descrizione` (`descrizione`),
--   ADD KEY `anum` (`anum`);
-- COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
