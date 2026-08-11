-- Script SQL para actualizar la base de datos de Edecan con las nuevas funcionalidades de Lumisiales

-- 1. Agregar campos de propiedad y coordenadas a la tabla existente
ALTER TABLE `app_miembros_lumisial` 
ADD `es_propio` TINYINT(1) NOT NULL DEFAULT '0' AFTER `status`,
ADD `latitud` DECIMAL(10,8) NULL DEFAULT NULL AFTER `es_propio`,
ADD `longitud` DECIMAL(11,8) NULL DEFAULT NULL AFTER `latitud`;

-- 2. Crear nueva tabla para almacenar los documentos legales y escrituras en Base de Datos
CREATE TABLE `app_miembros_lumisial_documentos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lumisial_uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_data` LONGBLOB NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
