CREATE DATABASE IF NOT EXISTS medicare_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE medicare_system;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  cargo VARCHAR(80) NOT NULL,
  hospital VARCHAR(150) NOT NULL,
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ativo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS pacientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  cpf VARCHAR(14) NOT NULL UNIQUE,
  plano_saude VARCHAR(120) NOT NULL,
  leito VARCHAR(20) NOT NULL,
  data_internacao DATE NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Internado',
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  usuario_id INT NULL,
  CONSTRAINT fk_pacientes_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS medicamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  codigo VARCHAR(60) NOT NULL UNIQUE,
  categoria VARCHAR(80) NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  estoque INT NOT NULL DEFAULT 0,
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  usuario_id INT NULL,
  CONSTRAINT fk_medicamentos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS registros_uso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id INT NOT NULL,
  medicamento_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_total DECIMAL(10,2) NOT NULL,
  data_uso DATETIME NOT NULL,
  observacoes TEXT NULL,
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  usuario_id INT NULL,
  CONSTRAINT fk_registros_paciente
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_registros_medicamento
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
    ON DELETE RESTRICT,
  CONSTRAINT fk_registros_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS faturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id INT NOT NULL,
  valor_total DECIMAL(10,2) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Pendente',
  data_emissao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_pagamento DATETIME NULL,
  usuario_id INT NULL,
  UNIQUE KEY uniq_faturas_paciente (paciente_id),
  CONSTRAINT fk_faturas_paciente
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_faturas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
);
