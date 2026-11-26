# TP N°6 · Normalización aplicada a RIFT OVERDRIVE

Proyecto integrador: sitio web del videojuego **RIFT OVERDRIVE** (PHP, páginas `index.php`, `blog.php`, `login.php`, `clase5/opiniones.php`, etc.).

La entidad elegida para normalizar es la **entrada del blog (devlog)** de `blog.php`: reúne autor, rol del autor, categoría, estado y una lista de etiquetas. El resto de las tablas del sitio (usuarios, comentarios, opiniones, plataformas) salen de los formularios y se explican en el punto 3.

---

## 1. Entidad / tabla principal sin normalizar

Tabla `publicaciones_sin_normalizar`: un solo registro mezcla datos de la entrada, de su autor, del rol, de la categoría, del estado y una **lista de etiquetas en un único campo**.

| id_publicacion | titulo | autor_id | autor_nombre | autor_email | rol_id | rol_nombre | categoria_id | categoria_nombre | estado_id | estado_nombre | etiquetas |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | Devlog #1: Así funcionan los portales | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada | portales |
| 2 | Devlog #2: Primeros bocetos de enemigos | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada | enemigos, beta |
| 3 | Próximamente: modo cooperativo | 1 | Lucía Fernández | lucia@riftoverdrive.test | 1 | administrador | 2 | Novedades | 1 | borrador | beta |

**Problemas detectados**

- **Campo multivalorado:** `etiquetas` guarda una lista en una sola celda (viola 1FN).
- **Redundancia:** el nombre, el email y el rol de Martín se repiten en cada entrada suya; "Devlog" y "publicada" también se repiten.
- **Anomalías:**
  - *Actualización:* si Martín cambia de email hay que modificar varias filas.
  - *Borrado:* si se elimina la única entrada de "Novedades", se pierde la categoría.
  - *Inserción:* no se puede crear una categoría nueva hasta que exista una entrada que la use.

---

## 2. Proceso de normalización

### 1FN: atributos atómicos

Se separa la lista de etiquetas en **una fila por etiqueta**, de modo que cada celda tenga un único valor.

| id_publicacion | etiqueta | titulo | autor_id | autor_nombre | autor_email | rol_id | rol_nombre | categoria_id | categoria_nombre | estado_id | estado_nombre |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | portales | Devlog #1: Así funcionan los portales | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada |
| 2 | enemigos | Devlog #2: Primeros bocetos de enemigos | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada |
| 2 | beta | Devlog #2: Primeros bocetos de enemigos | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada |
| 3 | beta | Próximamente: modo cooperativo | 1 | Lucía Fernández | lucia@riftoverdrive.test | 1 | administrador | 2 | Novedades | 1 | borrador |

**PK compuesta temporal:** `(id_publicacion, etiqueta)`.

### 2FN: dependencia funcional completa

Con la PK `(id_publicacion, etiqueta)`, todas las columnas no-clave (`titulo`, `autor_*`, `rol_*`, `categoria_*`, `estado_*`) dependen **solo de `id_publicacion`** y no de `etiqueta`. Es una dependencia parcial, así que se descompone en dos tablas.

**`publicaciones`** (PK: `id_publicacion`)

| id_publicacion | titulo | autor_id | autor_nombre | autor_email | rol_id | rol_nombre | categoria_id | categoria_nombre | estado_id | estado_nombre |
|---|---|---|---|---|---|---|---|---|---|---|
| 1 | Devlog #1: Así funcionan los portales | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada |
| 2 | Devlog #2: Primeros bocetos de enemigos | 2 | Martín Gómez | martin@riftoverdrive.test | 2 | editor | 1 | Devlog | 2 | publicada |
| 3 | Próximamente: modo cooperativo | 1 | Lucía Fernández | lucia@riftoverdrive.test | 1 | administrador | 2 | Novedades | 1 | borrador |

**`publicacion_etiqueta`** (PK compuesta: `id_publicacion`, `etiqueta`)

| id_publicacion (FK) | etiqueta |
|---|---|
| 1 | portales |
| 2 | enemigos |
| 2 | beta |
| 3 | beta |

### 3FN: sin dependencias transitivas

En `publicaciones` hay atributos que dependen de otro atributo no-clave y no de la PK:

- `autor_nombre`, `autor_email` y `rol_id` dependen de `autor_id`.
- `rol_nombre` depende de `rol_id` (y por lo tanto, transitivamente, de `id_publicacion`).
- `categoria_nombre` depende de `categoria_id`.
- `estado_nombre` depende de `estado_id`.

Se extraen a sus propias tablas (`usuarios`, `roles`, `categorias`, `estados_publicacion`) y en `publicaciones` quedan solo las claves foráneas. El texto de la etiqueta pasa a la tabla `etiquetas` con un id, y `publicacion_etiqueta` queda como tabla intermedia de la relación N:M.

**Resultado:** cada dato se guarda una sola vez.

---

## 3. Listado de tablas resultante

El mismo criterio se aplicó a los formularios del sitio: las listas cerradas (`<select>` y `radio`) pasan a tablas propias en lugar de repetir texto.

| Tabla | De dónde sale en la web | Clave primaria (PK) | Claves foráneas (FK) | Otros campos |
|---|---|---|---|---|
| `roles` | tipos de cuenta (login) | `id` | — | `nombre` (único), `descripcion`, `created_at`, `updated_at` |
| `plataformas` | select "Plataforma" del registro | `id` | — | `nombre` (único) |
| `modos_juego` | radio "Modo de juego favorito" | `id` | — | `nombre` (único) |
| `usuarios` | registro Beta + login | `id` | `rol_id` → `roles.id`<br>`plataforma_id` → `plataformas.id`<br>`modo_juego_id` → `modos_juego.id` | `nombre_completo`, `apodo`, `email` (único), `password_hash`, `fecha_nacimiento`, `codigo_invitacion`, `comentarios`, `acepta_newsletter`, `estado`, `created_at`, `updated_at`, `deleted_at` |
| `categorias` | blog | `id` | — | `nombre` (único), `descripcion`, `created_at`, `updated_at` |
| `estados_publicacion` | blog | `id` | — | `nombre` (único) |
| `publicaciones` | entradas de `blog.php` | `id` | `usuario_id` → `usuarios.id`<br>`categoria_id` → `categorias.id`<br>`estado_id` → `estados_publicacion.id` | `titulo`, `contenido`, `fecha_publicacion`, `created_at`, `updated_at`, `deleted_at` |
| `etiquetas` | blog | `id` | — | `nombre` (único) |
| `publicacion_etiqueta` | relación N:M | (`publicacion_id`, `etiqueta_id`) | `publicacion_id` → `publicaciones.id`<br>`etiqueta_id` → `etiquetas.id` | — |
| `comentarios` | sección "Comentarios" del blog | `id` | `publicacion_id` → `publicaciones.id`<br>`usuario_id` → `usuarios.id` | `contenido`, `created_at`, `updated_at` |
| `tipos_opinion` | select "Tipo de opinión" | `id` | — | `nombre` (único) |
| `opiniones` | `clase5/opiniones.php` | `id` | `tipo_opinion_id` → `tipos_opinion.id` | `nombre`, `email`, `puntuacion` (1 a 5), `mensaje`, `created_at`, `updated_at` |

### Diagrama de relaciones

```
roles 1 ──── N usuarios N ──── 1 plataformas
                │  N  └──────── 1 modos_juego
                │
                │ 1 (autor)                1 (quien comenta)
                ▼                              │
        publicaciones N ──── 1 categorias      │
          │ 1    │ N                           │
          │      └──── 1 estados_publicacion   │
          │                                    │
          ├── N comentarios N ─────────────────┘
          │
          └── N publicacion_etiqueta N ──── 1 etiquetas

tipos_opinion 1 ──── N opiniones
```

Relaciones:

- 1:N: un rol, una plataforma o un modo de juego tienen muchos usuarios.
- 1:N: un usuario escribe muchas publicaciones y muchos comentarios.
- 1:N: una categoría, un estado y una publicación agrupan muchas entradas o comentarios.
- 1:N: un tipo de opinión tiene muchas opiniones.
- N:M: publicaciones y etiquetas, a través de `publicacion_etiqueta`.

### Decisiones de diseño

- `opiniones` guarda `nombre` y `email` como datos propios de cada opinión, porque el formulario de `opiniones.php` no exige tener cuenta.
- `usuarios.plataforma_id` y `modo_juego_id` admiten `NULL` para poder crear cuentas de administrador y editor, que no completan esos campos del registro de la Beta.
- El campo `password_hash` guarda el resultado de `password_hash()` de PHP, nunca la contraseña en texto plano.