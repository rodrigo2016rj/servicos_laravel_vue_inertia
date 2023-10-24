-- -------------------------------------------------------------------------
-- banco_de_dados_servicos

DROP SCHEMA IF EXISTS banco_de_dados_servicos;

CREATE SCHEMA IF NOT EXISTS banco_de_dados_servicos 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE banco_de_dados_servicos;

-- -------------------------------------------------------------------------
-- Tabela imagem_do_imgur

DROP TABLE IF EXISTS imagem_do_imgur;

CREATE TABLE IF NOT EXISTS imagem_do_imgur(
  pk_imagem_do_imgur INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(60) NOT NULL,
  endereco VARCHAR(40) NOT NULL,
  momento_do_cadastro DATETIME NOT NULL,
  PRIMARY KEY (pk_imagem_do_imgur)
)
ENGINE = InnoDB;
