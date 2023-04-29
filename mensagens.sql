-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 29-Abr-2023 às 11:20
-- Versão do servidor: 5.5.54
-- versão do PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `sistema`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--

CREATE TABLE IF NOT EXISTS `mensagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remetente` varchar(100) NOT NULL,
  `receptor` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `unread` tinyint(4) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT '2023-04-26 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Extraindo dados da tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `remetente`, `receptor`, `mensagem`, `unread`, `timestamp`) VALUES
(32, 'gabriel', 'sara', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web', 0, '2023-04-26 16:17:48'),
(33, 'gabriel', 'mauricio', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using ''Content here, content here'', making it look like readable English. Many desktop publishing packages and web', 0, '2023-04-26 16:18:00'),
(34, 'mauricio', 'gabriel', 'Certo, pode deixar', 0, '2023-04-26 16:18:19'),
(35, 'mauricio', 'gabriel', 'trete', 0, '2023-04-27 09:00:03'),
(36, 'gabriel', 'mauricio', 'O que é isso?', 0, '2023-04-27 09:00:28'),
(37, 'mauricio', 'gabriel', 'Olá', 0, '2023-04-27 09:00:51'),
(38, 'gabriel', 'mauricio', 'Tudo certo?', 0, '2023-04-27 09:01:02'),
(39, 'mauricio', 'gabriel', 'Tudo', 0, '2023-04-27 09:01:07');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
