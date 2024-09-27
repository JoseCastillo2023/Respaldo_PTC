DROP DATABASE IF EXISTS PanaderiaHPTC2;
CREATE DATABASE PanaderiaHPTC2;
USE PanaderiaHPTC2;

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

CREATE TABLE tb_categorias (
  id_categoria INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  nombre_categoria VARCHAR(50) NOT NULL UNIQUE,
  descripcion_categoria VARCHAR(250) DEFAULT NULL,
  imagen_categoria VARCHAR(250) NOT NULL
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

CREATE TABLE tb_detalles_pedidos (
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
  estado_pedido ENUM('Pendiente','En camino','Entregado','Cancelado','Historial') NOT NULL,
  fecha_registro DATE NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Datos de la tabla de Categorias.
INSERT INTO tb_categorias (nombre_categoria, descripcion_categoria, imagen_categoria) VALUES ('Pasteles', 'Postres grandes en forma circular', 'Pastel.jfif');
INSERT INTO tb_categorias (nombre_categoria, descripcion_categoria, imagen_categoria) VALUES ('Galletas', 'Galletas con cacao', 'Galletas.png');
INSERT INTO tb_categorias (nombre_categoria, descripcion_categoria, imagen_categoria) VALUES ('Bolleria', 'Todo tipo de comida relaciona con los panes', 'Bolleria.jpg');

-- Datos de la tabla de Productos.
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Pastel de chocolate', 'Pastel de chocolate con frutas', 10.50, 100, 'Pastel_chocolate.jpg', 1, 1);
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Pastel de vainilla', 'Pastel de vainilla con frutas', 9.50, 100, 'Pastel_vainilla.jpg', 1, 1);
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Galletas simples', 'Galletas simples con azucar', 2.50, 100, 'Galletas_masa.jfif', 2, 1);
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Galletas de chispas de chocolate', 'Galletas de chispas', 1.50, 100, 'Galletas_chispas.jpg', 2, 1);
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Dona de fresa', 'Dona de fresa rellena de crema', 1.00, 0, 'Dona_fresa.jpg', 3, 1);
INSERT INTO tb_productos (nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto) 
VALUES ('Dona de chocolate', 'Dona de chocolate rellena de crema', 1.00, 100, 'Dona_chocolate.jpg', 3, 1);

-- Datos de la tabla de Clientes.
INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES ('Sofía', 'Navas', '17719188-1', 'sofianavas2006@gmail.com', '77778891', 'San Salvador, Apopa', '2000-01-15', 'Elzapato_2001', '456987', '2020-1-31 23:59:59', '2021-1-30');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES ('Miguel', 'Angel', '12222221-1', 'elmascapito@gmail.com', '71779988', 'San Salvador, Delgado', '2000-03-22', 'Elzapato_2002', '456989', '2021-12-31 23:59:59', '2022-2-26');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES 
('Jose Alfredo', 'Ramiréz Ochoa', '13333331-1', 'elmasnegro2006@gmail.com', '72593434', 'San Salvador, Nejapa', '2000-07-10', 'Elzapato_2003', '451765', '2024-12-31 23:59:59', '2022-2-26');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES 
('David', 'Gómez', '14444441-1', 'david.gomez@example.com', '72666666', 'San Salvador, San Salvador', '2000-12-05', 'Elzapato_2004', '265965', '2024-12-31 23:59:59', '2023-4-30');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES 
('Lucía', 'Fernández', '15555551-1', 'lucia.fernandez@example.com', '79555555', 'San Salvador, Ilopango', '2000-04-30', 'Elzapato_2005', '200345', '2024-12-31 23:59:59', '2024-1-30');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro)
VALUES 
('Jose', 'Castillo', '15555531-1', 'jose.castillo@example.com', '79555535', 'San Salvador, Ilopango', '2000-04-30', 'Elzapato_2006', '200345', '2024-12-31 23:59:59', '2024-1-30');

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro, estado_cliente)
VALUES 
('Fernando', 'Paniagua', '15555231-1', 'fer.panconagua@example.com', '79334535', 'San Salvador, Apopa', '2000-04-30', 'Elzapato_2007', '200345', '2024-12-31 23:59:59', '2024-3-30', 0);

INSERT INTO tb_clientes (nombre_cliente, apellido_cliente, dui_cliente, correo_cliente, telefono_cliente, direccion_cliente, nacimiento_cliente, clave_cliente, codigo_recuperacion, fecha_expiracion_codigo, fecha_registro, estado_cliente)
VALUES 
('Luis', 'Lopez', '15552531-1', 'luis.lopez2006@example.com', '79515535', 'San Salvador, Ilopango', '2000-04-30', 'Elzapato_2008', '200345', '2024-12-31 23:59:59', '2024-4-30', 0);


INSERT INTO tb_comentarios (id_producto, id_cliente, calificacion_producto, comentario_producto)
VALUES 
(1, 1, 5, 'Excelente pastel de chocolate muy cremoso.'),
(2, 1, 4, 'El pastel de vanilla esta muy ricooo.'),
(3, 1, 5, 'Las galletas son las mejores con su sabor.'),
(4, 1, 3, 'Las galletas de chocolate esta bien.'),
(5, 1, 5, 'Las mejores galletas que he probado.');



SELECT * FROM tb_administradores;

SELECT * FROM tb_clientes;


SELECT * FROM tb_categorias;


SELECT * FROM tb_productos;

SELECT * FROM tb_detalles_pedidos;

SELECT * FROM tb_pedidos;

SELECT * FROM tb_detalles_pedidos;

SELECT * FROM tb_pedidos;

SELECT * FROM tb_comentarios;






