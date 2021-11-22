-- Run the migrations

CREATE TABLE `alunno_elaborati`
(
    `id`                int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `alunno_id`         int(10) NOT NULL,
    `anno_scolastico`   varchar(10) NOT NULL COMMENT 'Anno scolastico. E.g. 2018/2019',
    `nome`              varchar(100) DEFAULT NULL,
    `descrizione`       varchar(100) DEFAULT NULL,
    `created_at`        timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8;