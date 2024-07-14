-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 28-06-2024 a las 04:40:21
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mysql`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id_tarea` int NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `tarea` text,
  `fecha` date DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `importancia` int DEFAULT NULL,
  `completada` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea`
--

INSERT INTO `tarea` (`id_tarea`, `id_usuario`, `tarea`, `fecha`, `categoria`, `importancia`, `completada`) VALUES
(10, 9, 'a', '2024-06-24', NULL, NULL, 0),
(11, 10, 'zx', '2024-06-24', NULL, NULL, 0),
(12, 8, 'aaaa', '2024-06-24', NULL, NULL, 0),
(13, 10, 'AAAAAAA', '2024-06-24', NULL, NULL, 0),
(14, 8, 'q', '2024-06-24', NULL, NULL, 1),
(17, 8, 'aa', '2024-06-24', NULL, NULL, 0),
(18, 11, 'dddd', '2024-06-24', NULL, NULL, 0),
(19, 11, 'ola', '2024-06-25', NULL, NULL, 0),
(39, 5, 'xc', '2024-01-03', NULL, NULL, 0),
(47, 12, 'sdxds', '2024-06-21', 'Trabajo', 2, 0),
(51, 12, 'tengo que hacer tarea', '2024-06-13', 'Trabajo', 2, 1),
(52, 12, 'nada', '2024-06-20', 'Trabajo', 2, 1),
(55, 12, 'Comer', '2024-06-12', 'Personal', 3, 1),
(56, 12, 'aaa', '2024-06-21', 'Personal', 2, 1),
(57, 7, 'aa', '2024-06-28', 'Trabajo', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `id_tarea` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `tarea_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `logins` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
