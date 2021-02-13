/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for locadora
CREATE DATABASE IF NOT EXISTS `locadora` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `locadora`;

-- Dumping structure for table locadora.documento
CREATE TABLE IF NOT EXISTS `documento` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_descricao` varchar(300) NOT NULL,
  `doc_data_vencimento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `doc_status` enum('Pendente','Concluído') NOT NULL DEFAULT 'Pendente',
  `doc_id_filial` int(11) NOT NULL,
  `doc_id_empresa` int(11) NOT NULL,
  `doc_id_veiculo` int(11) NOT NULL,
  `doc_flag` tinyint(1) NOT NULL DEFAULT '0',
  `doc_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doc_id`),
  KEY `doc_id_filial` (`doc_id_filial`),
  KEY `doc_id_empresa` (`doc_id_empresa`),
  KEY `doc_id_veiculo` (`doc_id_veiculo`),
  KEY `deletado` (`doc_deletado`),
  KEY `data_vencimento` (`doc_data_vencimento`),
  CONSTRAINT `doc_id_empresa` FOREIGN KEY (`doc_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doc_id_filial` FOREIGN KEY (`doc_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doc_id_veiculo` FOREIGN KEY (`doc_id_veiculo`) REFERENCES `veiculo` (`vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.empresa
CREATE TABLE IF NOT EXISTS `empresa` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_razao_social` varchar(300) DEFAULT NULL,
  `emp_nome_fantasia` varchar(300) NOT NULL,
  `emp_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`emp_id`),
  KEY `emp_deletado` (`emp_deletado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.filial
CREATE TABLE IF NOT EXISTS `filial` (
  `fil_id` int(11) NOT NULL AUTO_INCREMENT,
  `fil_razao_social` varchar(300) DEFAULT NULL,
  `fil_nome_fantasia` varchar(300) NOT NULL,
  `fil_telefone` varchar(300) DEFAULT NULL,
  `fil_id_empresa` int(11) NOT NULL,
  `fil_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fil_id`),
  KEY `deletado` (`fil_deletado`),
  KEY `fil_id_empresa` (`fil_id_empresa`),
  CONSTRAINT `fil_id_empresa` FOREIGN KEY (`fil_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.grupo_veiculo
CREATE TABLE IF NOT EXISTS `grupo_veiculo` (
  `gru_vei_id` int(11) NOT NULL AUTO_INCREMENT,
  `gru_vei_descricao` varchar(200) NOT NULL,
  `gru_vei_deletado` tinyint(1) NOT NULL DEFAULT '0',
  `gru_vei_id_empresa` int(11) NOT NULL,
  `gru_vei_id_filial` int(11) DEFAULT NULL,
  PRIMARY KEY (`gru_vei_id`),
  KEY `deletado` (`gru_vei_deletado`),
  KEY `gru_vei_id_empresa` (`gru_vei_id_empresa`),
  CONSTRAINT `gru_vei_id_empresa` FOREIGN KEY (`gru_vei_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.historico_posicao
CREATE TABLE IF NOT EXISTS `historico_posicao` (
  `his_pos_id` int(11) NOT NULL AUTO_INCREMENT,
  `his_pos_id_rastreador` int(11) NOT NULL,
  `his_pos_id_veiculo` int(11) NOT NULL,
  `his_pos_id_filial` int(11) NOT NULL,
  `his_pos_id_empresa` int(11) NOT NULL,
  `his_pos_localizacao` point NOT NULL,
  `his_pos_velocidade` float DEFAULT NULL,
  `his_pos_alerta` json DEFAULT NULL,
  `his_pos_data_gps` datetime NOT NULL,
  `his_pos_data_servidor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `his_pos_ignicao` tinyint(1) DEFAULT '0' COMMENT '0-ignicao desligada 1-ignicao ligada',
  `his_pos_status_gps` tinyint(1) DEFAULT '0' COMMENT '0-sem gps 1-com gps',
  `his_pos_status_gprs` tinyint(1) DEFAULT '0',
  `his_pos_bateria` int(11) DEFAULT '0' COMMENT 'porcentagem',
  PRIMARY KEY (`his_pos_id`),
  KEY `his_id_rastreador` (`his_pos_id_rastreador`),
  KEY `his_id_veiculo` (`his_pos_id_veiculo`),
  KEY `his_id_filial` (`his_pos_id_filial`),
  KEY `his_id_empresa` (`his_pos_id_empresa`),
  CONSTRAINT `his_id_empresa` FOREIGN KEY (`his_pos_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `his_id_filial` FOREIGN KEY (`his_pos_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `his_id_rastreador` FOREIGN KEY (`his_pos_id_rastreador`) REFERENCES `rastreador` (`ras_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `his_id_veiculo` FOREIGN KEY (`his_pos_id_veiculo`) REFERENCES `veiculo` (`vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.instalacao
CREATE TABLE IF NOT EXISTS `instalacao` (
  `ins_id` int(11) NOT NULL AUTO_INCREMENT,
  `ins_id_rastreador` int(11) NOT NULL,
  `ins_id_veiculo` int(11) NOT NULL,
  `ins_id_filial` int(11) DEFAULT NULL,
  `ins_id_empresa` int(11) NOT NULL,
  `ins_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ins_id`),
  KEY `deletado` (`ins_deletado`),
  KEY `ins_id_filial` (`ins_id_filial`),
  KEY `ins_id_empresa` (`ins_id_empresa`),
  KEY `ins_id_veiculo` (`ins_id_veiculo`),
  KEY `ins_id_rastreador` (`ins_id_rastreador`),
  CONSTRAINT `ins_id_empresa` FOREIGN KEY (`ins_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ins_id_filial` FOREIGN KEY (`ins_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ins_id_rastreador` FOREIGN KEY (`ins_id_rastreador`) REFERENCES `rastreador` (`ras_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ins_id_veiculo` FOREIGN KEY (`ins_id_veiculo`) REFERENCES `veiculo` (`vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.locacao
CREATE TABLE IF NOT EXISTS `locacao` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_data_locacao_agendada` datetime DEFAULT NULL,
  `loc_data_locacao` datetime DEFAULT NULL,
  `loc_data_devolucao_prevista` datetime DEFAULT NULL,
  `loc_data_devolucao` datetime DEFAULT NULL,
  `loc_limite_kilometragem` int(11) DEFAULT NULL,
  `loc_kilometragem_locacao` int(11) DEFAULT NULL,
  `loc_id_locatario` int(11) NOT NULL,
  `loc_id_veiculo` int(11) NOT NULL,
  `loc_id_filial` int(11) NOT NULL,
  `loc_id_empresa` int(11) NOT NULL,
  `loc_id_usuario` int(11) NOT NULL,
  `loc_data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `loc_data_alteracao` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `loc_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loc_id`),
  KEY `loc_id_veiculo` (`loc_id_veiculo`),
  KEY `loc_id_empresa` (`loc_id_empresa`),
  KEY `loc_id_filial` (`loc_id_filial`),
  KEY `loc_id_locatario` (`loc_id_locatario`),
  CONSTRAINT `loc_id_empresa` FOREIGN KEY (`loc_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `loc_id_filial` FOREIGN KEY (`loc_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `loc_id_locatario` FOREIGN KEY (`loc_id_locatario`) REFERENCES `locatario` (`lct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `loc_id_veiculo` FOREIGN KEY (`loc_id_veiculo`) REFERENCES `veiculo` (`vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.locatario
CREATE TABLE IF NOT EXISTS `locatario` (
  `lct_id` int(11) NOT NULL AUTO_INCREMENT,
  `lct_nome` varchar(300) NOT NULL,
  `lct_telefone` varchar(50) DEFAULT NULL,
  `lct_celular_principal` varchar(50) NOT NULL,
  `lct_celular_secundario` varchar(50) DEFAULT NULL,
  `lct_cpf` varchar(50) DEFAULT NULL,
  `lct_rg` varchar(50) DEFAULT NULL,
  `lct_email` varchar(255) DEFAULT NULL,
  `lct_id_filial` int(11) NOT NULL,
  `lct_id_empresa` int(11) NOT NULL,
  `lct_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lct_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.manutencao
CREATE TABLE IF NOT EXISTS `manutencao` (
  `man_id` int(11) NOT NULL AUTO_INCREMENT,
  `man_descricao` varchar(300) NOT NULL,
  `man_observacao` mediumtext,
  `man_kilometragem` int(11) DEFAULT NULL COMMENT 'KM',
  `man_data_vencimento` datetime DEFAULT CURRENT_TIMESTAMP,
  `man_flag` tinyint(1) NOT NULL DEFAULT '0',
  `man_status` enum('Pendente','Concluído') NOT NULL DEFAULT 'Pendente',
  `man_recorrente` tinyint(1) NOT NULL DEFAULT '0',
  `man_kilometragem_base_recorrente` int(11) DEFAULT NULL,
  `man_kilometragem_recorrente` int(11) DEFAULT NULL,
  `man_data_base_recorrente` datetime DEFAULT CURRENT_TIMESTAMP,
  `man_dias_recorrente` int(11) DEFAULT NULL,
  `man_id_veiculo` int(11) NOT NULL,
  `man_id_empresa` int(11) NOT NULL,
  `man_id_filial` int(11) NOT NULL,
  `man_data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `man_data_alteracao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `man_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`man_id`),
  KEY `man_deletado` (`man_deletado`),
  KEY `man_id_veiculo` (`man_id_veiculo`),
  KEY `man_id_empresa` (`man_id_empresa`),
  KEY `man_id_filial` (`man_id_filial`),
  CONSTRAINT `man_id_empresa` FOREIGN KEY (`man_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `man_id_filial` FOREIGN KEY (`man_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `man_id_veiculo` FOREIGN KEY (`man_id_veiculo`) REFERENCES `veiculo` (`vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.notificacao
CREATE TABLE IF NOT EXISTS `notificacao` (
  `not_id` int(11) NOT NULL AUTO_INCREMENT,
  `not_titulo` varchar(300) NOT NULL,
  `not_corpo` varchar(3000) NOT NULL,
  `not_lido` tinyint(1) NOT NULL DEFAULT '0',
  `not_data_ocorrencia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `not_data_leitura` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `not_id_usuario` int(11) DEFAULT NULL,
  `not_id_filial` int(11) DEFAULT NULL,
  `not_id_empresa` int(11) NOT NULL,
  `not_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`not_id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.oauth_clients
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `client_id` varchar(50) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(255) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.oauth_refresh_tokens
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_filial` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.oauth_tokens
CREATE TABLE IF NOT EXISTS `oauth_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_filial` int(11) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.perfil_item
CREATE TABLE IF NOT EXISTS `perfil_item` (
  `per_ite_id` int(11) NOT NULL AUTO_INCREMENT,
  `per_ite_descricao` varchar(300) NOT NULL,
  `per_ite_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`per_ite_id`),
  KEY `per_ite_deletado` (`per_ite_deletado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.perfil_item_x_veiculo_item
CREATE TABLE IF NOT EXISTS `perfil_item_x_veiculo_item` (
  `per_ite_x_vei_ite_id` int(11) NOT NULL AUTO_INCREMENT,
  `per_ite_x_vei_ite_id_perfil` int(11) NOT NULL,
  `per_ite_x_vei_ite_id_veiculo_item` int(11) NOT NULL,
  KEY `id` (`per_ite_x_vei_ite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.produto
CREATE TABLE IF NOT EXISTS `produto` (
  `pro_id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_descricao` varchar(50) NOT NULL,
  `pro_fabricante` varchar(100) NOT NULL,
  `pro_ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pro_id`),
  KEY `ativo` (`pro_ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.rastreador
CREATE TABLE IF NOT EXISTS `rastreador` (
  `ras_id` int(11) NOT NULL AUTO_INCREMENT,
  `ras_numero_serie` varchar(50) NOT NULL,
  `ras_numero_chip` varchar(50) DEFAULT NULL,
  `ras_id_filial` int(11) NOT NULL,
  `ras_id_empresa` int(11) NOT NULL,
  `ras_id_produto` int(11) NOT NULL,
  `ras_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ras_id`),
  KEY `deletado` (`ras_deletado`),
  KEY `ras_id_filial` (`ras_id_filial`),
  KEY `ras_id_empresa` (`ras_id_empresa`),
  KEY `ras_id_produto` (`ras_id_produto`),
  CONSTRAINT `ras_id_empresa` FOREIGN KEY (`ras_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ras_id_filial` FOREIGN KEY (`ras_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ras_id_produto` FOREIGN KEY (`ras_id_produto`) REFERENCES `produto` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.ultima_posicao
CREATE TABLE IF NOT EXISTS `ultima_posicao` (
  `ult_pos_id` int(11) NOT NULL AUTO_INCREMENT,
  `ult_pos_id_rastreador` int(11) NOT NULL,
  `ult_pos_id_veiculo` int(11) NOT NULL,
  `ult_pos_id_filial` int(11) NOT NULL,
  `ult_pos_id_empresa` int(11) NOT NULL,
  `ult_pos_localizacao` point NOT NULL,
  `ult_pos_velocidade` float DEFAULT NULL,
  `ult_pos_data_gps` datetime NOT NULL,
  `ult_pos_data_servidor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ult_pos_ignicao` tinyint(1) DEFAULT '0',
  `ult_pos_status_gps` tinyint(1) DEFAULT '0',
  `ult_pos_status_gprs` tinyint(11) DEFAULT '0',
  `ult_pos_bateria` int(11) DEFAULT '0',
  PRIMARY KEY (`ult_pos_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `usu_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_login` varchar(150) CHARACTER SET latin1 NOT NULL,
  `usu_senha` varchar(150) CHARACTER SET latin1 NOT NULL,
  `usu_nome` varchar(300) CHARACTER SET latin1 NOT NULL,
  `usu_email` varchar(300) CHARACTER SET latin1 NOT NULL,
  `usu_id_filial` int(11) DEFAULT NULL,
  `usu_id_empresa` int(11) NOT NULL,
  `usu_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usu_id`),
  KEY `usu_id_empresa` (`usu_id_empresa`),
  KEY `usu_id_filial` (`usu_id_filial`),
  CONSTRAINT `usu_id_empresa` FOREIGN KEY (`usu_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usu_id_filial` FOREIGN KEY (`usu_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.veiculo
CREATE TABLE IF NOT EXISTS `veiculo` (
  `vei_id` int(11) NOT NULL AUTO_INCREMENT,
  `vei_id_filial` int(11) NOT NULL,
  `vei_id_empresa` int(11) NOT NULL,
  `vei_id_grupo` int(11) NOT NULL,
  `vei_descricao` varchar(50) NOT NULL,
  `vei_placa` varchar(12) NOT NULL,
  `vei_chassi` varchar(50) DEFAULT NULL,
  `vei_renavam` varchar(50) DEFAULT NULL,
  `vei_cor` varchar(50) DEFAULT NULL,
  `vei_tipo_combustivel` enum('Etanol','Gasolina','Flex','Diesel','Gnv') DEFAULT NULL,
  `vei_modelo` varchar(200) DEFAULT NULL,
  `vei_ano_modelo` int(4) DEFAULT NULL,
  `vei_fabricante` varchar(200) DEFAULT NULL,
  `vei_ano_fabricacao` int(4) DEFAULT NULL,
  `vei_kilometragem` int(11) DEFAULT '0',
  `vei_data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vei_data_alteracao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vei_deletado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vei_id`),
  KEY `vei_deletado` (`vei_deletado`),
  KEY `vei_id_filial` (`vei_id_filial`),
  KEY `vei_id_empresa` (`vei_id_empresa`),
  KEY `vei_id_grupo` (`vei_id_grupo`),
  CONSTRAINT `vei_id_empresa` FOREIGN KEY (`vei_id_empresa`) REFERENCES `empresa` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vei_id_filial` FOREIGN KEY (`vei_id_filial`) REFERENCES `filial` (`fil_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vei_id_grupo` FOREIGN KEY (`vei_id_grupo`) REFERENCES `grupo_veiculo` (`gru_vei_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table locadora.veiculo_item
CREATE TABLE IF NOT EXISTS `veiculo_item` (
  `vei_ite_id` int(11) NOT NULL AUTO_INCREMENT,
  `vei_ite_descricao` varchar(50) NOT NULL,
  `vei_ite_ativo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vei_ite_id`),
  KEY `vei_ite_ativo` (`vei_ite_ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table locadora.veiculo_x_perfil_item
CREATE TABLE IF NOT EXISTS `veiculo_x_perfil_item` (
  `vei_x_per_ite_id` int(11) NOT NULL AUTO_INCREMENT,
  `vei_x_per_ite_id_veiculo` int(11) NOT NULL,
  `vei_x_per_ite_id_perfil_item` int(11) NOT NULL,
  PRIMARY KEY (`vei_x_per_ite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
