--
-- Struttura della tabella `classificazione`
--

DROP TABLE IF EXISTS `classificazione`;
CREATE TABLE `classificazione` (
  `id` int(11) NOT NULL,
  `descrizione` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- AUTO_INCREMENT per la tabella `classificazione`
--
ALTER TABLE `classificazione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Indici per le tabelle `classificazione`
--
ALTER TABLE `classificazione`
  ADD PRIMARY KEY (`id`,`descrizione`) USING BTREE,
  ADD UNIQUE KEY `descrizione_2` (`descrizione`),
  ADD KEY `descrizione` (`descrizione`);


