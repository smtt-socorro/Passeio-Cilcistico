-- Schema público (parcial) — Sistema Evento Bike SMTT
-- Migrations complementares: 02_functions.sql, 03_triggers.sql, 04_admin_seed.sql

CREATE TYPE status_inscricao AS ENUM ('ativa', 'cancelada', 'excluida');
CREATE TYPE status_admin AS ENUM ('ativo', 'inativo');

CREATE TABLE IF NOT EXISTS controle_sequencia (
    id INT PRIMARY KEY,
    ultimo_numero INT NOT NULL DEFAULT 0
);

INSERT INTO controle_sequencia (id, ultimo_numero) VALUES (1, 0)
ON CONFLICT (id) DO NOTHING;

CREATE TABLE IF NOT EXISTS inscricoes (
    id SERIAL PRIMARY KEY,
    id_inscricao_formatado VARCHAR(20) NOT NULL UNIQUE,
    numero_sequencial_id INT NOT NULL,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    data_nascimento DATE NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100) DEFAULT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    aceita_termos BOOLEAN NOT NULL DEFAULT FALSE,
    data_inscricao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status status_inscricao NOT NULL DEFAULT 'ativa'
);

CREATE INDEX IF NOT EXISTS idx_inscricoes_cpf ON inscricoes (cpf);
CREATE INDEX IF NOT EXISTS idx_inscricoes_status ON inscricoes (status);
CREATE INDEX IF NOT EXISTS idx_inscricoes_data_inscricao ON inscricoes (data_inscricao);

CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status status_admin NOT NULL DEFAULT 'ativo',
    login_attempts INT NOT NULL DEFAULT 0,
    last_login_attempt TIMESTAMP DEFAULT NULL,
    last_login TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_admin_users_username ON admin_users (username);
CREATE INDEX IF NOT EXISTS idx_admin_users_status ON admin_users (status);

CREATE TABLE IF NOT EXISTS logs (
    id SERIAL PRIMARY KEY,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    data_log TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_logs_data_log ON logs (data_log);
