-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2026 a las 09:30:05
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
-- Base de datos: `gestion_afiliaciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `afiliaciones`
--

CREATE TABLE `afiliaciones` (
  `id_afiliacion` int(11) NOT NULL,
  `tipo` enum('rebranding','nueva') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `ganancia` decimal(10,2) DEFAULT 0.00,
  `fecha` date NOT NULL,
  `cantidad` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `afiliaciones`
--

INSERT INTO `afiliaciones` (`id_afiliacion`, `tipo`, `monto`, `ganancia`, `fecha`, `cantidad`) VALUES
(2, 'rebranding', 0.00, 231.00, '2026-03-16', 42),
(7, 'rebranding', 0.00, 253.00, '2026-03-17', 46),
(9, 'rebranding', 0.00, 264.00, '2026-03-18', 48),
(10, 'rebranding', 0.00, 247.50, '2026-03-19', 45),
(11, 'rebranding', 0.00, 71.50, '2026-03-20', 13),
(15, 'rebranding', 0.00, 55.00, '2026-03-23', 10),
(16, 'rebranding', 0.00, 1017.50, '2026-03-01', 185),
(17, 'rebranding', 0.00, 203.50, '2026-03-24', 37),
(19, 'rebranding', 0.00, 291.50, '2026-03-25', 53),
(24, 'rebranding', 0.00, 126.50, '2026-03-26', 23),
(25, 'rebranding', 0.00, 242.00, '2026-03-27', 44),
(27, 'rebranding', 0.00, 71.50, '2026-03-28', 13),
(28, 'rebranding', 0.00, 49.50, '2026-03-29', 9),
(30, 'rebranding', 0.00, 181.50, '2026-03-30', 33),
(31, 'rebranding', 0.00, 121.00, '2026-03-31', 22),
(35, 'rebranding', 0.00, 176.00, '2026-04-01', 32),
(36, 'rebranding', 0.00, 176.00, '2026-04-02', 32),
(37, 'rebranding', 0.00, 137.50, '2026-04-03', 25),
(39, 'rebranding', 0.00, 0.00, '2026-04-04', 0),
(40, 'rebranding', 0.00, 154.00, '2026-04-06', 28),
(41, 'rebranding', 0.00, 291.50, '2026-04-07', 53),
(42, 'rebranding', 0.00, 165.00, '2026-04-08', 30),
(48, 'rebranding', 0.00, 203.50, '2026-04-09', 37),
(51, 'rebranding', 0.00, 330.00, '2026-04-10', 60),
(52, 'rebranding', 0.00, 308.00, '2026-04-11', 56),
(55, 'rebranding', 0.00, 390.50, '2026-04-13', 71),
(56, 'rebranding', 0.00, 374.00, '2026-04-14', 68),
(60, 'rebranding', 0.00, 302.50, '2026-04-15', 55),
(61, 'rebranding', 0.00, 231.00, '2026-04-16', 42),
(69, 'rebranding', 0.00, 335.50, '2026-04-19', 61),
(71, 'rebranding', 0.00, 132.00, '2026-04-20', 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `afiliaciones_personal`
--

CREATE TABLE `afiliaciones_personal` (
  `id` int(11) NOT NULL,
  `nombre_personal` varchar(100) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `afiliaciones_personal`
--

INSERT INTO `afiliaciones_personal` (`id`, `nombre_personal`, `tipo`, `cantidad`, `fecha`) VALUES
(90, 'fiori', 'rebranding', 11, '2026-04-20'),
(91, 'oscar', 'rebranding', 13, '2026-04-20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ahorro`
--

CREATE TABLE `ahorro` (
  `id_ahorro` int(11) NOT NULL,
  `monto_actual` decimal(10,2) DEFAULT NULL,
  `meta_mensual` decimal(10,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ahorro`
--

INSERT INTO `ahorro` (`id_ahorro`, `monto_actual`, `meta_mensual`, `fecha`) VALUES
(1, 0.00, 2000.00, '2026-03-21'),
(2, 0.00, 2000.00, '2026-03-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL,
  `tipo` enum('fijo','comida') NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_fijos`
--

CREATE TABLE `gastos_fijos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos_fijos`
--

INSERT INTO `gastos_fijos` (`id`, `nombre`, `monto`) VALUES
(1, 'SENATI', 300.00),
(2, 'Alquiler', 500.00),
(3, 'Pandero', 200.00),
(4, 'Laptop', 150.00),
(5, 'Pago personal', 400.00),
(6, 'SENATI', 300.00),
(7, 'Alquiler', 500.00),
(8, 'Pandero', 200.00),
(9, 'Laptop', 150.00),
(10, 'Pago personal', 400.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas`
--

CREATE TABLE `metas` (
  `id_meta` int(11) NOT NULL,
  `tipo` enum('diaria','semanal','mensual') DEFAULT NULL,
  `cantidad_objetivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metas`
--

INSERT INTO `metas` (`id_meta`, `tipo`, `cantidad_objetivo`) VALUES
(1, 'diaria', 25),
(2, 'semanal', 150),
(3, 'mensual', 600),
(4, 'diaria', 25);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','pagado') DEFAULT 'pendiente',
  `descripcion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `fecha_pago`, `monto`, `estado`, `descripcion`) VALUES
(1, '2026-03-24', 508.75, 'pendiente', '50% primera quincena'),
(2, '2026-04-15', 508.75, 'pendiente', '50% restante'),
(5, '2026-04-24', 1504.25, 'pendiente', '50% 1ra Quincena'),
(6, '2026-05-15', 1504.25, 'pendiente', '50% Restante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renovaciones`
--

CREATE TABLE `renovaciones` (
  `id` int(11) NOT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `servicio` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `imagen` text DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `ubicacion` text DEFAULT NULL,
  `latitud` varchar(100) DEFAULT NULL,
  `longitud` varchar(100) DEFAULT NULL,
  `qr` text DEFAULT NULL,
  `visitado` tinyint(1) DEFAULT 0,
  `trucho` tinyint(1) DEFAULT 0,
  `gestor` varchar(2) DEFAULT NULL,
  `en_proceso` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `renovaciones`
--

INSERT INTO `renovaciones` (`id`, `cliente`, `telefono`, `direccion`, `servicio`, `fecha`, `imagen`, `codigo`, `ubicacion`, `latitud`, `longitud`, `qr`, `visitado`, `trucho`, `gestor`, `en_proceso`) VALUES
(1396, 'Marina Garcia Castro', '967490847', 'Av Nuevo Pucallpa', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/2f2ab26c-1bff-4893-8445-019507c98845', NULL, NULL, '-8.41097', '-74.647534', '0002010102113932186ad1d9ab9959eeb205d7556461b21f5204561153036045802PE5906YAPERO6004Lima6304FFFF', 0, 0, 'A', 0),
(1397, 'Cesar tapullima', '980949097', 'Colonización', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/1d769933-17b4-4d2b-8fdf-0f0e3a77235c', NULL, NULL, '-8.387854', '-74.54805', '0002010102113944m+2+zX4HSLU/eD4jq8np0zb5bTQnDFoUirvGW3gwofI=5204561153036045802PE5906YAPERO6004Lima630462BE', 1, 0, 'B', 0),
(1398, 'Alejandro otto', '914071104', 'Colonización', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/e4936567-2bd1-414d-82ed-1401421e3b2f', NULL, NULL, '-8.387844', '-74.548165', '00020101021139320beb0427f62553b2a423724fc62ad62d5204561153036045802PE5906YAPERO6004Lima6304314A', 0, 0, 'A', 0),
(1399, 'Dania cumapa', '932366246', 'Calle union', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/69243d3d-6a3c-4326-b53c-3a37a3afd38c', NULL, NULL, '-8.386685', '-74.549046', '000201010211393219c06425f97d5bbb816b605f03dd9f6c5204561153036045802PE5906YAPERO6004Lima63044015', 1, 0, 'B', 0),
(1400, 'Marcos amaringo', '939671942', 'Calle union', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/d75b9221-5449-46e7-ba04-757c97a986ad', NULL, NULL, '-8.38669', '-74.548987', '000201010211393233436280cae856f199c4a4e009534cf35204561153036045802PE5906YAPERO6004Lima63040E66', 1, 0, 'A', 0),
(1401, 'Elio gio', '967139189', 'Wiracocha', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/f4f0d06c-d961-405b-bf6b-c8659e12892e', NULL, NULL, '-8.387131', '-74.547614', '00020101021139324c28327591cf5a6ebc7de20e28af10c95204561153036045802PE5906YAPERO6004Lima63042902', 1, 0, 'B', 0),
(1402, 'Jhonatan García', '920003368', 'Alamedas', 'Afiliación Yape', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/28807752-f0e4-4f24-8c7c-6d323850b1b8', NULL, NULL, '-8.378926', '-74.568903', '00020101021139322b3441be5f015c6884a696408c35902d5204561153036045802PE5906YAPERO6004Lima6304C982', 1, 0, 'A', 0),
(1403, 'Jorge penaherrera', '978503546', 'Km 34', 'Afiliación Yape', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/71236ebc-7a27-4c80-bf68-ffd05d909443', NULL, NULL, '-8.476106', '-74.805384', '0002010102113932c9bfd59505f25c5d9a7cca831b82b6c55204561153036045802PE5906YAPERO6004Lima63047E0F', 1, 0, 'B', 0),
(1404, 'Samanta anco cutipa', '920024942', 'Jr los cedros', 'Afiliación Yape', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/9cf69d2a-ea1c-4f35-a9db-e81fc4576d28', NULL, NULL, '-8.392138', '-74.585878', '0002010102113932a0221c7de94b5c19aacffbd529e886735204561153036045802PE5906YAPERO6004Lima6304E3C4', 1, 0, 'A', 0),
(1405, 'Pilar perez rios', '978240195', 'Jr mapuya', 'Afiliación Yape', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/8b524653-6133-4fc8-ba42-34dd47b3787b', NULL, NULL, '-8.387487', '-74.586428', '00020101021139323ae56ca34ce25e1c84c8a1b64b40e42f5204561153036045802PE5906YAPERO6004Lima6304405C', 1, 0, 'B', 0),
(1406, 'Jim cristian macedo arnao', '903442755', 'Jr mapuya', 'Afiliación Yape', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/059c7e9e-1640-4de3-b739-00492fef1ae2', NULL, NULL, '-8.387683', '-74.5862', '0002010102113932442b548d646250fdac65427495bdcb6f5204561153036045802PE5906YAPERO6004Lima6304BFDB', 1, 0, 'A', 0),
(1407, 'Ernesto rojas uriarte', '929469916', 'Av play wood', 'Afiliación Yape', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/de9c649a-1e1e-4a9d-92ca-ea5541b85034', NULL, NULL, '-8.381168', '-74.587398', '000201010211393265ad82f0aa3b582386b0f911b450c2cc5204561153036045802PE5906YAPERO6004Lima63049266', 1, 0, 'B', 0),
(1408, 'Elizabeth Lopez Rios', '910853892', 'Jr. Rosa de America', 'REBRANDING', '2025-12-18', 'https://tw.navego360.com/v1.0/file/image/0af8d974-4907-4ca2-afe8-58e317d3bc1e', NULL, NULL, '-8.408151', '-74.548383', '000201010211393280348545fb815ebbb846859d607399855204561153036045802PE5906YAPERO6004Lima6304D23D', 1, 0, 'A', 0),
(1409, 'Rosa Luz Rojas huaman', '907382856', 'Calle Los Guerreros', 'REBRANDING', '2025-12-18', 'https://tw.navego360.com/v1.0/file/image/26afd8fc-a6e3-47f6-b5bb-28d377749c10', NULL, NULL, '-8.422937', '-74.554147', '000201010211393280b9445968395a0995270b39b8a3a38b5204561153036045802PE5906YAPERO6004Lima6304C104', 1, 0, 'B', 0),
(1410, 'Yolanda Caballero mozombite', '917965937', 'Jr. 12 de octubre', 'REBRANDING', '2025-12-18', 'https://tw.navego360.com/v1.0/file/image/9803b0c3-d5ed-4aa9-8562-4e2b16477da7', NULL, NULL, '-8.422917', '-74.554305', '00020101021139322dd778cf52635e1287871d6fbd3f204c5204561153036045802PE5906YAPERO6004Lima6304DBBD', 1, 0, 'A', 0),
(1411, 'Lili rios cerapio', '932658058', 'Jr rioja', 'REBRANDING', '2025-12-18', 'https://tw.navego360.com/v1.0/file/image/7e3fcbd3-b394-4b01-bdb9-aa9d2b003793', NULL, NULL, '-8.423938', '-74.554052', '000201010211393232d6bcbf0ec3549f85e0b2bbba9622f95204561153036045802PE5906YAPERO6004Lima63041864', 1, 0, 'B', 0),
(1412, 'Rosalina palomino aratea', '901711834', 'Jr Alan sisley', 'REBRANDING', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/009295d4-aecd-4760-8789-8439d4aa4849', NULL, NULL, '-8.415756', '-74.56623', '0002010102113932a5f3811a45a056608705c64a4d68ff8e5204561153036045802PE5906YAPERO6004Lima6304F553', 1, 0, 'A', 0),
(1413, 'Gerly Morales', '918223463', 'Fortaleza', 'REBRANDING', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/893a9a60-0973-47cc-92ff-3513271dd1b9', NULL, NULL, '-8.387588', '-74.548842', '0002010102113944vEA1PyUZs8pgCk1qxlZ2Ma7EC+5mdC36TapNmL+INcU=5204561153036045802PE5906YAPERO6004Lima6304BFAD', 1, 0, 'B', 0),
(1414, 'Eva gavina', '981100827', 'Calle unión', 'REBRANDING', '2025-12-19', 'https://tw.navego360.com/v1.0/file/image/9ca8deb4-7068-4243-ab06-950c164b92ec', NULL, NULL, '-8.387146', '-74.548915', '000201010211393272a4547685425cb689105fe7ffa01b075204561153036045802PE5906YAPERO6004Lima6304AE5D', 1, 0, 'A', 0),
(1415, 'Nelly elizabeth cordova malpartida', '945096807', 'Jr putumayo', 'REBRANDING', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/5fb35a55-81ff-494f-a64a-25d8dbc072dc', NULL, NULL, '-8.386017', '-74.586463', '0002010102113932336acf4e1d1e595ea87e7c515db8d0515204561153036045802PE5906YAPERO6004Lima63044C51', 1, 0, 'B', 0),
(1416, 'Gladys torres alarcon', '935963025', 'Jr play wood', 'REBRANDING', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/b7a26997-d97d-495f-9658-d04473244fa4', NULL, NULL, '-8.381189', '-74.587447', '00020101021139320f0e547ad86459be961fc8396225d2745204561153036045802PE5906YAPERO6004Lima6304648A', 1, 0, 'A', 0),
(1417, 'Axel ruiz montalvan', '935870200', 'Jr lupuna', 'REBRANDING', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/a1e0efc0-cd96-4276-baa8-391b2b781daa', NULL, NULL, '-8.381188', '-74.587932', '0002010102113932abd463cc7c2a5f6796ded0d1f9ae69495204561153036045802PE5906YAPERO6004Lima6304BB46', 1, 0, 'B', 0),
(1418, 'Lena barrera pacaya', '920492739', 'Jr lupuna', 'REBRANDING', '2025-12-20', 'https://tw.navego360.com/v1.0/file/image/6dda5c15-c83e-418d-8f8d-3f2cbc2deed4', NULL, NULL, '-8.381863', '-74.587318', '0002010102113932fad3c8b4df9b51208b960389c4f634195204561153036045802PE5906YAPERO6004Lima630495F5', 1, 0, 'A', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `latitud` double DEFAULT NULL,
  `longitud` double DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `usuario`, `latitud`, `longitud`, `fecha`) VALUES
(1, 'Oscar Cueva', -8.3950755, -74.5684815, '2026-03-25 05:14:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password`) VALUES
(1, 'Oscar Cueva', 'oscarcueva@yape.pe', '123456'),
(2, 'Oscar Cueva', 'oscarcueva@yape.pe', '123456');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `afiliaciones`
--
ALTER TABLE `afiliaciones`
  ADD PRIMARY KEY (`id_afiliacion`);

--
-- Indices de la tabla `afiliaciones_personal`
--
ALTER TABLE `afiliaciones_personal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ahorro`
--
ALTER TABLE `ahorro`
  ADD PRIMARY KEY (`id_ahorro`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gasto`);

--
-- Indices de la tabla `gastos_fijos`
--
ALTER TABLE `gastos_fijos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `metas`
--
ALTER TABLE `metas`
  ADD PRIMARY KEY (`id_meta`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`);

--
-- Indices de la tabla `renovaciones`
--
ALTER TABLE `renovaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `afiliaciones`
--
ALTER TABLE `afiliaciones`
  MODIFY `id_afiliacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `afiliaciones_personal`
--
ALTER TABLE `afiliaciones_personal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `ahorro`
--
ALTER TABLE `ahorro`
  MODIFY `id_ahorro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `gastos_fijos`
--
ALTER TABLE `gastos_fijos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `metas`
--
ALTER TABLE `metas`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `renovaciones`
--
ALTER TABLE `renovaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1419;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
