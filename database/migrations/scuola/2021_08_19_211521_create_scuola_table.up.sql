CREATE TABLE `anno`
(
    `id`              int(10)     NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Anno (intero) è la chiave primaria',
    `responsabile_id` int(10)          DEFAULT NULL,
    `scolastico`      varchar(10) NOT NULL COMMENT 'Anno scolastico. E.g. 2018/2019',
    `descrizione`     varchar(100)     DEFAULT NULL,
    `data_inizio`     date        NOT NULL COMMENT 'Data inizio dell anno scolastico',
    `data_fine`       date             DEFAULT NULL COMMENT 'Data fine dell anno scolastico',
    `created_at`      timestamp   NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      timestamp   NULL DEFAULT NULL,
    CONSTRAINT unique_anno_scolastico UNIQUE (scolastico)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `tipo`
(
    `id`          int(10)                                                NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id univoco della classe in un anno scolastico',
    `nome`        varchar(50)                                            NOT NULL,
    `ciclo`       ENUM ('prescuola', 'elementari', 'medie', 'superiori', 'universita') NOT NULL DEFAULT 'superiori',
    `descrizione` varchar(100)                                                    DEFAULT NULL,
    `ord`         int(10)                                                NOT NULL COMMENT 'ordine progressivo per ordinare le classi',
    `next`        int(10)                                                NULL DEFAULT NULL COMMENT 'tipo di clsse successivo',
    `created_at`  timestamp                                              NULL     DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  timestamp                                              NULL     DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `classi`
(
    `id`         int(10)   NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `tipo_id`    int(10)   NOT NULL,
    `anno_id`    int(10)   NOT NULL COMMENT 'Anno scolastico di riferimento',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT NULL,
    CONSTRAINT unique_classe_as UNIQUE (tipo_id, anno_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `alunni_classi`
(
    `classe_id`   int(10)   NOT NULL,
    `persona_id`  int(10)   NOT NULL,
    `data_inizio` date      NOT NULL COMMENT 'Data inizio dell alunno nella classe',
    `data_fine`   date           DEFAULT NULL COMMENT 'Data fine dell alunno nella classe',
    `created_at`  timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  timestamp NULL DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE `coordinatori_classi`
(
    `classe_id`       int(10)   NOT NULL,
    `coordinatore_id` int(10)   NOT NULL,
    `tipo`            enum ('responsabile','coordinatore','collaboratore') DEFAULT 'coordinatore' NOT NULL,
    `data_inizio`     date      NOT NULL COMMENT 'Data inizio del coordiantore nella classe',
    `data_fine`       date                                                 DEFAULT NULL COMMENT 'Data fine del coordinatore nella classe',
    `created_at`      timestamp NULL                                       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      timestamp NULL                                       DEFAULT NULL,
    CONSTRAINT unique_coord UNIQUE (classe_id, coordinatore_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# NOTE: since there is a circular dependencies (popolazione_attuale.scuola -> scuola.alunni AND db_scuola.xx -> db_nomadelfia.persone)
# the foreign key of the db_scuola referencing the db_nomadelfia have been moved into the common folder

ALTER TABLE `alunni_classi`
    ADD FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `classi`
    ADD FOREIGN KEY (`tipo_id`) REFERENCES `tipo` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `classi`
    ADD FOREIGN KEY (`anno_id`) REFERENCES `anno` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `coordinatori_classi`
    ADD FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

CREATE INDEX classi_anni_idx ON classi (anno_id);
CREATE INDEX alunni_classi_idx ON alunni_classi (classe_id);
CREATE UNIQUE INDEX alunni_classi_unique ON alunni_classi (classe_id, persona_id, data_inizio);

