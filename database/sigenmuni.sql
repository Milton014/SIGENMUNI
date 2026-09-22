-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-09-2026 a las 05:17:36
-- Versión del servidor: 8.0.41
-- Versión de PHP: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sigenmuni4`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint UNSIGNED NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `usuario_login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_id` int DEFAULT NULL,
  `entidad_descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detalle` text COLLATE utf8mb4_unicode_ci,
  `datos_anteriores` longtext COLLATE utf8mb4_unicode_ci,
  `datos_nuevos` longtext COLLATE utf8mb4_unicode_ci,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto`
--

CREATE TABLE `concepto` (
  `id` int NOT NULL,
  `codigo` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('REMUNERATIVO','NO_REMUNERATIVO','ASIGNACION_FAMILIAR','DESCUENTO','APORTE_PATRONAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `forma_calculo` enum('FIJO','TABLA_CATEGORIA','PORCENTAJE','MANUAL','FORMULA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FIJO',
  `porcentaje` decimal(10,4) DEFAULT '0.0000',
  `monto_fijo` decimal(12,2) DEFAULT '0.00',
  `requiere_manual` tinyint(1) DEFAULT '0',
  `asignable_empleado` tinyint(1) NOT NULL DEFAULT '0',
  `base_calculo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orden_calculo` int NOT NULL DEFAULT '0',
  `aplica_sac` tinyint(1) NOT NULL DEFAULT '0',
  `visible_recibo` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_valor`
--

CREATE TABLE `concepto_valor` (
  `id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `escalafon_id` int DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT '0.00',
  `porcentaje` decimal(10,2) DEFAULT '0.00',
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int NOT NULL,
  `institucion_id` int NOT NULL,
  `oficina_id` int NOT NULL,
  `situacion_id` int NOT NULL,
  `escalafon_id` int DEFAULT NULL,
  `categoria_id` int NOT NULL,
  `nro_legajo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuil` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_inactivo` date DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domicilio` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_concepto`
--

CREATE TABLE `empleado_concepto` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `monto_manual` decimal(12,2) DEFAULT '0.00',
  `porcentaje_manual` decimal(10,2) DEFAULT '0.00',
  `cantidad` decimal(10,2) DEFAULT '1.00',
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_periodo_laboral`
--

CREATE TABLE `empleado_periodo_laboral` (
  `id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `motivo_inicio` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo_fin` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_alta_id` int DEFAULT NULL,
  `usuario_cierre_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `escalafon`
--

CREATE TABLE `escalafon` (
  `id` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion`
--

CREATE TABLE `institucion` (
  `id` int NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion`
--

CREATE TABLE `liquidacion` (
  `id` int NOT NULL,
  `tipo_liquidacion` varchar(50) NOT NULL,
  `periodo` varchar(7) NOT NULL,
  `fecha_liquidacion` date NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` enum('BORRADOR','CERRADA','ANULADA') NOT NULL DEFAULT 'BORRADOR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion_detalle`
--

CREATE TABLE `liquidacion_detalle` (
  `id` int NOT NULL,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `concepto_id` int NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `porcentaje_aplicado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `es_manual` tinyint(1) NOT NULL DEFAULT '0',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion_empleado`
--

CREATE TABLE `liquidacion_empleado` (
  `id` int NOT NULL,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `aplica_prevision` tinyint(1) NOT NULL DEFAULT '1',
  `total_remunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_descuentos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_no_remunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_asignaciones` decimal(12,2) NOT NULL DEFAULT '0.00',
  `neto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion_novedad`
--

CREATE TABLE `liquidacion_novedad` (
  `id` int UNSIGNED NOT NULL,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `dias_liquidados` tinyint UNSIGNED NOT NULL DEFAULT '30',
  `aplica_presentismo` tinyint(1) NOT NULL DEFAULT '1',
  `dias_sac` smallint UNSIGNED DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion_participante`
--

CREATE TABLE `liquidacion_participante` (
  `id` int NOT NULL,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liquidacion_protocolar`
--

CREATE TABLE `liquidacion_protocolar` (
  `id` int NOT NULL,
  `liquidacion_id` int NOT NULL,
  `empleado_id` int NOT NULL,
  `importe` decimal(12,2) NOT NULL DEFAULT '0.00',
  `aplica_prevision` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficina`
--

CREATE TABLE `oficina` (
  `id` int NOT NULL,
  `institucion_id` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuit` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `es_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_modulo_permiso`
--

CREATE TABLE `rol_modulo_permiso` (
  `id` int NOT NULL,
  `rol_id` int NOT NULL,
  `modulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archivo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permitido` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacion`
--

CREATE TABLE `situacion` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` int DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `codigo_recuperacion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auditoria_fecha` (`fecha_hora`),
  ADD KEY `idx_auditoria_usuario` (`usuario_id`),
  ADD KEY `idx_auditoria_modulo` (`modulo`),
  ADD KEY `idx_auditoria_accion` (`accion`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categoria_codigo` (`codigo`);

--
-- Indices de la tabla `concepto`
--
ALTER TABLE `concepto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `concepto_valor`
--
ALTER TABLE `concepto_valor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_concepto_valor_concepto` (`concepto_id`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nro_legajo` (`nro_legajo`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `cuil` (`cuil`),
  ADD UNIQUE KEY `uq_empleado_legajo` (`nro_legajo`),
  ADD UNIQUE KEY `uq_empleado_dni` (`dni`),
  ADD UNIQUE KEY `uq_empleado_cuil` (`cuil`),
  ADD UNIQUE KEY `uq_empleado_email` (`email`),
  ADD KEY `institucion_id` (`institucion_id`),
  ADD KEY `oficina_id` (`oficina_id`),
  ADD KEY `situacion_id` (`situacion_id`),
  ADD KEY `escalafon_id` (`escalafon_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `empleado_concepto`
--
ALTER TABLE `empleado_concepto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_empleado_concepto_empleado` (`empleado_id`),
  ADD KEY `fk_empleado_concepto_concepto` (`concepto_id`);

--
-- Indices de la tabla `empleado_periodo_laboral`
--
ALTER TABLE `empleado_periodo_laboral`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empleado_periodo_desde` (`empleado_id`,`fecha_desde`),
  ADD KEY `idx_periodo_empleado` (`empleado_id`),
  ADD KEY `idx_periodo_fecha_desde` (`fecha_desde`),
  ADD KEY `idx_periodo_fecha_hasta` (`fecha_hasta`),
  ADD KEY `fk_periodo_laboral_usuario_alta` (`usuario_alta_id`),
  ADD KEY `fk_periodo_laboral_usuario_cierre` (`usuario_cierre_id`);

--
-- Indices de la tabla `escalafon`
--
ALTER TABLE `escalafon`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `institucion`
--
ALTER TABLE `institucion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `liquidacion`
--
ALTER TABLE `liquidacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `liquidacion_detalle`
--
ALTER TABLE `liquidacion_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_liq_det_liquidacion` (`liquidacion_id`),
  ADD KEY `idx_liq_det_empleado` (`empleado_id`),
  ADD KEY `idx_liq_det_concepto` (`concepto_id`);

--
-- Indices de la tabla `liquidacion_empleado`
--
ALTER TABLE `liquidacion_empleado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_liq_emp_liquidacion` (`liquidacion_id`),
  ADD KEY `idx_liq_emp_empleado` (`empleado_id`);

--
-- Indices de la tabla `liquidacion_novedad`
--
ALTER TABLE `liquidacion_novedad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_liquidacion_novedad_empleado` (`liquidacion_id`,`empleado_id`),
  ADD KEY `idx_liquidacion_novedad_liquidacion` (`liquidacion_id`),
  ADD KEY `idx_liquidacion_novedad_empleado` (`empleado_id`);

--
-- Indices de la tabla `liquidacion_participante`
--
ALTER TABLE `liquidacion_participante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_liquidacion_participante` (`liquidacion_id`,`empleado_id`),
  ADD KEY `idx_liquidacion_participante_liquidacion` (`liquidacion_id`),
  ADD KEY `idx_liquidacion_participante_empleado` (`empleado_id`);

--
-- Indices de la tabla `liquidacion_protocolar`
--
ALTER TABLE `liquidacion_protocolar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_liq_protocolar` (`liquidacion_id`,`empleado_id`),
  ADD KEY `fk_protocolar_empleado` (`empleado_id`);

--
-- Indices de la tabla `oficina`
--
ALTER TABLE `oficina`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_modulo_permiso`
--
ALTER TABLE `rol_modulo_permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rol_archivo` (`rol_id`,`archivo`);

--
-- Indices de la tabla `situacion`
--
ALTER TABLE `situacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `uq_usuario_email` (`email`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concepto`
--
ALTER TABLE `concepto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concepto_valor`
--
ALTER TABLE `concepto_valor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado_concepto`
--
ALTER TABLE `empleado_concepto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado_periodo_laboral`
--
ALTER TABLE `empleado_periodo_laboral`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `escalafon`
--
ALTER TABLE `escalafon`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `institucion`
--
ALTER TABLE `institucion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion`
--
ALTER TABLE `liquidacion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion_detalle`
--
ALTER TABLE `liquidacion_detalle`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion_empleado`
--
ALTER TABLE `liquidacion_empleado`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion_novedad`
--
ALTER TABLE `liquidacion_novedad`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion_participante`
--
ALTER TABLE `liquidacion_participante`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `liquidacion_protocolar`
--
ALTER TABLE `liquidacion_protocolar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `oficina`
--
ALTER TABLE `oficina`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol_modulo_permiso`
--
ALTER TABLE `rol_modulo_permiso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `situacion`
--
ALTER TABLE `situacion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `concepto_valor`
--
ALTER TABLE `concepto_valor`
  ADD CONSTRAINT `fk_concepto_valor_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`);

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `institucion` (`id`),
  ADD CONSTRAINT `empleado_ibfk_2` FOREIGN KEY (`oficina_id`) REFERENCES `oficina` (`id`),
  ADD CONSTRAINT `empleado_ibfk_3` FOREIGN KEY (`situacion_id`) REFERENCES `situacion` (`id`),
  ADD CONSTRAINT `empleado_ibfk_4` FOREIGN KEY (`escalafon_id`) REFERENCES `escalafon` (`id`),
  ADD CONSTRAINT `empleado_ibfk_5` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`);

--
-- Filtros para la tabla `empleado_concepto`
--
ALTER TABLE `empleado_concepto`
  ADD CONSTRAINT `fk_empleado_concepto_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`),
  ADD CONSTRAINT `fk_empleado_concepto_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`);

--
-- Filtros para la tabla `empleado_periodo_laboral`
--
ALTER TABLE `empleado_periodo_laboral`
  ADD CONSTRAINT `fk_periodo_laboral_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_periodo_laboral_usuario_alta` FOREIGN KEY (`usuario_alta_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_periodo_laboral_usuario_cierre` FOREIGN KEY (`usuario_cierre_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `liquidacion_detalle`
--
ALTER TABLE `liquidacion_detalle`
  ADD CONSTRAINT `fk_liquidacion_detalle_concepto` FOREIGN KEY (`concepto_id`) REFERENCES `concepto` (`id`),
  ADD CONSTRAINT `fk_liquidacion_detalle_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`),
  ADD CONSTRAINT `fk_liquidacion_detalle_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`);

--
-- Filtros para la tabla `liquidacion_empleado`
--
ALTER TABLE `liquidacion_empleado`
  ADD CONSTRAINT `fk_liquidacion_empleado_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`),
  ADD CONSTRAINT `fk_liquidacion_empleado_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`);

--
-- Filtros para la tabla `liquidacion_novedad`
--
ALTER TABLE `liquidacion_novedad`
  ADD CONSTRAINT `fk_liquidacion_novedad_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_liquidacion_novedad_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `liquidacion_participante`
--
ALTER TABLE `liquidacion_participante`
  ADD CONSTRAINT `fk_liquidacion_participante_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_liquidacion_participante_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `liquidacion_protocolar`
--
ALTER TABLE `liquidacion_protocolar`
  ADD CONSTRAINT `fk_protocolar_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id`),
  ADD CONSTRAINT `fk_protocolar_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidacion` (`id`);

--
-- Filtros para la tabla `oficina`
--
ALTER TABLE `oficina`
  ADD CONSTRAINT `oficina_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `institucion` (`id`);

--
-- Filtros para la tabla `rol_modulo_permiso`
--
ALTER TABLE `rol_modulo_permiso`
  ADD CONSTRAINT `fk_rol_modulo_permiso_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
