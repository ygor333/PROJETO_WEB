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



CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    sts_cadastro ENUM('A','B') NOT NULL DEFAULT 'A', -- A = acesso liberado, B = acesso bloqueado
    tentativas_login TINYINT UNSIGNED NOT NULL DEFAULT 0,
    primeiro_acesso TINYINT(1) NOT NULL DEFAULT 1, -- 1 = precisa trocar a senha no próximo login
    dt_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;



CREATE TABLE logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    acao ENUM('login_sucesso','login_falha','bloqueio','desbloqueio','troca_senha','logout') NOT NULL,
    descricao VARCHAR(255) NULL,
    dt_acao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=INNODB;

-- Usuário inicial do sistema (linha de base do projeto).
-- Senha provisória: Mudar@123 — no primeiro login o sistema OBRIGA a troca.
INSERT INTO usuarios (nome, email, senha, sts_cadastro, tentativas_login, primeiro_acesso) VALUES
('Administrador', 'admin@crudmundo.com', '$2y$10$zI/qJOYSOW7D5Jspt6pVQeGBhsWqkNkPaPGfh84HNvesuCl4PkJXK', 'A', 0, 1);

