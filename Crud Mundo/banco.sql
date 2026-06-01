CREATE DATABASE bd_mundo;

USE bd_mundo;

CREATE TABLE paises (

    id_pais INT PRIMARY KEY AUTO_INCREMENT,

    nome VARCHAR(100) NOT NULL,

    idioma VARCHAR(100),

    moeda VARCHAR(100)

);