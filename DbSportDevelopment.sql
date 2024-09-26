DROP DATABASE IF EXISTS sport;
CREATE DATABASE sport;
USE sport;

CREATE TABLE tb_administradores (
 id_admin INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
 nombre VARCHAR(50) NOT NULL,
 apellido VARCHAR(50) NOT NULL,
 correo_administrador VARCHAR(100) NOT NULL,
 alias_administrador VARCHAR(25) NOT NULL,
 clave_administrador VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_clientes (
  id_cliente INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  nombre_cliente VARCHAR(50) NOT NULL,
  apellido_cliente VARCHAR(50) NOT NULL,
  dui_cliente VARCHAR(10) NOT NULL,
  correo_cliente VARCHAR(100) NOT NULL,
  telefono_cliente VARCHAR(9) NOT NULL,
  direccion_cliente VARCHAR(250) NOT NULL,
  nacimiento_cliente DATE NOT NULL,
  clave_cliente VARCHAR(100) NOT NULL,
  estado_cliente TINYINT(1) NOT NULL DEFAULT 1,
  fecha_registro DATE NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  codigo_recuperacion VARCHAR(6) NOT NULL,
  fecha_expiracion_codigo DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_tallas (
   id_talla INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   nombre VARCHAR(5) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_categorias (
  id_categoria INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(250) DEFAULT NULL,
  imagen VARCHAR(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_productos(
  id_producto INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  nombre_producto VARCHAR(50) NOT NULL,
  descripcion_producto VARCHAR(250) NOT NULL,
  precio_producto DECIMAL(5,2) NOT NULL,
  existencias_producto INT(10) UNSIGNED NOT NULL,
  imagen_producto VARCHAR(25) NOT NULL,
  id_categoria INT(10) UNSIGNED NOT NULL,
  estado_producto TINYINT(1) NOT NULL,
  id_administrador INT(10) UNSIGNED NOT NULL,
  calificacion_promedio ENUM ('1', '2', '3', '4', '5') NOT NULL,
  fecha_registro DATE NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_comentarios (
  id_comentario INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  id_producto INT(10) UNSIGNED NOT NULL,
  id_cliente INT(10) UNSIGNED NOT NULL,
  calificacion_producto ENUM ('1', '2', '3', '4', '5') NOT NULL,
  comentario_producto VARCHAR(255) NOT NULL,
  fecha_valoracion DATE NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  estado_comentario  TINYINT(1) NOT NULL
);

CREATE TABLE tb_detalle_pedidos (
  id_detalle INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  id_producto INT(10) UNSIGNED NOT NULL,
  cantidad_producto SMALLINT(6) UNSIGNED NOT NULL,
  precio_producto DECIMAL(5,2) UNSIGNED NOT NULL,
  id_pedido INT(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_pedidos (
  id_pedido INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  id_cliente INT(10) UNSIGNED NOT NULL,
  direccion_pedido VARCHAR(250) NOT NULL,
  estado_pedido ENUM('Pendiente','EnCamino','Entregado','Cancelado','Historial') NOT NULL,
  fecha_registro DATE NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_colores (
 id_color INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
 nombre VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_marcas (
   id_marca INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   nombre VARCHAR(20) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_deportes (
   id_deporte INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   nombre VARCHAR(20) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_generos (
   id_genero INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   nombre VARCHAR(50) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_imagenes (
   id_imagen INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   nombre_imagen VARCHAR(25) NOT NULL,
   id_producto INT
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE tb_tarjetas (
   id_tarjeta INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
   tipo_tarjeta ENUM('Visa','MasterCard') NOT NULL,
   tipo_uso ENUM('Credito','Debito') NOT NULL,
   numero_tarjeta INT(16) UNSIGNED NOT NULL,
   nombre_tarjeta VARCHAR(50) NOT NULL,
   fecha_expiracion INT(5) UNSIGNED NOT NULL,
   codigo_verificacion INT(5) UNSIGNED NOT NULL,
   id_cliente INT(10) UNSIGNED NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO tb_categorias (id_categoria, nombre, imagen, descripcion) VALUES 
(1, 'Futbol', 'CategoriaFutbol.png', 'Todos los productos relacionados con el fútbol'),
(2, 'Baloncesto', 'CategoriaBaloncesto.png', 'Todos los productos relacionados con el baloncesto'),
(3, 'Voleibol', 'CategoriaVoleibol.png', 'Todos los productos relacionados con el voleibol'),
(4, 'Sneackers', 'CategoriaZapatos.png', 'Todos los productos relacionados con sneackers');

INSERT INTO tb_productos (id_producto, nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto, id_administrador, fecha_registro) 
VALUES 
(1, 'Balon de Futbol', 'Balon oficial para partidos de futbol.', 29.99, 150, 'img5.png', 1, 1, 1, '2024-05-30'),
(2, 'Balon de Futbol', 'Balon oficial para partidos de futbol.', 29.99, 150, 'img5.png', 1, 1, 1, '2024-05-30'),
(3, 'Balon de Futbol', 'Balon oficial para partidos de futbol.', 29.99, 150, 'img5.png', 1, 1, 1, '2024-05-30'),
(4, 'Balon de Futbol', 'Balon oficial para partidos de futbol.', 29.99, 150, 'img5.png', 1, 1, 1, '2024-05-30'),
(5, 'Balon de Basquetball', 'Balon oficial para partidos de basquetball.', 34.99, 100, 'img6.png', 2, 1, 1, '2024-05-30'),
(6, 'Balon de Basquetball', 'Balon oficial para partidos de basquetball.', 34.99, 100, 'img6.png', 2, 1, 1, '2024-05-30'),
(7, 'Balon de Basquetball', 'Balon oficial para partidos de basquetball.', 34.99, 100, 'img6.png', 2, 1, 1, '2024-05-30'),
(8, 'Balon de Basquetball', 'Balon oficial para partidos de basquetball.', 34.99, 100, 'img6.png', 2, 1, 1, '2024-05-30'),
(9, 'Balon de Voleyball', 'Balon oficial para partidos de voleyball.', 24.99, 200, 'img7.png', 3, 1, 1, '2024-05-30'),
(10, 'Balon de Voleyball', 'Balon oficial para partidos de voleyball.', 24.99, 200, 'img7.png', 3, 1, 1, '2024-05-30'),
(11, 'Balon de Voleyball', 'Balon oficial para partidos de voleyball.', 24.99, 200, 'img7.png', 3, 1, 1, '2024-05-30'),
(12, 'Balon de Voleyball', 'Balon oficial para partidos de voleyball.', 24.99, 200, 'img7.png', 3, 1, 1, '2024-05-30'),
(13, 'Sneackers Deportivos', 'Zapatos deportivos de alta calidad.', 89.99, 50, 'img1.png', 4, 1, 1, '2024-05-30'),
(14, 'Sneackers Deportivos', 'Zapatos deportivos de alta calidad.', 89.99, 50, 'img2.png', 4, 1, 1, '2024-05-30'),
(15, 'Sneackers Deportivos', 'Zapatos deportivos de alta calidad.', 89.99, 50, 'img3.png', 4, 1, 1, '2024-05-30'),
(16, 'Sneackers Deportivos', 'Zapatos deportivos de alta calidad.', 89.99, 50, 'img4.png', 4, 1, 1, '2024-05-30');

INSERT INTO tb_comentarios (id_producto, id_cliente, calificacion_producto, comentario_producto, fecha_valoracion, estado_comentario)
VALUES 
(1, 1, '5', 'Excelente producto, muy satisfecho con la compra.', '2024-08-01', 1),
(1, 1, '4', 'Buen producto, aunque el envío se retrasó un poco.', '2024-08-02', 1),
(2, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(2, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(3, 1, '5', 'Excelente producto, muy satisfecho con la compra.', '2024-08-01', 1),
(3, 1, '4', 'Buen producto, aunque el envío se retrasó un poco.', '2024-08-02', 1),
(4, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(4, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(5, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(5, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(6, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(6, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(7, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(7, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(8, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(8, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(9, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(9, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(10, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(10, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(11, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(11, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(12, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(12, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(13, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(13, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(14, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(14, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(15, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(15, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1),
(16, 1, '2', 'No estoy contento con la compra, el producto no cumplió mis expectativas.', '2024-08-04', 1),
(16, 1, '3', 'El producto está bien, pero esperaba mejor calidad.', '2024-08-03', 1);


INSERT INTO tb_tallas (nombre) VALUES
('XS'),
('S'),
('M'),
('L'),
('XL');

INSERT INTO tb_colores (nombre) VALUES
('Rojo'),
('Azul'),
('Verde'),
('Negro'),
('Blanco');

INSERT INTO tb_marcas (nombre) VALUES
('Nike'),
('Adidas'),
('Puma'),
('Reebok'),
('Under Armour');

INSERT INTO tb_deportes (nombre) VALUES
('Fútbol'),
('Baloncesto'),
('Voleibol'),
('Tenis'),
('Natación');

INSERT INTO tb_generos (nombre) VALUES
('Masculino'),
('Femenino'),
('Unisex'),
('Niños'),
('Niñas');

SELECT * FROM tb_administradores;

SELECT * FROM tb_clientes;

SELECT * FROM tb_tallas;

SELECT * FROM tb_categorias;

SELECT * FROM tb_tarjetas;

SELECT * FROM tb_productos;

SELECT * FROM tb_detalle_pedidos;

SELECT * FROM tb_pedidos;

SELECT * FROM tb_colores;

SELECT * FROM tb_marcas;

SELECT * FROM tb_deportes;

SELECT * FROM tb_generos;

SELECT * FROM tb_imagenes;

SELECT * FROM tb_detalle_pedidos;

SELECT * FROM tb_pedidos;

SELECT * FROM tb_comentarios;