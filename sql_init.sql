create database edt;

use edt;

create table `elleve`(
    `id` int AUTO_INCREMENT,
    `nom` varchar(50) not null,
    `prenom` varchar(50) not null,
    `classe` varchar(50),
    `id_importation` int,
    primary key(`id`)
)ENGINE=InnoDB;

create table `proph`(
    `id` int AUTO_INCREMENT,
    `nom` varchar(50) not null,
    `prenom` varchar(50) not null,
    `id_importation` int,
    primary key(`id`)
)ENGINE=InnoDB;

create table `rdv`(
    `id` int AUTO_INCREMENT,
    `nom` varchar(50) not null,
    `date` timestamp not null,
    `durre` int not null,
    `couleur` varchar(50) not null,
    `id_elleve` int not null,
    `id_proph` int not null,
    `lieu` varchar(50) not null,
    `id_importation` int,
    CONSTRAINT uc_rdv unique(`date`, `id_elleve`),
    primary key(`id`),
    foreign key(`id_elleve`) references elleve(`id`),
    foreign key(`id_proph`) references proph(`id`)
)ENGINE=InnoDB;

create table `importation`(
    `id` int AUTO_INCREMENT,
    `date` timestamp not null,
)ENGINE=InnoDB;

SHOW TABLE STATUS;