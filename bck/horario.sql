-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 04:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `horario`
--

-- --------------------------------------------------------

--
-- Table structure for table `aulas_agendadas`
--

CREATE TABLE `aulas_agendadas` (
  `id` int(11) NOT NULL,
  `tipo_aula_id` int(11) DEFAULT NULL,
  `uc_id` int(11) DEFAULT NULL,
  `dia_semana` enum('Seg','Ter','Qua','Qui','Sex','Sab','Dom') DEFAULT NULL,
  `turno` enum('Manhã','Tarde','Noite') DEFAULT NULL,
  `semana_inicio` date DEFAULT NULL,
  `cor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipos_aula`
--

CREATE TABLE `tipos_aula` (
  `id` int(11) NOT NULL,
  `sigla` char(3) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `cor` varchar(10) DEFAULT '#1a2041'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unidades_curriculares`
--

CREATE TABLE `unidades_curriculares` (
  `id` int(11) NOT NULL,
  `tipo_aula_id` int(11) DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aulas_agendadas`
--
ALTER TABLE `aulas_agendadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_aula_id` (`tipo_aula_id`),
  ADD KEY `fk_aulas_uc` (`uc_id`);

--
-- Indexes for table `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipos_aula`
--
ALTER TABLE `tipos_aula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Indexes for table `unidades_curriculares`
--
ALTER TABLE `unidades_curriculares`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aulas_agendadas`
--
ALTER TABLE `aulas_agendadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tipos_aula`
--
ALTER TABLE `tipos_aula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unidades_curriculares`
--
ALTER TABLE `unidades_curriculares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aulas_agendadas`
--
ALTER TABLE `aulas_agendadas`
  ADD CONSTRAINT `aulas_agendadas_ibfk_1` FOREIGN KEY (`tipo_aula_id`) REFERENCES `tipos_aula` (`id`),
  ADD CONSTRAINT `fk_aulas_uc` FOREIGN KEY (`uc_id`) REFERENCES `unidades_curriculares` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tipos_aula`
--
ALTER TABLE `tipos_aula`
  ADD CONSTRAINT `tipos_aula_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
