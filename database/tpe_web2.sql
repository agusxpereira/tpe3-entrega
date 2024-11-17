
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `usuario` varchar(150) NOT NULL UNIQUE,
  `password` varchar(60) NOT NULL
);


INSERT INTO `usuarios` (usuario, password) VALUES
("webadmin", "$2y$10$.k09NI/.OMdOWsk9D76JJ./o6Wy7dJsbFnYWYLo8C3Ox44yHmfEXC");

CREATE TABLE IF NOT EXISTS `generos` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL UNIQUE,
  `descripcion` varchar(255) NOT NULL,
  `ruta_imagen` varchar(255),
  `activo` BOOLEAN NOT NULL DEFAULT TRUE,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



INSERT INTO `generos` (nombre, descripcion) VALUES 
('realismo', 'narrativas que retratan la vida cotidiana y las experiencias humanas de manera fiel y veraz.'),
('novela', 'obra literaria extensa que narra una historia de ficción con personajes y tramas desarrolladas.'),
('distopía', 'relatos que presentan sociedades futuras caracterizadas por opresión y deshumanización.'),
('romántico', 'género que enfatiza las emociones, el amor y la belleza de las relaciones humanas.');

CREATE TABLE IF NOT EXISTS `libros` (
  `id_libro` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `paginas` int(11) NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `id_genero` int(11) NOT NULL,
  `en_oferta` tinyint(1) NOT NULL DEFAULT 0,
  `precio` double NOT NULL,
  
);

ALTER TABLE `libros`
  ADD CONSTRAINT `fk_generos_libros` FOREIGN KEY (`id_genero`) REFERENCES `generos` (`id`);

INSERT INTO `libros` (titulo, autor, id_genero, paginas) VALUES
('cien años de soledad', 'gabriel garcía márquez', 1, 417),
('don quijote de la mancha', 'miguel de cervantes', 2, 863),
('1984', 'george orwell', 3, 328),
('orgullo y prejuicio', 'jane austen', 4, 432);
