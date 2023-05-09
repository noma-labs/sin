
CREATE TABLE IF NOT EXISTS  `categorie`(
    `id` int(1) NOT NULL,
    `categoria` varchar(200) NOT NULL,
    `descrizione` varchar(150) NOT NULL,
    `note` varchar(500) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `categorie`
--

INSERT INTO `categorie` (`id`, `categoria`, `descrizione`, `note`) VALUES
    (0, 'BS', 'SPECIALE', ''),
    (1, 'AM', 'ETÀ MINIMA RICHIESTA: 14 ANNI', ''),
    (2, 'A1', 'ETÀ MINIMA RICHIESTA: 16 ANNI', ''),
    (3, 'A2', 'ETÀ MINIMA RICHIESTA: 18 ANNI', ''),
    (4, 'A', 'CLASSIFICATA ANCHE COME PATENTE A3, È CONSEGUIBILE A DIVERSE ETÀ E CON DIFFERENTI MODALITÀ', ''),
    (5, 'B1', 'ETÀ MINIMA RICHIESTA: 16 ANNI. INTRODOTTA DAL 19 GENNAIO 2013', ''),
    (6, 'B', 'ETÀ MINIMA RICHIESTA: 18 ANNI.', ''),
    (7, 'B E', 'CONSEGUIBILE A 18 ANNI, ABILITA ALLA CONDUZIONE DI AUTOVEICOLI CONDUCIBILI CON LA PATENTE B', ''),
    (8, 'C1', 'CONSEGUIBILE A 18 ANNI (CON L\'OBBLIGO DI AVER CONSEGUITO LA PATENTE B)', ''),
(9, 'C1 E', 'ETÀ MINIMA RICHIESTA: 18 ANNI (CON L\'OBBLIGO DI AVER CONSEGUITO LA PATENTE DI CATEGORIA C1)', ''),
    (10, 'C', 'CONSEGUIBILE A 21 ANNI (CON OBBLIGO DI AVER CONSEGUITO LA PATENTE B)', ''),
    (11, 'C E', 'CONSEGUIBILE A 21 ANNI (CON OBBLIGO DI AVER CONSEGUITO LA PATENTE DI CATEGORIA C)', ''),
    (12, 'D1', 'ETÀ MINIMA RICHIESTA: 21 ANNI (CON L\'OBBLIGO DI AVER CONSEGUITO LA PATENTE DI CATEGORIA B)', ''),
(13, 'D', 'CONSEGUIBILE A 24 ANNI (CON OBBLIGO DI AVERE CONSEGUITO ALMENO LA PATENTE B)', ''),
(14, 'D E', 'CONSEGUIBILE A 24 ANNI ( CON L\'OBBLIGO DI AVER CONSEGUITO LA PATENTE DI CATEGORIA D)', ''),
    (16, 'C.Q.C. PERSONE', 'PER TRASPORTO PERSONE (IN VIGORE DAL 10/09/2008)', ''),
    (17, 'C.Q.C. MERCI', 'PER TRASPORTO MERCI (IN VIGORE DAL 10/09/2009)', ''),
    (18, 'D1 E', 'CONSEGUIBILE A 21 ANNI (CON OBBLIGO DI AVER CONSEGUITO LA PATENTE D1)', '');

--
-- Struttura della tabella `patenti_categorie`
--

CREATE TABLE `patenti_categorie` (
    `numero_patente` varchar(30) NOT NULL,
    `categoria_patente_id` int(11) NOT NULL,
    `data_rilascio` date DEFAULT NULL COMMENT 'campo 10 patente europea',
    `data_scadenza` date DEFAULT NULL COMMENT 'capo 11 patente europea',
    `note` varchar(200) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Struttura della tabella `persone_patenti`
--

CREATE TABLE `persone_patenti` (
    `persona_id` int(10) NOT NULL,
    `numero_patente` varchar(15) NOT NULL,
    `rilasciata_dal` varchar(100) NOT NULL,
    `data_rilascio_patente` date NOT NULL,
    `data_scadenza_patente` date NOT NULL,
    `stato` enum('commissione') DEFAULT NULL,
    `note` varchar(200) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE VIEW IF NOT EXISTS `v_clienti_patente`
AS select `db_nomadelfia`.`persone`.`id` AS `persona_id`,
       concat(`db_nomadelfia`.`persone`.`nome`,' ',`db_nomadelfia`.`persone`.`cognome`) AS `nome_cognome`,
       `db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,
       `db_nomadelfia`.`persone`.`nome` AS `nome`,
       `db_nomadelfia`.`persone`.`cognome` AS `cognome`,
       `db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,
       `db_nomadelfia`.`persone`.`provincia_nascita` AS `provincia_nascita`,
       (
           select distinct (case `persone_patenti`.`numero_patente` when '' then '' else 'CP ' end) from `persone_patenti`
           where ((`persone_patenti`.`numero_patente` is not null) and (`persone_patenti`.`persona_id` = `db_nomadelfia`.`persone`.`id`))
       ) AS `cliente_con_patente`
from `db_nomadelfia`.`persone`
join `db_nomadelfia`.`popolazione` on `db_nomadelfia`.`popolazione`.`persona_id` = `db_nomadelfia`.`persone`.`id`
where `db_nomadelfia`.`persone`.`data_nascita` <= (sysdate() - interval 180 year_month) and popolazione.data_uscita IS NULL;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `categorie`
--
ALTER TABLE `categorie`
    ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indici per le tabelle `patenti_categorie`
--
ALTER TABLE `patenti_categorie`
    ADD PRIMARY KEY (`categoria_patente_id`,`numero_patente`) USING BTREE,
    ADD KEY `numero_patente_persona` (`numero_patente`,`categoria_patente_id`) USING BTREE;

--
-- Indici per le tabelle `persone_patenti`
--
ALTER TABLE `persone_patenti`
    ADD PRIMARY KEY (`persona_id`,`numero_patente`,`data_rilascio_patente`,`data_scadenza_patente`) USING BTREE,
    ADD UNIQUE KEY `numero_patente_2` (`numero_patente`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `patenti_categorie`
--
ALTER TABLE `patenti_categorie`
    ADD CONSTRAINT `patenti_categorie_ibfk_1` FOREIGN KEY (`categoria_patente_id`) REFERENCES `categorie` (`id`);

--
-- Limiti per la tabella `persone_patenti`
--
ALTER TABLE `persone_patenti`
    ADD CONSTRAINT `persone_patenti_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `db_nomadelfia`.`persone` (`id`);

