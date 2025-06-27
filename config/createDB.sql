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

ALTER TABLE usuario ADD telefone VARCHAR(20) NOT NULL;


