CREATE TABLE `tipo_evento` (
  `id` int(10) PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descrizione` varchar(100),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

