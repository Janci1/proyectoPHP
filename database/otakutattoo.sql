-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-09-2025 a las 21:27:30
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
-- Base de datos: `otakutattoo`
--
CREATE DATABASE IF NOT EXISTS `otakutattoo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `otakutattoo`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `idCita` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fecha_cita` date NOT NULL,
  `motivo_cita` varchar(255) NOT NULL,
  `hora_cita` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`idCita`, `idUser`, `fecha_cita`, `motivo_cita`, `hora_cita`) VALUES
(2, 3, '2025-09-19', 'sadasdsads', '21:53:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencias`
--

DROP TABLE IF EXISTS `incidencias`;
CREATE TABLE `incidencias` (
  `idIncidencia` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidencias`
--

INSERT INTO `incidencias` (`idIncidencia`, `idUser`, `email`, `asunto`, `mensaje`, `fecha_creacion`) VALUES
(1, 3, 'fio123@ejemplo.com', 'no puedo agendar', 'no funciona agendar citas me manda al inicio y no me agenda la cita', '2025-09-13 20:29:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

DROP TABLE IF EXISTS `noticias`;
CREATE TABLE `noticias` (
  `idNoticia` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `texto` text NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`idNoticia`, `idUser`, `titulo`, `imagen`, `texto`, `fecha`) VALUES
(1, 1, 'Rauw Alejandro: El Reggaetonero que Lleva el Anime en la Piel', 'img/noticias/noticiaRawAlejandro.jpg', '¡Atención, otakus y fanáticos del reggaetón! Rauw Alejandro, el \'Zorro\' de la música urbana, ha revelado una faceta sorprendente: su profunda devoción por el anime. Lejos de los escenarios, el artista puertorriqueño lleva su pasión por la animación japonesa ¡literalmente bajo la piel!\r\n\r\nEn una entrevista reciente, \"El Zorro\" confesó: \"Soy muy friki. Este brazo lo cogí para esto, para todos los personajes de anime que me gustan\". Su brazo se ha convertido en un lienzo vibrante con icónicos personajes como Trunks de Dragon Ball, simbolizando fuerza y determinación. También luce la máscara de Tanjiro Kamado de Demon Slayer, y rinde homenaje a Kenshin Himura de Rurouni Kenshin (Samurai X), un guiño a la dualidad entre paz y conflicto.\r\n\r\nEstos impresionantes tatuajes fueron realizados durante su primera gira por España, añadiendo un valor sentimental a cada trazo. Rauw ha explicado que para él, estas obras no son solo adornos: \"Yo soy bien fan de la cultura japonesa. Y pues sí, tengo como que ese amor entre el samurái y la geisha\". Esta fascinación se extiende a otras piezas en su cuerpo, como la geisha y el samurái en su cuello.\r\n\r\nQue un artista de su talla muestre con tanto orgullo su amor por el anime es un mensaje potente. Rauw Alejandro no solo rompe barreras musicales, sino que también redefine lo que significa ser una estrella, demostrando que la pasión por el arte es un motor inagotable de creatividad y autenticidad. ¡Sin duda, \"El Zorro\" seguirá sorprendiéndonos!', '2025-04-15'),
(8, 1, 'Expo Tatuaje Otaku: Un Lienzo para la Pasión Anime', 'img/noticias/pre.jpeg', 'Las \"Expo Tatuaje Otaku\" son eventos especializados que celebran la fusión entre el arte del tatuaje y el vibrante mundo del anime y el manga. Estas convenciones reúnen a tatuadores altamente cualificados, con un dominio excepcional de los estilos y personajes icónicos de la animación japonesa. Son una oportunidad única para los fans de la cultura otaku de conseguir diseños personalizados inspirados en sus series favoritas, directamente de la mano de artistas que entienden a fondo esta estética.\r\n\r\nEstos eventos no solo son espacios para tatuarse, sino también una experiencia cultural completa. Ofrecen concursos de tatuajes en categorías específicas de anime, oportunidades de conocer a otros entusiastas y disfrutar de actividades relacionadas. La interacción directa con los artistas y la posibilidad de verlos trabajar en vivo son grandes atractivos, creando un ambiente dinámico y creativo que resuena con la pasión de la comunidad otaku.\r\n\r\nCon ejemplos como la \"Otaku Tattoo Expo\" en Texas o la \"Anime Tattoo Expo\" en Barcelona, queda claro que este tipo de convenciones están en auge globalmente. Demuestran cómo el tatuaje se ha convertido en un lienzo para expresar la identidad y los intereses personales, permitiendo a los fans llevar su amor por el anime más allá de la pantalla y exhibirlo con orgullo en su propia piel.', '2025-05-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_data`
--

DROP TABLE IF EXISTS `users_data`;
CREATE TABLE `users_data` (
  `idUser` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `sexo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `users_data`
--

INSERT INTO `users_data` (`idUser`, `nombre`, `apellidos`, `email`, `telefono`, `fecha_nacimiento`, `direccion`, `sexo`) VALUES
(1, 'John', 'Silva', 'janci.trabajo@gmail.com', '600828227', '1988-04-15', 'Severo Ochoa', 'Masculino'),
(3, 'fiorella', 'carrasco', 'fiorella@gmail.com', '618624580', '1993-04-10', 'Severo Ochoa', 'Femenino'),
(19, 'Juan', 'Silva', 'jancia@gmail.com', '654988798', '1988-12-01', 'Severo Ochoa', 'Masculino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_login`
--

DROP TABLE IF EXISTS `users_login`;
CREATE TABLE `users_login` (
  `idLogin` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `users_login`
--

INSERT INTO `users_login` (`idLogin`, `idUser`, `usuario`, `password`, `rol`) VALUES
(1, 1, 'mijin', '$2y$10$H5DFFwh3sS2YpcANA8kIOu0lAhkFBswllm3ipGChezkfuEa/awCJW', 'admin'),
(3, 3, 'fiore', '$2y$10$cU78KIUvD3VGC/e0QMlqrOKQY9BgdgwfTWdeWoxstW2ZTCUhkmW.y', 'user'),
(4, 19, 'jancio', '$2y$10$6tmKgOPKiBIE7yntsLfd8u9IXeCvOChN3Xw8AsXkSsllvE.SxCRsW', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `foreignUser` (`idUser`);

--
-- Indices de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD PRIMARY KEY (`idIncidencia`),
  ADD UNIQUE KEY `idUser` (`idUser`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`idNoticia`),
  ADD UNIQUE KEY `titulo` (`titulo`),
  ADD KEY `foreignUsers` (`idUser`);

--
-- Indices de la tabla `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD PRIMARY KEY (`idLogin`),
  ADD UNIQUE KEY `idUser` (`idUser`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `incidencias`
--
ALTER TABLE `incidencias`
  MODIFY `idIncidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `idNoticia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `users_data`
--
ALTER TABLE `users_data`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `users_login`
--
ALTER TABLE `users_login`
  MODIFY `idLogin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `foreignUser` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `incidencias`
--
ALTER TABLE `incidencias`
  ADD CONSTRAINT `foreignUseres` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `foreignUsers` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`);

--
-- Filtros para la tabla `users_login`
--
ALTER TABLE `users_login`
  ADD CONSTRAINT `foreignUsuer` FOREIGN KEY (`idUser`) REFERENCES `users_data` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
