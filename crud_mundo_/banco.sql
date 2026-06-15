
CREATE DATABASE bd_mundo2;
USE bd_mundo2;

CREATE TABLE continentes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    populacao BIGINT,
    area DECIMAL(10,2),
    total_paises INT
);

CREATE TABLE governantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    partido VARCHAR(100),
    data_nascimento DATE,
    idade INT,
    inicio_mandato DATE,
    fim_mandato DATE
);

CREATE TABLE paises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    continente_id INT,
    governante_id INT,
    populacao BIGINT,
    area DECIMAL(10,2),
    idioma VARCHAR(100),
    clima VARCHAR(100),
    regime_politico VARCHAR(100),
    moeda VARCHAR(100),

    FOREIGN KEY (continente_id) REFERENCES continentes(id),
    FOREIGN KEY (governante_id) REFERENCES governantes(id)
);

CREATE TABLE cidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    pais_id INT,
    governante_id INT,
    populacao BIGINT,
    area DECIMAL(10,2),
    clima VARCHAR(100),
    data_fundacao DATE,

    FOREIGN KEY (pais_id) REFERENCES paises(id),
    FOREIGN KEY (governante_id) REFERENCES governantes(id)
);
