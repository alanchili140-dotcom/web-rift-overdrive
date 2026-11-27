-- =====================================================================
-- TP N°8 · Programación Web · migracion.sql
-- Ejecutar UNA VEZ, después de tp6/schema.sql y tp6/consultas.sql.
-- Se puede volver a ejecutar sin problemas.
--
-- Qué hace:
--   1) Deja los roles como pide el enunciado del TP8: 'admin' y 'usuario'
--      (en el TP6 se llamaban 'administrador' y 'jugador'; 'editor' queda igual).
--   2) Le pone una contraseña real (hasheada con bcrypt) a dos cuentas de prueba
--      para poder probar el login. Las contraseñas de consultas.sql eran ficticias.
--
-- Cuentas de prueba (SOLO para la demo en local):
--   lucia@riftoverdrive.test  → rol admin    · contraseña: Rift#2026Demo
--   carla@example.test        → rol usuario  · contraseña: Rift#2026Demo
-- =====================================================================

USE rift_overdrive_db;

-- 1) Roles ------------------------------------------------------------
UPDATE roles SET nombre = 'admin',   descripcion = 'Acceso total: administra el blog y los usuarios'
WHERE nombre = 'administrador';

UPDATE roles SET nombre = 'usuario', descripcion = 'Usuario registrado en el sitio y la Beta'
WHERE nombre = 'jugador';

-- Por si la base se creó sin esos roles (INSERT IGNORE no duplica: nombre es UNIQUE)
INSERT IGNORE INTO roles (nombre, descripcion) VALUES
    ('admin',   'Acceso total: administra el blog y los usuarios'),
    ('usuario', 'Usuario registrado en el sitio y la Beta');

-- 2) Cuentas de prueba con contraseña bcrypt (password_hash de PHP) ------
UPDATE usuarios
SET password_hash = '$2y$10$9uvHN3Sreza5DkFDigh...j2lOfsVmKshM/6d61TzAI28mM/U9Tbu'
WHERE email IN ('lucia@riftoverdrive.test', 'carla@example.test');

-- Verificación: debe mostrar a Lucía con rol 'admin' y a Carla con rol 'usuario'
SELECT u.id, u.email, r.nombre AS rol
FROM usuarios u
INNER JOIN roles r ON r.id = u.rol_id
ORDER BY u.id;