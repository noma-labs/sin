
CREATE TABLE `elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `titolo`            varchar(100) DEFAULT NULL,
    `autori`            varchar(10) NOT NULL,
    `data`              varchar(10) NOT NULL,
    `descrizione`       varchar(100) DEFAULT NULL,
    `collocazione`      varchar(100) NOT NULL,
    `file_name`         varchar(100) NOT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
