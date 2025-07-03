-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 11:14 PM
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
-- Database: `sistema_escuela`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumnos` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `DNI` int(10) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumnos`
--

INSERT INTO `alumnos` (`id_alumnos`, `nombre`, `apellido`, `DNI`, `fecha_nacimiento`, `telefono`, `curso`) VALUES
(2, 'sebastian ', 'Palacios', 48483434, '2015-07-19', '12345678', '');

-- --------------------------------------------------------

--
-- Table structure for table `materias`
--

CREATE TABLE `materias` (
  `id_materias` int(11) NOT NULL,
  `nombre_materia` varchar(50) NOT NULL,
  `curso` varchar(50) NOT NULL,
  `especialidad` varchar(50) NOT NULL,
  `temas` int(50) NOT NULL COMMENT 'buscar como gestionar pdf'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='completar materia_tecnica si la es materia de una especialid';

-- --------------------------------------------------------

--
-- Table structure for table `materias_previas`
--

CREATE TABLE `materias_previas` (
  `id_materias_previas` int(11) NOT NULL,
  `id_alumnos` int(11) NOT NULL,
  `id_materias` int(11) NOT NULL,
  `año` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) NOT NULL,
  `nombre_usuario` int(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fecha_entrada` date NOT NULL,
  `tipo_usuario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumnos`);

--
-- Indexes for table `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materias`);

--
-- Indexes for table `materias_previas`
--
ALTER TABLE `materias_previas`
  ADD PRIMARY KEY (`id_materias_previas`),
  ADD UNIQUE KEY `id_alumnos` (`id_alumnos`),
  ADD UNIQUE KEY `id_materias` (`id_materias`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumnos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materias_previas`
--
ALTER TABLE `materias_previas`
  MODIFY `id_materias_previas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `materias_previas`
--
ALTER TABLE `materias_previas`
  ADD CONSTRAINT `alumnosXprevias` FOREIGN KEY (`id_alumnos`) REFERENCES `alumnos` (`id_alumnos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materiasXprevias` FOREIGN KEY (`id_materias`) REFERENCES `materias` (`id_materias`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
