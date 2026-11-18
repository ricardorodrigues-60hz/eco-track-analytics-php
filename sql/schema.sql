-- =============================================================================
-- EcoTrack Analytics — Definição do Esquema Relacional (DDL)
-- Stack: MariaDB / MySQL 8.0+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- Integridade Referencial: ON DELETE RESTRICT em todas as chaves estrangeiras
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '-03:00';

CREATE DATABASE IF NOT EXISTS ecotrack
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ecotrack;

-- -----------------------------------------------------------------------------
-- 1. Tabela: usuarios
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS coletas;
DROP TABLE IF EXISTS materiais;
DROP TABLE IF EXISTS categorias_materiais;
DROP TABLE IF EXISTS ecopontos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome             VARCHAR(120) NOT NULL,
    email            VARCHAR(180) NOT NULL UNIQUE,
    senha            VARCHAR(255) NOT NULL,
    perfil           ENUM('admin', 'coletor') NOT NULL DEFAULT 'coletor',
    ativo            TINYINT(1) NOT NULL DEFAULT 1,
    tentativas_login INT NOT NULL DEFAULT 0,
    bloqueado_ate    DATETIME NULL DEFAULT NULL,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Tabela: ecopontos
-- -----------------------------------------------------------------------------
CREATE TABLE ecopontos (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_unidade         VARCHAR(120) NOT NULL,
    endereco             VARCHAR(255) NOT NULL,
    capacidade_toneladas DECIMAL(10,2) NOT NULL,
    ativo                TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Tabela: categorias_materiais (Código CONAMA 275/2001)
-- -----------------------------------------------------------------------------
CREATE TABLE categorias_materiais (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(80) NOT NULL UNIQUE,
    cor_hex        CHAR(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Tabela: materiais
-- -----------------------------------------------------------------------------
CREATE TABLE materiais (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id      INT UNSIGNED NOT NULL,
    nome_material     VARCHAR(120) NOT NULL,
    fator_co2_kg      DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    fator_agua_litros DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fonte_referencia  TEXT NULL,
    CONSTRAINT fk_materiais_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias_materiais(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Tabela Fato: coletas
-- -----------------------------------------------------------------------------
CREATE TABLE coletas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    ecoponto_id INT UNSIGNED NOT NULL,
    material_id INT UNSIGNED NOT NULL,
    peso_kg     DECIMAL(10,3) NOT NULL,
    data_coleta DATE NOT NULL,
    observacoes TEXT NULL,
    ativo       TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_peso_positivo CHECK (peso_kg > 0),
    CONSTRAINT fk_coletas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_coletas_ecoponto
        FOREIGN KEY (ecoponto_id) REFERENCES ecopontos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_coletas_material
        FOREIGN KEY (material_id) REFERENCES materiais(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices de consulta e agregação para relatórios e dashboards
CREATE INDEX idx_coletas_data_coleta ON coletas(data_coleta);
CREATE INDEX idx_coletas_ecoponto    ON coletas(ecoponto_id);
CREATE INDEX idx_coletas_material    ON coletas(material_id);
CREATE INDEX idx_coletas_usuario     ON coletas(usuario_id);
CREATE INDEX idx_coletas_ativo       ON coletas(ativo);
