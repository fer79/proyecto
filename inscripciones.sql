-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Servidor: 172.16.216.35
-- Tiempo de generación: 12-04-2019 a las 14:20:54
-- Versión del servidor: 10.1.26-MariaDB-0+deb9u1
-- Versión de PHP: 5.6.38-2+0~20181015120829.6+stretch~1.gbp567807

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inscprueba2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `padre` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `padre`) VALUES
(1, 'General', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_usuarios`
--

CREATE TABLE `categorias_usuarios` (
  `id_categoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cron_emails`
--

CREATE TABLE `cron_emails` (
  `id` int(11) NOT NULL,
  `id_formulario` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `para` varchar(255) COLLATE utf8_bin NOT NULL,
  `titulo` varchar(255) COLLATE utf8_bin NOT NULL,
  `mensaje` text COLLATE utf8_bin NOT NULL,
  `desde_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `desde_nombre` varchar(255) COLLATE utf8_bin NOT NULL,
  `prioridad` int(11) NOT NULL DEFAULT '0',
  `intentos` int(11) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL,
  `enviado` int(11) NOT NULL DEFAULT '0',
  `fecha_enviado` datetime NOT NULL,
  `eliminado` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `cron_emails`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formularios`
--

CREATE TABLE `formularios` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `categoria` int(11) NOT NULL DEFAULT '1',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `formulario` text,
  `cantidad` bigint(20) DEFAULT NULL,
  `colderecha` text,
  `colizquierda` text,
  `vincular` int(11) DEFAULT NULL,
  `f_sorteo_plaza` datetime DEFAULT NULL,
  `sorteos_realizados` text,
  `eliminado` int(11) NOT NULL DEFAULT '0',
  `abonar` int(11) NOT NULL DEFAULT '0',
  `costocurso` int(11) NOT NULL,
  `monedacostocurso` varchar(10) NOT NULL,
  `lugarabono` varchar(255) NOT NULL,
  `fechaabonoinicio` date NOT NULL,
  `fechaabonofin` date NOT NULL,
  `fechacomienzocurso` date DEFAULT NULL,
  `cargahoraria` varchar(10) DEFAULT '0',
  `email_previo_comienzo_enviado` int(11) NOT NULL DEFAULT '0',
  `email_previo_fin_enviado` int(11) NOT NULL DEFAULT '0',
  `email_comienzo_enviado` int(11) NOT NULL DEFAULT '0',
  `publicado` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `formularios`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `permisos` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `permisos`) VALUES
(1, 'Super Admin', NULL),
(2, 'Usuario Básico', '{}'),
(3, 'Tesorería', '[\"formularios_ver_listado\",\"registros_ver_pago\",\"registros_exportar\",\"registros_ver_listado\",\"registros_marcar_pago\"]'),
(4, 'Creador', '[\"formularios_crear\",\"formularios_modificar\",\"formularios_eliminar\",\"formularios_ver_listado\",\"registros_ver_pago\",\"registros_sorteo_plaza\",\"registros_sorteo_beca\",\"registros_marcar_pago\",\"registros_ver_habilitado\",\"registros_marcar_habilitado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\",\"registros_eliminar\"]'),
(6, 'Posgrados', '[\"usuarios_crear\",\"usuarios_modificar\",\"usuarios_ver_listado\",\"formularios_crear\",\"formularios_modificar\",\"formularios_eliminar\",\"formularios_ver_listado\",\"registros_sorteo_plaza\",\"registros_sorteo_beca\",\"registros_habilitar_tarde\",\"registros_marcar_pago\",\"registros_marcar_habilitado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\",\"categorias_modificar\"]'),
(7, 'Bedelia', '[\"formularios_crear\",\"formularios_modificar\",\"formularios_eliminar\",\"formularios_ver_listado\",\"registros_sorteo_plaza\",\"registros_sorteo_beca\",\"registros_marcar_habilitado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\",\"registros_eliminar\"]'),
(9, 'Observador', '[\"formularios_ajenos\",\"formularios_modificar\",\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(10, 'Comisión Evaluadora', '[\"formularios_ajenos\",\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sorteo_becas`
--

CREATE TABLE `sorteo_becas` (
  `id` int(11) NOT NULL,
  `id_respuestas_campos` int(11) NOT NULL,
  `respuesta` varchar(255) NOT NULL,
  `fecha_sorteo` datetime NOT NULL,
  `porcentajedescuento` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sorteo_becas`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ci` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `f_nacimiento` varchar(255) DEFAULT NULL,
  `ciudadania` varchar(255) DEFAULT NULL,
  `residencia` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `departamento` varchar(255) DEFAULT NULL,
  `cpostal` varchar(255) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `formacionacademica` varchar(255) DEFAULT NULL,
  `centrodetitulacion` varchar(255) DEFAULT NULL,
  `f_titulacion` varchar(255) DEFAULT NULL,
  `key_validacion` varchar(255) DEFAULT NULL,
  `activo` varchar(255) DEFAULT NULL,
  `fecha_creacion` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_companeros`
--

CREATE TABLE `usuarios_companeros` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_companero` int(11) NOT NULL,
  `permisos` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_companeros`
--

INSERT INTO `usuarios_companeros` (`id`, `id_usuario`, `id_companero`, `permisos`) VALUES
(20, 3, 4, '[\"formularios_modificar\",\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(13, 4, 3, '[\"formularios_ver_listado\"]'),
(14, 4, 2273, '[\"formularios_ver_listado\"]'),
(16, 2273, 2453, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(19, 3, 2992, '[\"registros_ver_completo\",\"registros_ver_listado\"]'),
(21, 4, 3290, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(22, 4, 3291, '[\"formularios_ver_listado\"]'),
(23, 4, 3292, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\"]'),
(24, 4, 3293, '[\"formularios_ver_listado\"]'),
(25, 4, 3294, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\"]'),
(26, 3, 3290, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(27, 3, 3291, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(28, 3, 3292, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(29, 3, 3293, '[\"formularios_ver_listado\"]'),
(30, 3, 3294, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\"]'),
(31, 2273, 3290, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(32, 2273, 3291, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(33, 2273, 3292, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(34, 2273, 3293, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]'),
(35, 2273, 3294, '[\"formularios_ver_listado\",\"registros_ver_completo\",\"registros_ver_listado\",\"registros_exportar\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_crean_formularios`
--

CREATE TABLE `usuarios_crean_formularios` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_crean_formularios`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_habilitados_formulario`
--

CREATE TABLE `usuarios_habilitados_formulario` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_formulario` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_habilitados_formulario`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_permisos_formularios`
--

CREATE TABLE `usuarios_permisos_formularios` (
  `id_formulario` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `permiso` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuarios_permisos_formularios`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_respuestas_formularios`
--

CREATE TABLE `usuarios_respuestas_formularios` (
  `id` int(11) NOT NULL,
  `id_respuesta` int(11) NOT NULL,
  `id_campo` varchar(2550) DEFAULT NULL,
  `respuesta` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_respuestas_formularios`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_roles`
--

CREATE TABLE `usuarios_roles` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_roles`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_tardia_formulario`
--

CREATE TABLE `usuarios_tardia_formulario` (
  `id` int(11) NOT NULL,
  `id_formulario` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fecha_habilitado` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios_tardia_formulario`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_responde_formulario`
--

CREATE TABLE `usuario_responde_formulario` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT NULL,
  `fecha_respuesta` varchar(255) DEFAULT NULL,
  `borrador` int(11) DEFAULT NULL,
  `cuotas` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `pago1` varchar(255) DEFAULT NULL,
  `pago2` varchar(255) DEFAULT NULL,
  `pago3` varchar(255) DEFAULT NULL,
  `habilitado` smallint(6) NOT NULL,
  `seleccionado` int(11) DEFAULT NULL,
  `notacolor` varchar(100) NOT NULL,
  `notatexto` text NOT NULL,
  `diploma` varchar(255) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuario_responde_formulario`
--


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cron_emails`
--
ALTER TABLE `cron_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `formularios`
--
ALTER TABLE `formularios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios_permisos_formularios`
--
ALTER TABLE `usuarios_permisos_formularios`
  ADD PRIMARY KEY (`id_formulario`,`id_usuario`);

--
-- Indices de la tabla `usuario_responde_formulario`
--
ALTER TABLE `usuario_responde_formulario`
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cron_emails`
--
ALTER TABLE `cron_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `formularios`
--
ALTER TABLE `formularios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=419;
--
-- AUTO_INCREMENT de la tabla `usuario_responde_formulario`
--
ALTER TABLE `usuario_responde_formulario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4925;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
