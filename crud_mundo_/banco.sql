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
    token_recuperacao_hash CHAR(64) NULL, -- hash sha256 do token enviado ao usuário
    token_expira DATETIME NULL, -- validade do token de recuperação de senha
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

-- ------------------------------------------------------------
-- Dados de exemplo: continentes e países (valores aproximados)
-- governante_id fica NULL (a coluna permite); vincule depois pelo sistema.
-- ------------------------------------------------------------
INSERT INTO continentes (nome) VALUES
('América do Sul'),   -- id 1
('América do Norte'), -- id 2
('Europa'),           -- id 3
('Ásia'),             -- id 4
('África'),           -- id 5
('Oceania');          -- id 6

INSERT INTO paises (nome, continente_id, governante_id, populacao, area, idioma, clima, regime_politico, moeda) VALUES
('Brasil',        1, NULL,    203000000, 8515767, 'Português',                       'Tropical',            'República Federativa Presidencialista', 'Real'),
('Argentina',     1, NULL,     46000000, 2780400, 'Espanhol',                        'Temperado',           'República Federativa Presidencialista', 'Peso argentino'),
('Canadá',        2, NULL,     40000000, 9984670, 'Inglês e francês',                'Frio e temperado',    'Monarquia Constitucional Federal',      'Dólar canadense'),
('França',        3, NULL,     68000000,  643801, 'Francês',                         'Temperado oceânico',  'República Semipresidencialista',        'Euro'),
('Alemanha',      3, NULL,     84000000,  357022, 'Alemão',                          'Temperado',           'República Federativa Parlamentarista',  'Euro'),
('Japão',         4, NULL,    124000000,  377975, 'Japonês',                         'Temperado',           'Monarquia Constitucional',              'Iene'),
('Índia',         4, NULL,   1430000000, 3287263, 'Hindi e inglês',                  'Tropical monçônico',  'República Federativa Parlamentarista',  'Rupia indiana'),
('Egito',         5, NULL,    112000000, 1002450, 'Árabe',                           'Desértico',           'República Semipresidencialista',        'Libra egípcia'),
('África do Sul', 5, NULL,     60000000, 1221037, 'Vários (inglês, zulu, africâner)','Subtropical',         'República Parlamentarista',             'Rand'),
('Austrália',     6, NULL,     26000000, 7692024, 'Inglês',                          'Árido e tropical',    'Monarquia Constitucional Federal',      'Dólar australiano');
