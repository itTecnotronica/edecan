CREATE TABLE `misioneros_por_evento` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha_de_evento_id` INTEGER UNSIGNED NOT NULL,
  `user_id` INTEGER UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `updated_at` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;
