-- =====================================================================
-- TP N°6 · Programación Web · consultas.sql
-- Ejecutar DESPUÉS de schema.sql sobre una base recién creada
-- (los IDs de los INSERT asumen tablas vacías con AUTO_INCREMENT en 1).
-- =====================================================================

USE rift_overdrive_db;

-- =====================================================================
-- C · CREATE (datos iniciales)
-- =====================================================================

-- Tablas maestras / secundarias
INSERT INTO roles (nombre, descripcion) VALUES
    ('administrador', 'Gestiona usuarios y todo el contenido del sitio'),
    ('editor',        'Publica entradas del blog'),
    ('jugador',       'Usuario registrado en la Beta');

INSERT INTO plataformas (nombre) VALUES
    ('PC'), ('PlayStation 5'), ('Xbox Series X/S');

INSERT INTO modos_juego (nombre) VALUES
    ('Campaña'), ('Multijugador'), ('Ambos');

INSERT INTO categorias (nombre, descripcion) VALUES
    ('Devlog',    'Avances semanales del desarrollo'),
    ('Novedades', 'Anuncios generales del juego');

INSERT INTO estados_publicacion (nombre) VALUES
    ('borrador'), ('publicada'), ('archivada');

INSERT INTO etiquetas (nombre) VALUES
    ('portales'), ('enemigos'), ('beta');

INSERT INTO tipos_opinion (nombre) VALUES
    ('Sugerencia'), ('Reporte de error'), ('Elogio'), ('Otro');

-- Usuarios (los hashes son de ejemplo; en el TP7 se generan con password_hash())
INSERT INTO usuarios
    (rol_id, plataforma_id, modo_juego_id, nombre_completo, apodo, email,
     password_hash, fecha_nacimiento, codigo_invitacion, acepta_newsletter)
VALUES
    (1, NULL, NULL, 'Lucía Fernández', 'Lu',         'lucia@riftoverdrive.test',  '$2y$10$hashDeEjemploAdmin00000000000000000000000000000000000', '1998-05-20', NULL,        0),
    (2, NULL, NULL, 'Martín Gómez',    'Marto',      'martin@riftoverdrive.test', '$2y$10$hashDeEjemploEditor0000000000000000000000000000000000', '1997-11-02', NULL,        0),
    (3, 1,    1,    'Carla Díaz',      'Jugador_23', 'carla@example.test',        '$2y$10$hashDeEjemploJugador000000000000000000000000000000000', '2005-03-14', 'RIFT-1234', 1),
    (3, 2,    3,    'Pablo Ruiz',      'FPS_fan',    'pablo@example.test',        '$2y$10$hashDeEjemploJugador000000000000000000000000000000001', '2003-09-30', NULL,        0);

-- Tabla principal (vinculando claves foráneas: autor, categoría, estado)
INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, contenido, fecha_publicacion) VALUES
    (2, 1, 2, 'Devlog #1: Así funcionan los portales',
        'El equipo de programación terminó el sistema base de portales...', '2026-10-05 10:00:00'),
    (2, 1, 2, 'Devlog #2: Primeros bocetos de enemigos',
        'El equipo de arte compartió los primeros diseños de los robots enemigos...', '2026-10-12 10:00:00'),
    (1, 2, 1, 'Próximamente: modo cooperativo',
        'Estamos evaluando un modo cooperativo para una futura actualización...', NULL);

-- Relación N:M publicación <-> etiqueta
INSERT INTO publicacion_etiqueta (publicacion_id, etiqueta_id) VALUES
    (1, 1),
    (2, 2),
    (2, 3),
    (3, 3);

-- Comentarios y opiniones
INSERT INTO comentarios (publicacion_id, usuario_id, contenido) VALUES
    (1, 3, '¡Se ve buenísimo el sistema de portales!'),
    (1, 4, '¿Va a tener modo cooperativo?'),
    (2, 3, 'Comentario de prueba (se borra más abajo)');

INSERT INTO opiniones (tipo_opinion_id, nombre, email, puntuacion, mensaje) VALUES
    (1, 'Carla Díaz', 'carla@example.test', 5, 'Sería genial poder jugar con amigos en cooperativo.'),
    (2, 'Pablo Ruiz', 'pablo@example.test', 3, 'Encontré un bug de colisión al atravesar un portal.');

-- =====================================================================
-- R · READ
-- =====================================================================

-- 1) WHERE + ORDER BY: entradas del blog ya publicadas, las más nuevas primero
SELECT id, titulo, fecha_publicacion
FROM publicaciones
WHERE estado_id = 2
ORDER BY fecha_publicacion DESC;

-- 2) INNER JOIN: título + autor + categoría + estado
SELECT p.id,
       p.titulo,
       u.nombre_completo AS autor,
       c.nombre          AS categoria,
       e.nombre          AS estado
FROM publicaciones p
INNER JOIN usuarios u            ON u.id = p.usuario_id
INNER JOIN categorias c          ON c.id = p.categoria_id
INNER JOIN estados_publicacion e ON e.id = p.estado_id
ORDER BY p.id;

-- 3) INNER JOIN: comentarios de cada entrada con el apodo de quien comentó
SELECT p.titulo, u.apodo, cm.contenido, cm.created_at
FROM comentarios cm
INNER JOIN publicaciones p ON p.id = cm.publicacion_id
INNER JOIN usuarios u      ON u.id = cm.usuario_id
ORDER BY p.id, cm.created_at;

-- 4) INNER JOIN con la tabla N:M: entradas con sus etiquetas
SELECT p.titulo,
       GROUP_CONCAT(et.nombre ORDER BY et.nombre SEPARATOR ', ') AS etiquetas
FROM publicaciones p
INNER JOIN publicacion_etiqueta pe ON pe.publicacion_id = p.id
INNER JOIN etiquetas et            ON et.id = pe.etiqueta_id
GROUP BY p.id, p.titulo;

-- 5) INNER JOIN: jugadores registrados en la Beta con su plataforma y modo favorito
SELECT u.apodo, u.email, pl.nombre AS plataforma, m.nombre AS modo_favorito
FROM usuarios u
INNER JOIN roles r        ON r.id  = u.rol_id
INNER JOIN plataformas pl ON pl.id = u.plataforma_id
INNER JOIN modos_juego m  ON m.id  = u.modo_juego_id
WHERE r.nombre = 'jugador'
ORDER BY u.apodo;

-- =====================================================================
-- U · UPDATE (siempre con WHERE)
-- =====================================================================

-- Publicar el borrador del modo cooperativo
UPDATE publicaciones
SET estado_id = 2,
    fecha_publicacion = NOW()
WHERE id = 3;

-- =====================================================================
-- D · DELETE (siempre con WHERE)
-- =====================================================================

-- Dar de baja el comentario de prueba
DELETE FROM comentarios
WHERE id = 3;

-- Verificación final
SELECT id, titulo, estado_id FROM publicaciones;
SELECT id, publicacion_id, contenido FROM comentarios;
