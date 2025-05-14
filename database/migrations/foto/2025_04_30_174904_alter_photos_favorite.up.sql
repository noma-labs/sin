-- -- -- Update region_info, description, location, taken_at for existing sha values
-- -- UPDATE photos
-- -- SET
-- --     region_info = VALUES(region_info),
-- --     description = VALUES(description),
-- --     location = VALUES(location),
-- --     taken_at = VALUES(taken_at)
-- -- WHERE sha IN (
-- --     '258e431fc3c21fb25567479e581eb8a5',
-- --     '01011d72764684bcfa00fdbfb689d953',
-- --     'f39a67c1d290d91231ca1c261611f529',
-- --     '389c7e8dd33718053132346abda95d1d',
-- --     '3ce199f8e60341e0d3b69a55fc93edf2',
-- --     'f70042220ba916129670318a2fdeda40',
-- --     'bd80136184cdb4e4c4140a304d9f48e6',
-- --     'baab799f9a3541c94f26f2ca5be3738a',
-- --     '1acc2ac83efd10d5e985ed1dadd9eded',
-- --     'd6a8ee6c87b2cff4fb97a8f2a40cec4d',
-- --     'ab87174317557eebd13981be1d503186',
-- --     'f2ff68b0a915e96e7890b900c590deea',
-- --     'd39c27bc2f7dab6ec1c121701fef5f08',
-- --     '40ebcf04049578ffab4d82d5d7ccf975',
-- --     'f8e906a5942b786c67d336328cee1a3a',
-- --     '015ad6f1aea1962a0c8a13555da27bc6',
-- --     'e59bffdccd6eb48cbf238bbe77e2a497',
-- --     'bbc43715116505f1cf9a774987799cf7',
-- --     '1f945f8af2425455e907ab7d3f91dea5',
-- --     'fd0935c95066a39790df2900d0591e4b',
-- --     '7186d6c6ff88c869555dbfcce8e16fec',
-- --     'c246d0cf76a2c6cef9ffd67784fcdab3',
-- --     '624b598a08fe279c0abddfedda60acdf',
-- --     '1e0a026678204d1a723ac7733eaca40c',
-- --     'fedd95c4f2f4ecb635b6bca772dd7c9f',
-- --     'ab04fb7dbfe291631254ea262fc4b315',
-- --     'bbb860c1c97064fa0c416f276ddf34c3',
-- --     '32e3e83ecbd4e366f2b39caefed76fb5',
-- --     '9bb1342bcc710bf38a0100294b79f5b7',
-- --     'c21d9198ac2ecc49f33f739bba05f503',
-- --     'd4b16340cbeb4373f46becc5a06ea6fb',
-- --     '7d6b721939d468854e8e34e5dce555e9',
-- --     'd85ea744ca8bfbb383fff69445bc3232',
-- --     '1f033464514a7546f962df13e1bc8f3e',
-- --     '90162efe27ebca9476c8b744f843d059',
-- --     'f92df085d90fca4619dbd7ac0c42c9a3',
-- --     '3ee540d1a3963c5a59f89322467ca005',
-- --     'c1150180ed84e36b7280a45cbfbd1afc',
-- --     '5e0f1c26365b595bfd5971762317a2e9',
-- --     'd0e94cccb882d005a293dfa4585d19a2',
-- --     'f524aa8d19950e8b3d2dbef0fe684dae',
-- --     'd46ec6f0bb33b7868e5e1b959918ea85',
-- --     '25ced7d323a78a040badde04bb9ffac0',
-- --     '6f3a4a9080ea6afdda3b22fdd8a3bb7e',
-- --     'd5ac818808bb15bea4fc6ac4044de13e',
-- --     'd5de76b857e08a8add09932000305b67',
-- --     '08cb3f46109b76a89d16300816c092be',
-- --     'bfd17c3ba9e3cf9acf5a561f64a8f4d2',
-- --     '1c20fa2fecf8c065597d66aed62aba48',
-- --     '6bc8e3013d7cb2535ae6108cf6866b3d',
-- --     '00d6d8aa30ba9bbbb549b768c520526e',
-- --     'af67de8380f5fe67c663f5e8b3ba952c',
-- --     'dc2b65254c1796438a46698f0fc19800',
-- --     '49a3a4a6724677057151de6787409cb7',
-- --     '75ad48419c5b560e554022bfb949ddeb',
-- --     'edc90b5e39bd4789858e6a52b9f553ce',
-- --     'ed13ed6c2650fbe74be25d082d59b866',
-- --     'a9333d8c57e5cdedaad07dcb2be67084',
-- --     '21e237cc821e6db2959d3b923967ddcc',
-- --     'c79ed8bcdc06dca4287152962c8e930a',
-- --     'f7ef36c786e8dc94ffba05d5471d8e51',
-- --     '91fb3d4ae679021441a6dd910ba78701',
-- --     'd888faf06063fbed718afd1702f651ab',
-- --     '2705faaad1817ea14fb217b261aa8117',
-- --     'c2caa3b79590cf5faf6350783d8da5a6',
-- --     '500c752512eb1533c6601eaa10026880',
-- --     '8bbc7ee2dabf71f7c4435df3940fd360',
-- --     '077b524f85420319926770b0688f269a',
-- --     '6cb15ef5d220ab560778d274935b1060',
-- --     '61ad259ecd62dd12626b12de926c20b6',
-- --     '9250ff183081c704c5def3cec1741f41',
-- --     '98c5c1f5ff5a47d347cf429894cf315b',
-- --     '63b9faa7934c269367fccc3b8a97ae69'
-- -- );

-- -- For each row, update the columns if sha exists (no INSERT, only UPDATE)
-- UPDATE photos SET
--     description = 'Da sinistra in alto: Maurizio Del Greco, Michele Neri, Giampaolo Ferrari, Francesco Marchese,  Emanuele Vergari,  Dario Mazelli, Massimo Belluzzi.\r\nDa sinistra in basso: Daniele Vergari, Giordano Belluzzi, Cristian Turri, Cristian, Marco Benzi Rossi,  Giovanni Fontana',
--     location = 'Grosseto',
--     taken_at = '1990-02-18 00:00:00'
-- WHERE sha = '258e431fc3c21fb25567479e581eb8a5';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1994-10-09 00:00:00'
-- WHERE sha = '01011d72764684bcfa00fdbfb689d953';

-- UPDATE photos SET
--     description = 'Marcello Galli \"dentro\" lo spogliatoio (una sùghera a bordo campo).',
--     location = 'Nomadelfia',
--     taken_at = '1975-12-07 00:00:00'
-- WHERE sha = 'f39a67c1d290d91231ca1c261611f529';

-- UPDATE photos SET
--     description = 'Costruzione spogliatoi campo di calcio utilizzando prefabbricato di gemona krivaia',
--     location = 'Nomadelfia',
--     taken_at = '1988-10-04 00:00:00'
-- WHERE sha = '389c7e8dd33718053132346abda95d1d';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1997-03-31 00:00:00'
-- WHERE sha = '3ce199f8e60341e0d3b69a55fc93edf2';

-- UPDATE photos SET
--     description = 'Incontro di calcio tra i giovani di nomadelfia e la primavera della cavese (0-2).\r\n\r\nDa sinistra in alto: Brunetto Zamperini (Brunello),?,Vincenzo Belluzzi (Didi), Carlo Malagoli (Carlino), ?, Maurizio Pivetta, Gaetano Critelli, Renato Buratti, Raffaele De Palma (Raoul Coleman).\r\nDa sinistra in basso: Tiziano Montorsi, Andrea Neri, Daniele Neri, Alessandro Santini (Sandro), Mauro Sensi.',
--     location = 'Cava dei Tirreni, Propaganda',
--     taken_at = '1981-08-08 00:00:00'
-- WHERE sha = 'f70042220ba916129670318a2fdeda40';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1955-01-01 00:00:00'
-- WHERE sha = 'bd80136184cdb4e4c4140a304d9f48e6';

-- UPDATE photos SET
--     description = 'Maurizio Tettamanti con l\'infermeria rientra negli spogliatoi',
--     location = 'Nomadelfia',
--     taken_at = '1991-11-17 00:00:00'
-- WHERE sha = 'baab799f9a3541c94f26f2ca5be3738a';

-- UPDATE photos SET
--     description = 'Scontro di gioco tra Germano (a sinistra) e Aldo Giangrande.(a destra)',
--     location = 'Nomadelfia',
--     taken_at = '1964-01-01 00:00:00'
-- WHERE sha = '1acc2ac83efd10d5e985ed1dadd9eded';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1998-01-11 00:00:00'
-- WHERE sha = 'd6a8ee6c87b2cff4fb97a8f2a40cec4d';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Giovanni Arcuri,  Luigi Mancone, Antonio Cavani, Franco, Silvano Pignatti.\r\nDa sinistra in basso: Fulvio Colombo (Vanni), Pavullo, Giuseppe Saputo,Francesco Mastrorilli (Franchino), Alfio Antonio Bruno (Antonio)',
--     location = 'Nomadelfia',
--     taken_at = '1966-07-01 00:00:00'
-- WHERE sha = 'ab87174317557eebd13981be1d503186';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2004-10-03 00:00:00'
-- WHERE sha = 'f2ff68b0a915e96e7890b900c590deea';

-- UPDATE photos SET
--     description = NULL,
--     location = 'Nomadelfia',
--     taken_at = '2006-04-17 00:00:00'
-- WHERE sha = 'd39c27bc2f7dab6ec1c121701fef5f08';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Antonio De Marchi,  Angelo Amorosi, Daniele Neri, Lino Neri, Tommaso Vergari, Marco Galli, Giuseppe Santolini (Beppe).\r\nDa sinistra in basso: Alessandro Santini (Sandro), Gaetano Critelli, Dzavit Ibraim (Davide), Bruno Giangrande, Franco Lanzini,  Renato Buratti, Antonio Zangari (Antonello), Riccardo Mecacci.',
--     location = 'Batignano',
--     taken_at = '1983-11-06 00:00:00'
-- WHERE sha = '40ebcf04049578ffab4d82d5d7ccf975';

-- UPDATE photos SET
--     description = 'Squadra con Nelusco (primo in seconda fila a partire da sinistra) e Sentimenti (IV) Lucidio (secondo giocatore in seconda fila da sinistra) detto anche \"Cochi\", portiere della Juventus e dell\'Italia',
--     location = 'S.Prospero (MO)',
--     taken_at = '1940-08-01 00:00:00'
-- WHERE sha = 'f8e906a5942b786c67d336328cee1a3a';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1998-01-11 00:00:00'
-- WHERE sha = '015ad6f1aea1962a0c8a13555da27bc6';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Luca Giangrande, Renato Buratti, Mauro Bozzola,  Angelo Galli, Bruno Giangrande, Gabriele Bertoni, Samuele Santolini.\r\nDa sinistra in basso: Riccardo Mecacci, Lucio Galli, Alessandro Carena, Marco Cavalieri, Paolo Miles, Domenico Saragoni (Nico).',
--     location = 'Nomadelfia',
--     taken_at = '1988-10-20 00:00:00'
-- WHERE sha = 'e59bffdccd6eb48cbf238bbe77e2a497';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Sergio Montorsi, Moreno Bianchini, Andrea Neri, Lino Neri, Mario Dalia (Mariolo), Antonio De Marchi,  Vincenzo Belluzzi (Didi), Bruno Giangrande, Gaetano Critelli, Stefano Montorsi.\r\nDa sinistra in basso: Aldo Cane\' (Aldino),  Mauro Sensi, Franco Lanzini, Alessandro Santini (Sandro), Daniele Neri, Dzavit Ibraim (Davide), Maurizio Pivetta,  Renato Buratti, Enrico.',
--     location = 'Batignano',
--     taken_at = '1982-10-03 00:00:00'
-- WHERE sha = 'bbc43715116505f1cf9a774987799cf7';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2003-05-25 00:00:00'
-- WHERE sha = '1f945f8af2425455e907ab7d3f91dea5';

-- UPDATE photos SET
--     description = '.',
--     location = 'Nomadelfia',
--     taken_at = '1988-10-22 00:00:00'
-- WHERE sha = 'fd0935c95066a39790df2900d0591e4b';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1995-01-29 00:00:00'
-- WHERE sha = '7186d6c6ff88c869555dbfcce8e16fec';

-- UPDATE photos SET
--     description = NULL,
--     location = 'Monte Antico (?)',
--     taken_at = '1986-10-01 00:00:00'
-- WHERE sha = 'c246d0cf76a2c6cef9ffd67784fcdab3';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Angelo Amorosi,  Daniele Neri,  Gabriele Sensi, Alessandro Santini (Sandro), Marco Galli, Stefano Montorsi.\r\nDa sinistra in basso: Lino Neri, Domenico Saragoni (Nico),  Flavio Tettamanti, Renato Buratti, Sabatino Zamperini (Chicco)',
--     location = 'Vetulonia',
--     taken_at = '1983-12-11 00:00:00'
-- WHERE sha = '624b598a08fe279c0abddfedda60acdf';

-- UPDATE photos SET
--     description = 'Partita di Calcio durante l\'intervallo. \r\nDaniele Neri (a sinistra).',
--     location = 'Diaccialone, Nomadelfia.',
--     taken_at = '1981-11-03 00:00:00'
-- WHERE sha = '1e0a026678204d1a723ac7733eaca40c';

-- UPDATE photos SET
--     description = 'In alto da sinistra: Ubaldo Giuliani, Alberto Ferrari (Albertino), Massimo Scapoccin,  Carlo Giangrande, Maurizio Antonelli, Antonio Walter Pirani, Luigi Buratti,  Eugenio Galardi.\r\nDa sinistra in basso: Adelfo Galli, Maurizio Poppi, Luigi Pivetta (Luigino), Vittorio, Mauro Peverini.',
--     location = 'Montelattaia',
--     taken_at = '1971-09-26 00:00:00'
-- WHERE sha = 'fedd95c4f2f4ecb635b6bca772dd7c9f';

-- UPDATE photos SET
--     description = 'Albero spogliatoio',
--     location = 'Nomadelfia',
--     taken_at = '1964-01-01 00:00:00'
-- WHERE sha = 'ab04fb7dbfe291631254ea262fc4b315';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1997-02-02 00:00:00'
-- WHERE sha = 'bbb860c1c97064fa0c416f276ddf34c3';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Tiziano Montorsi, Federico Carena, Franco Lanzini, Samuele Santolini, Gabriele Bertoni, Lino Neri, Angelo Galli, Alessandro Santini (Sandro), Livio Neri.\r\nDa sinistra in basso: Paolo Miles,  Riccardo Mecacci, Renato Buratti,  Gabriele Sensi, Daniele Neri,  Luca Giangrande, Andrea Neri,  Francesco Quartuccio, Lorenzo Neri.',
--     location = NULL,
--     taken_at = '1989-11-11 00:00:00'
-- WHERE sha = '32e3e83ecbd4e366f2b39caefed76fb5';

-- UPDATE photos SET
--     description = 'Da sinistra in altro: Tommaso Vergari, Brunetto Zamperini (Brunello), Flavio Tettamanti, Angelo Amorosi, Domenico Saragoni (Nico), Daniele Neri, Garbiele Sensi,  Maurizio Pivetta.\r\nDa sinistra in basso:  Maurizio Tettamanti, Riccardo Mecacci, Renato Buratti, Gaetano Critelli, Dzavit Ibraim (Davide), Mauro Sensi,  Lino Neri, Alessandro Santini (Sandro).',
--     location = 'Paganico',
--     taken_at = '1984-03-24 00:00:00'
-- WHERE sha = '9bb1342bcc710bf38a0100294b79f5b7';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1993-04-24 00:00:00'
-- WHERE sha = 'c21d9198ac2ecc49f33f739bba05f503';

-- UPDATE photos SET
--     description = 'Partita di calcio durante l\'intervallo.',
--     location = 'Diaccialone, Nomadelfia.',
--     taken_at = '1979-12-01 00:00:00'
-- WHERE sha = 'd4b16340cbeb4373f46becc5a06ea6fb';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1996-09-29 00:00:00'
-- WHERE sha = '7d6b721939d468854e8e34e5dce555e9';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1964-01-01 00:00:00'
-- WHERE sha = 'd85ea744ca8bfbb383fff69445bc3232';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2002-02-03 00:00:00'
-- WHERE sha = '1f033464514a7546f962df13e1bc8f3e';

-- UPDATE photos SET
--     description = 'NOMADELFIA SPORT FOTO DI GRUPPO SQUADRA DI NOMADELFIA. CAMPIONE PROVINCIALE.',
--     location = 'Batignano',
--     taken_at = '1959-01-01 00:00:00'
-- WHERE sha = '90162efe27ebca9476c8b744f843d059';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2004-02-29 00:00:00'
-- WHERE sha = 'f92df085d90fca4619dbd7ac0c42c9a3';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1972-10-01 00:00:00'
-- WHERE sha = '3ee540d1a3963c5a59f89322467ca005';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2005-10-09 00:00:00'
-- WHERE sha = 'c1150180ed84e36b7280a45cbfbd1afc';

-- UPDATE photos SET
--     description = 'Inversani Giuseppe (Iusfi - secondo da sinistra)',
--     location = 'Zambla Alta',
--     taken_at = '1950-01-01 00:00:00'
-- WHERE sha = '5e0f1c26365b595bfd5971762317a2e9';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Bernardo Mollicone (Bernardino), Giovanni Motta (Gianni),  Pietro Manfredi, Ugo Caselli, Giuseppe Santolini (Beppe), Giuseppe, \r\nDa sinistra in basso: Remigio Chesne, Lodovico Martini,  Renzo Pivetti, Sergio Montorsi, Sandro Napoli (Nani).',
--     location = 'Nomadelfia',
--     taken_at = '1965-01-01 00:00:00'
-- WHERE sha = 'd0e94cccb882d005a293dfa4585d19a2';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1963-06-01 00:00:00'
-- WHERE sha = 'f524aa8d19950e8b3d2dbef0fe684dae';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1997-03-16 00:00:00'
-- WHERE sha = 'd46ec6f0bb33b7868e5e1b959918ea85';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Mauro Bozzola, Giuseppe Santolini (Beppe), Renato Buratti, Maurizio Tettamanti, Angelo Galli, Franco Lanzini, Alessandro Santini (Sandro), Lino Neri, Samuele Santolini, Luca Giangrande, Livio Neri.\r\nDa sinistra in basso: ?, Daniele Neri, Salvatore Ferrari, Giovanni Carena, Gabriele Sensi, Lorenzo Neri, Federico Carena.',
--     location = 'Sorano',
--     taken_at = '1987-11-07 00:00:00'
-- WHERE sha = '25ced7d323a78a040badde04bb9ffac0';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1993-12-12 00:00:00'
-- WHERE sha = '6f3a4a9080ea6afdda3b22fdd8a3bb7e';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Ruggero Pintus,  Alessandro Santini (Sandro),  Lino Neri, Rezio Fornasari, Daniele Neri, Gabriele Sensi, Angelo Amorosi,  Domenico Saragoni (Nico), Mario Dalia (Mariolo),  Maurizio Tettamanti.\r\nDa sinistra in basso: Vincenzo Belluzzi (Didi), Gaetano Critelli, Dzavit Ibraim (Davide), Franco Lanzini, Renato Buratti, Marco Galli.',
--     location = 'Massa Marittima',
--     taken_at = '1983-10-30 00:00:00'
-- WHERE sha = 'd5ac818808bb15bea4fc6ac4044de13e';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1994-05-15 00:00:00'
-- WHERE sha = 'd5de76b857e08a8add09932000305b67';

-- UPDATE photos SET
--     description = 'Da sinistra in alto:Giuseppe Santolini (Beppe), Ugo,  Alberto Scillieri, Sebastiano Davitti, Fabio Nuti,  Michele Mazzocchi,  Daniele Bortolotto, Giovanni Carena.\r\nDal basso a sinistra:   Zeno Mazelli,  Samuele Montorsi Gessi,  Stefano Scifoni,  Egisto Corda (Manuel),  Terenzio Santolini,  Salvatore Tursi.',
--     location = 'Grosseto',
--     taken_at = '1990-02-18 00:00:00'
-- WHERE sha = '08cb3f46109b76a89d16300816c092be';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2008-03-16 00:00:00'
-- WHERE sha = 'bfd17c3ba9e3cf9acf5a561f64a8f4d2';

-- UPDATE photos SET
--     description = NULL,
--     location = 'Monte Antico (?)',
--     taken_at = '1986-10-01 00:00:00'
-- WHERE sha = '1c20fa2fecf8c065597d66aed62aba48';

-- UPDATE photos SET
--     description = 'Da sinistrain alto: Germano, Armando Bonfanti (Dino), Ulderico Tacchini,Virgilio Galli, Armando Bonfanti (Dino), Massimo.\r\nBelluzzi,Timothy Simon Grey.\r\nDa sinsitra in basso: Renato Trombetta, Pierluigi Ghinelli (Luigi), Mario Letili,Livio Neri.',
--     location = 'Nomadelfia',
--     taken_at = '1964-01-01 00:00:00'
-- WHERE sha = '6bc8e3013d7cb2535ae6108cf6866b3d';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1997-10-12 00:00:00'
-- WHERE sha = '00d6d8aa30ba9bbbb549b768c520526e';

-- UPDATE photos SET
--     description = 'Da sinistra in alto:  Stefano Montorsi,  Maurizio Tettamanti, Michele Neri, Lorenzo Neri, Andrea Neri, Daniele Neri, Daniele Bortolotto.\r\nDa sinistra in basso: Francesco Quartuccio, Domenico Saragoni (Nico), Federico Carena,  Paolo Miles,Alessandro Santini (Sandro),  Luca Giangrande.',
--     location = 'Paganico',
--     taken_at = '1990-12-22 00:00:00'
-- WHERE sha = 'af67de8380f5fe67c663f5e8b3ba952c';

-- UPDATE photos SET
--     description = 'Da sinistra in alto:Emma Paola Andrenucci (Paola), Marisa Montorsi, Serenella Zamperini, Chiara Giangrande,?,?,?,?,?\r\nDa sinistra in basso:  Grazia Firpo, Beatrice Santini,  Angela Mencuccini (Angelina), ?,?,?',
--     location = 'Pitigliano',
--     taken_at = '1972-02-01 00:00:00'
-- WHERE sha = 'dc2b65254c1796438a46698f0fc19800';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2000-10-08 00:00:00'
-- WHERE sha = '49a3a4a6724677057151de6787409cb7';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1991-12-26 00:00:00'
-- WHERE sha = '75ad48419c5b560e554022bfb949ddeb';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Livio Neri, Domenico Saragoni (Nico), Luca Giangrande, Alessandro Santini (Sandro), Lino Neri, Daniele Neri, Lorenzo Neri,  Francesco Quartuccio, Michele Neri,  Stefano Montorsi.\r\nDa sinistra in basso: Sebastiano Davitti, Federico Carena, Paolo Miles,  Lucio Galli,   Giovanni Carena,  Salvatore Tursi,  Samuele Santolini,  Maurizio Tettamanti.',
--     location = 'Nomadelfia',
--     taken_at = '1991-01-17 00:00:00'
-- WHERE sha = 'edc90b5e39bd4789858e6a52b9f553ce';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Livio Neri, Antonio De Marchi, Tommaso Vergari,  Alessandro Santini (Sandro),  Gabriele Sensi, Lino Neri,  Maurizio Pivetta, Angelo Amorosi, Stefano Montorsi, Giuseppe Santolini (Beppe).\r\nDal sinistra in basso: Daniele Neri, Renato Buratti, Dzavit Ibraim (Davide),  Domenico Saragoni (Nico), Sabatino Zamperini (Chicco), Marco Galli, Gaetano Critelli',
--     location = 'Ribolla',
--     taken_at = '1983-12-04 00:00:00'
-- WHERE sha = 'ed13ed6c2650fbe74be25d082d59b866';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Mario Dalia (Mariolo), Giuseppe Santolini (Beppe),Antonio De Marchi,  Moreno Bianchini, Rezio Fornasari,  Franco Lanzini, Gabriele Sensi, Bruno Giangrande, Stefano Fabbrini, Stefano Montorsi.\r\nDa sinistra in basso: Renato Buratti, Vincenzo Belluzzi (Didi), Alessandro Santini (Sandro), Daniele Neri, Gaetano Critelli,  Maurizio Pivetta, Aldo Cane\' (Aldino).',
--     location = 'Batignano',
--     taken_at = '1982-12-18 00:00:00'
-- WHERE sha = 'a9333d8c57e5cdedaad07dcb2be67084';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1993-10-03 00:00:00'
-- WHERE sha = 'd888faf06063fbed718afd1702f651ab';

-- UPDATE photos SET
--     description = 'Da sinistra in alto:  Sergio Montorsi, Marco Cavalieri, Massimiliano Parisi,  Mauro Bozzola,  Valerio Pintus, Pierluigi Spezza (Luigi),  Samuele Santolini, ?.\r\nDa sinistra in basso: Salvatore Ferrari, Paolo Alberto Farinelli (Paolo), Lucio Galli, ?, Federico Carena, Luca Giangrande, Luca Giannoni',
--     location = 'Scarlino',
--     taken_at = '1984-02-29 00:00:00'
-- WHERE sha = '2705faaad1817ea14fb217b261aa8117';

-- UPDATE photos SET
--     description = 'In alto a sinistra: Gabriele, Giuseppe Napoli (Pino), Giovanni Battista Bertoni Bacchi (Titta), Ermanno Bosi (Luciano), Pietro Riccio (Pietrino).\r\nDa sinistra in basso: Giovanni Negrisolo, Massimo Belluzzi, Ubaldo Giuliani, Gianni Tavernelli (Giovannino),  Giuseppe Mario Lo Petrone',
--     location = 'Nomadelfia',
--     taken_at = '1966-07-01 00:00:00'
-- WHERE sha = 'c2caa3b79590cf5faf6350783d8da5a6';

-- UPDATE photos SET
--     description = 'Costruzione spogliatoi campo di calcio utilizzando prefabbricato di gemona krivaia',
--     location = 'Nomadelfia',
--     taken_at = '1988-10-04 00:00:00'
-- WHERE sha = '500c752512eb1533c6601eaa10026880';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1960-01-01 00:00:00'
-- WHERE sha = '8bbc7ee2dabf71f7c4435df3940fd360';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '2001-10-07 00:00:00'
-- WHERE sha = '077b524f85420319926770b0688f269a';

-- UPDATE photos SET
--     description = 'Partita di calcio durante l\'intervallo.',
--     location = 'Diaccialone, Nomadelfia.',
--     taken_at = '1979-12-01 00:00:00'
-- WHERE sha = '6cb15ef5d220ab560778d274935b1060';

-- UPDATE photos SET
--     description = NULL,
--     location = NULL,
--     taken_at = '1960-01-01 00:00:00'
-- WHERE sha = '61ad259ecd62dd12626b12de926c20b6';

-- UPDATE photos SET
--     description = 'Da sinistra in alto: Pasquale Rea, Angelo Galli, Vincenzo Belluzzi (Didi), Mario Dalia (Mariolo), Angelo Amorosi, Andrea Neri, Brunetto Zamperini (Brunello), Massimiliano Parisi, Tommaso Vergari.\r\nDa sinistra in basso:  Bruno Giangrande, Domenico Saragoni (Nico), Alessandro Santini (Sandro),  Gabriele Sensi,  Renato Buratti',
--     location = 'Monte Antico',
--     taken_at = '1984-11-11 00:00:00'
-- WHERE sha = '9250ff183081c704c5def3cec1741f41';

-- UPDATE photos SET
--     description = 'Squadra di calcio. Da sinistra  Vando Rossi, Pietro Manfredi, Mirco, Romano Ontani, Carlo Maselli, Fausto pescetelli, Benito Pedrazzoli\r\nDa sinistra in basso: Azio Rossi, ?, Enrico Bernardi, ?',
--     location = 'Fossoli',
--     taken_at = '1949-01-01 00:00:00'
-- WHERE sha = '98c5c1f5ff5a47d347cf429894cf315b';

-- UPDATE photos SET
--     description = 'Da sinista in alto: Alessandro Santini (Sandro), Livio Neri, Luca Giangrande, Franco Lanzini, Angelo Galli, Sergio Montorsi, Gabriele Sensi, Daniele Neri,  Giuseppe Santolini (Beppe),  Samuele Santolini.\r\nDa sinistra in basso: Riccardo Mecacci, Alessandro Carena, Domenico Saragoni (Nico), Andrea Neri, Lino Neri',
--     location = 'Nomadelfia',
--     taken_at = '1989-01-07 00:00:00'
-- WHERE sha = '63b9faa7934c269367fccc3b8a97ae69';

-- --
