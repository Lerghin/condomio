- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2025 a las 21:45:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `condominio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_gastos`
--

CREATE TABLE `detalles_gastos` (
  `id` int(11) NOT NULL,
  `recibo_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `imagen_ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_gastos`
--

INSERT INTO `detalles_gastos` (`id`, `recibo_id`, `descripcion`, `monto`, `imagen_ruta`) VALUES
(1, 5, 'pintura', 27.00, 'uploads/gastos/th.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deudas_residentes`
--

CREATE TABLE `deudas_residentes` (
  `id` int(11) NOT NULL,
  `residente_id` int(11) NOT NULL,
  `recibo_id` int(11) NOT NULL,
  `monto_deuda` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `deudas_residentes`
--

INSERT INTO `deudas_residentes` (`id`, `residente_id`, `recibo_id`, `monto_deuda`) VALUES
(1, 1, 5, -17.00),
(2, 2, 5, 10.00),
(3, 3, 5, 10.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `unidad` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `monto_bs` decimal(10,2) NOT NULL,
  `referencia` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `unidad`, `fecha`, `nombre`, `apellido`, `cedula`, `monto`, `monto_bs`, `referencia`, `tipo`) VALUES
(1, '1a', '2025-02-12', 'TITO JOSE', 'GARCIA BELLO', '15587040', 27.00, 1647.75, '123456', 'Bs Efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recibos_mensuales`
--

CREATE TABLE `recibos_mensuales` (
  `id` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `anno` int(11) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `monto_total_bs` decimal(10,2) NOT NULL,
  `imagen_ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recibos_mensuales`
--

INSERT INTO `recibos_mensuales` (`id`, `mes`, `anno`, `monto_total`, `monto_total_bs`, `imagen_ruta`) VALUES

(5, 'Enero', 2025, 30.00, 1830.83, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residentes`
--

CREATE TABLE `residentes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `unidad` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `residentes`
--

INSERT INTO `residentes` (`id`, `nombre`, `apellido`, `cedula`, `unidad`, `telefono`) VALUES
(1, 'TITO JOSE', 'GARCIA BELLO', '15587040', '1a', '04244316376'),
(2, 'carlos garcia canache', 'ventas calle bejums', '87654321', '1b', '04244316376'),
(3, 'TITO JOSE', 'GARCIA BELLO', '21534303', '1c', '04244316376');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `residente_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contrasena`, `rol`, `residente_id`) VALUES
(1, 'Admin User', 'admin@condominio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL),
(2, 'Nuevo12', 'pablo2000814@gmail.com', '$2y$10$rwBpANnejFqWiOkRtr536.mx3nlvbjeLSjFxmjYAvgDhfMPaNCSyC', 'user', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalles_gastos`
--
ALTER TABLE `detalles_gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recibo_id` (`recibo_id`);

--
-- Indices de la tabla `deudas_residentes`
--
ALTER TABLE `deudas_residentes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `residente_id` (`residente_id`),
  ADD KEY `recibo_id` (`recibo_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unidad` (`unidad`);

--
-- Indices de la tabla `recibos_mensuales`
--
ALTER TABLE `recibos_mensuales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `residentes`
--
ALTER TABLE `residentes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unidad` (`unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalles_gastos`
--
ALTER TABLE `detalles_gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `deudas_residentes`
--
ALTER TABLE `deudas_residentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recibos_mensuales`
--
ALTER TABLE `recibos_mensuales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `residentes`
--
ALTER TABLE `residentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_gastos`
--
ALTER TABLE `detalles_gastos`
  ADD CONSTRAINT `detalles_gastos_ibfk_1` FOREIGN KEY (`recibo_id`) REFERENCES `recibos_mensuales` (`id`);

--
-- Filtros para la tabla `deudas_residentes`
--
ALTER TABLE `deudas_residentes`
  ADD CONSTRAINT `deudas_residentes_ibfk_1` FOREIGN KEY (`residente_id`) REFERENCES `residentes` (`id`),
  ADD CONSTRAINT `deudas_residentes_ibfk_2` FOREIGN KEY (`recibo_id`) REFERENCES `recibos_mensuales` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`unidad`) REFERENCES `residentes` (`unidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
