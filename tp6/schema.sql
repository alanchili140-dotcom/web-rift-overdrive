-- =====================================================================
-- TP N°6 · Programación Web · schema.sql
-- Proyecto: RIFT OVERDRIVE (sitio web del videojuego)
-- Motor: MySQL 8 / MariaDB 10.4+ (XAMPP, Laragon, phpMyAdmin)
-- Re-ejecutable: usa IF NOT EXISTS.
--
-- Páginas del sitio que persisten datos con este esquema:
--   index.php#registro  -> usuarios (+ plataformas, modos_juego)
--   login.php           -> usuarios (email + password_hash) y roles
--   blog.php            -> publicaciones, categorias, etiquetas, comentarios
--   clase5/opiniones.php-> opiniones (+ tipos_opinion)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS rift_overdrive_db
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE rift_overdrive_db;

-- ---------------------------------------------------------------------
-- 1) Tablas maestras / secundarias (listas cerradas de valores)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Select "Plataforma" del formulario de registro (PC, PS5, Xbox)
CREATE TABLE IF NOT EXISTS plataformas (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Radio "Modo de juego favorito" (campaña, multijugador, ambos)
CREATE TABLE IF NOT EXISTS modos_juego (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS estados_publicacion (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS etiquetas (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Select "Tipo de opinión" (sugerencia, bug, elogio, otro)
CREATE TABLE IF NOT EXISTS tipos_opinion (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2) Usuarios: autenticación (login), rol y datos personales (registro)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS usuarios (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    rol_id             INT          NOT NULL,
    plataforma_id      INT          NULL,
    modo_juego_id      INT          NULL,
    nombre_completo    VARCHAR(80)  NOT NULL,
    apodo              VARCHAR(20)  NULL,
    email              VARCHAR(150) NOT NULL,
    password_hash      VARCHAR(255) NOT NULL,        -- password_hash() de PHP, nunca texto plano
    fecha_nacimiento   DATE         NOT NULL,
    codigo_invitacion  VARCHAR(9)   NULL,            -- formato RIFT-1234
    comentarios        TEXT         NULL,
    acepta_newsletter  TINYINT      NOT NULL DEFAULT 0,
    estado             TINYINT      NOT NULL DEFAULT 1, -- 1 = activo, 0 = inactivo
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at         TIMESTAMP NULL,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id)        REFERENCES roles(id),
    CONSTRAINT fk_usuarios_plataforma
        FOREIGN KEY (plataforma_id) REFERENCES plataformas(id),
    CONSTRAINT fk_usuarios_modo
        FOREIGN KEY (modo_juego_id) REFERENCES modos_juego(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3) Tabla principal de negocio: publicaciones (entradas del blog / devlog)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS publicaciones (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id        INT          NOT NULL,   -- autor
    categoria_id      INT          NOT NULL,
    estado_id         INT          NOT NULL,
    titulo            VARCHAR(150) NOT NULL,
    contenido         TEXT         NOT NULL,
    fecha_publicacion DATETIME     NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at        TIMESTAMP NULL,
    CONSTRAINT fk_publicaciones_usuario
        FOREIGN KEY (usuario_id)   REFERENCES usuarios(id),
    CONSTRAINT fk_publicaciones_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    CONSTRAINT fk_publicaciones_estado
        FOREIGN KEY (estado_id)    REFERENCES estados_publicacion(id)
) ENGINE=InnoDB;

-- Comentarios del blog (sección "Comentarios" de blog.php)
CREATE TABLE IF NOT EXISTS comentarios (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT          NOT NULL,
    usuario_id     INT          NOT NULL,
    contenido      VARCHAR(500) NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comentarios_publicacion
        FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_comentarios_usuario
        FOREIGN KEY (usuario_id)     REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Opiniones enviadas desde clase5/opiniones.php (no requieren cuenta)
CREATE TABLE IF NOT EXISTS opiniones (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tipo_opinion_id INT          NOT NULL,
    nombre          VARCHAR(40)  NOT NULL,
    email           VARCHAR(150) NOT NULL,
    puntuacion      TINYINT      NOT NULL,
    mensaje         VARCHAR(500) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_opiniones_puntuacion CHECK (puntuacion BETWEEN 1 AND 5),
    CONSTRAINT fk_opiniones_tipo
        FOREIGN KEY (tipo_opinion_id) REFERENCES tipos_opinion(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4) Tabla de relación N:M: publicacion_etiqueta
--    (PK compuesta: evita repetir el mismo par; ON DELETE CASCADE limpia
--     el detalle al borrar una publicación o una etiqueta)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS publicacion_etiqueta (
    publicacion_id INT NOT NULL,
    etiqueta_id    INT NOT NULL,
    PRIMARY KEY (publicacion_id, etiqueta_id),
    CONSTRAINT fk_pe_publicacion
        FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_pe_etiqueta
        FOREIGN KEY (etiqueta_id)    REFERENCES etiquetas(id)     ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5) Índices explícitos
-- ---------------------------------------------------------------------

-- Login (login.php) y búsqueda de usuarios por email: alta cardinalidad.
-- Al ser UNIQUE también impide registrar dos veces el mismo correo.
CREATE UNIQUE INDEX idx_usuarios_email ON usuarios(email);

-- Listado del blog: filtrar por categoría y estado, ordenar por fecha.
CREATE INDEX idx_publicaciones_categoria_estado
    ON publicaciones(categoria_id, estado_id, fecha_publicacion);
