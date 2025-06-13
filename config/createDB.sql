create database cadastroFrete;

use cadastroFrete;

create table usuario (
    id int auto_increment primary key,
    nome varchar(40) not null,
    email varchar(50) not null unique,
    senha varchar(20) not null
);

-- Alterando senha para hash
ALTER TABLE usuario MODIFY COLUMN senha VARCHAR(255) NOT NULL;

-- Criar tabela de ofertas de frete
CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    descricao TEXT,
    tipo_carga VARCHAR(50),
    peso_maximo DECIMAL(8, 2), -- em kg
    data_disponivel DATE,
    prazo_entrega INT, -- dias
    status ENUM('ativa', 'pausada', 'finalizada') DEFAULT 'ativa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Chave estrangeira
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    
    -- Índices para melhor performance
    INDEX idx_origem (origem),
    INDEX idx_destino (destino),
    INDEX idx_preco (preco),
    INDEX idx_status (status),
    INDEX idx_data_disponivel (data_disponivel)
);

-- Inserir alguns dados de exemplo
INSERT INTO ofertas (usuario_id, origem, destino, preco, descricao, tipo_carga, peso_maximo, data_disponivel, prazo_entrega) VALUES
(1, 'São Paulo - SP', 'Rio de Janeiro - RJ', 150.00, 'Transporte de documentos e pequenas encomendas', 'Documentos', 50.00, '2025-06-15', 2),
(1, 'Brasília - DF', 'Goiânia - GO', 80.00, 'Frete para mudança residencial', 'Móveis', 500.00, '2025-06-20', 1),
(1, 'Belo Horizonte - MG', 'Salvador - BA', 300.00, 'Transporte de eletrônicos', 'Eletrônicos', 200.00, '2025-06-18', 3);

-- Tabela auxiliar para estados (opcional, para padronizar)
CREATE TABLE estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sigla CHAR(2) NOT NULL UNIQUE,
    nome VARCHAR(50) NOT NULL,
    regiao VARCHAR(20) NOT NULL
);

-- Inserir estados brasileiros
INSERT INTO estados (sigla, nome, regiao) VALUES
('AC', 'Acre', 'Norte'),
('AL', 'Alagoas', 'Nordeste'),
('AP', 'Amapá', 'Norte'),
('AM', 'Amazonas', 'Norte'),
('BA', 'Bahia', 'Nordeste'),
('CE', 'Ceará', 'Nordeste'),
('DF', 'Distrito Federal', 'Centro-Oeste'),
('ES', 'Espírito Santo', 'Sudeste'),
('GO', 'Goiás', 'Centro-Oeste'),
('MA', 'Maranhão', 'Nordeste'),
('MT', 'Mato Grosso', 'Centro-Oeste'),
('MS', 'Mato Grosso do Sul', 'Centro-Oeste'),
('MG', 'Minas Gerais', 'Sudeste'),
('PA', 'Pará', 'Norte'),
('PB', 'Paraíba', 'Nordeste'),
('PR', 'Paraná', 'Sul'),
('PE', 'Pernambuco', 'Nordeste'),
('PI', 'Piauí', 'Nordeste'),
('RJ', 'Rio de Janeiro', 'Sudeste'),
('RN', 'Rio Grande do Norte', 'Nordeste'),
('RS', 'Rio Grande do Sul', 'Sul'),
('RO', 'Rondônia', 'Norte'),
('RR', 'Roraima', 'Norte'),
('SC', 'Santa Catarina', 'Sul'),
('SP', 'São Paulo', 'Sudeste'),
('SE', 'Sergipe', 'Nordeste'),
('TO', 'Tocantins', 'Norte');