CREATE DATABASE IF NOT EXISTS condominio;
USE condominio;

-- Tabla de residentes
CREATE TABLE IF NOT EXISTS residentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    unidad VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL
);

-- Tabla de pagos
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidad VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    monto_bs DECIMAL(10, 2) NOT NULL,
    referencia VARCHAR(50) NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    FOREIGN KEY (unidad) REFERENCES residentes(unidad)
);

-- Tabla de recibos mensuales
CREATE TABLE IF NOT EXISTS recibos_mensuales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mes VARCHAR(20) NOT NULL,
    anno INT NOT NULL,
    monto_total DECIMAL(10, 2) NOT NULL,
    monto_total_bs DECIMAL(10, 2) NOT NULL
);

-- Tabla de detalles de gastos
CREATE TABLE IF NOT EXISTS detalles_gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recibo_id INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (recibo_id) REFERENCES recibos_mensuales(id)
);

-- Tabla de deudas por residente
CREATE TABLE IF NOT EXISTS deudas_residentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    residente_id INT NOT NULL,
    recibo_id INT NOT NULL,
    monto_deuda DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (residente_id) REFERENCES residentes(id),
    FOREIGN KEY (recibo_id) REFERENCES recibos_mensuales(id)
);

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL
);