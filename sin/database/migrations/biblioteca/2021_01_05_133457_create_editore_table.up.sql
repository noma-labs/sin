--
-- Struttura della tabella `editore`
--

DROP TABLE IF EXISTS `editore`;
CREATE TABLE `editore` (
  `id` int(10) NOT NULL,
  `editore` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipedi` enum('S','V','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- AUTO_INCREMENT per la tabella `editore`
--
ALTER TABLE `editore`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;


--
-- Indici per le tabelle `editore`
--
ALTER TABLE `editore`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `editore` (`editore`),
  ADD KEY `tipedi` (`tipedi`);


--
-- Struttura della tabella `editore_libro`
--

DROP TABLE IF EXISTS `editore_libro`;
CREATE TABLE `editore_libro` (
  `editore_id` int(10) NOT NULL,
  `libro_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indici per le tabelle `editore_libro`
--
ALTER TABLE `editore_libro`
  ADD PRIMARY KEY (`libro_id`,`editore_id`) USING BTREE,
  ADD KEY `editore_id` (`editore_id`),
  ADD KEY `libro_id` (`libro_id`);
