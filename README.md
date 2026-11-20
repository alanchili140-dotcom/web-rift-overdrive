![alt text](image.png)# RIFT OVERDRIVE — Sitio web (TP4: migración a PHP, SSI y SSR)

Sitio web escolar de **RIFT OVERDRIVE**, un videojuego FPS futurista con portales (proyecto ficticio de la Escuela Técnica).
En esta etapa (Unidad 4) el sitio estático HTML/CSS/JS se migró a **PHP**: las páginas ahora se generan en el servidor (SSR),
las partes repetidas (encabezado, menú y pie) se reutilizan con **Server Side Includes** y la configuración global vive en un archivo **.env**.

## Prototipo en Figma

> 🔗 PEGAR ACÁ el enlace público (o con permiso de lectura) del diseño en Figma.

## Tecnologías utilizadas

- HTML5 y CSS3
- JavaScript (menú desplegable, acordeón, lightbox, modo claro/oscuro, validación y resumen del formulario)
- PHP 8.x (`include`/`require_once`, variables, constantes)
- XAMPP o Laragon (servidor local)
- Git y GitHub

## Estructura del proyecto

```
├── config/
│   └── env.php            # lee el .env y define las constantes (APP_NAME, APP_EMAIL, APP_ENV)
├── includes/
│   ├── header.php         # <head>, metadatos, CSS y <title> dinámico
│   ├── nav.php            # logo + menú de navegación (con clase "active")
│   └── footer.php         # pie de página + scripts de JavaScript
├── css/  js/  img/
├── index.php  blog.php  galeria.php  contacto.php  descarga.php
├── login.php  preguntas.php  quienes-somos.php  terminos.php  CV.php
├── .env.example           # plantilla de variables de entorno
└── .gitignore             # excluye el .env real
```

## Cómo funciona

Cada página define sus variables y arma la vista con los includes:

```php
<?php
$titulo_pagina = 'Contacto — RIFT OVERDRIVE';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main> ... contenido de la página ... </main>
<?php require_once 'includes/footer.php'; ?>
```

- **Título dinámico:** `header.php` imprime `$titulo_pagina` dentro de `<title>`.
- **Ítem activo del menú:** `nav.php` compara la página actual (`basename($_SERVER['PHP_SELF'])`) con cada link y agrega `class="active"`.
- **Variables de entorno:** `config/env.php` lee el `.env` y define `APP_NAME`, `APP_EMAIL` y `APP_ENV`, que se usan en el pie de página y en los correos de contacto.

## Instrucciones de instalación y ejecución local

1. Instalá **XAMPP** (o Laragon).
2. Cloná el repositorio dentro de `htdocs` (XAMPP) o `www` (Laragon):
   ```bash
   git clone https://github.com/alanchili140-dotcom/web-rift-overdrive.git
   ```
3. Copiá la plantilla de variables de entorno y editá los valores si querés:
   ```bash
   cp .env.example .env
   ```
4. Iniciá **Apache** desde el panel de XAMPP/Laragon.
5. Abrí en el navegador: `http://localhost/web-rift-overdrive/index.php`

> Alternativa rápida sin XAMPP, si tenés PHP instalado: `php -S localhost:8000` dentro de la carpeta del proyecto.

## Evidencia de funcionamiento

### Servidor local ejecutándose
> 📸 Captura del sitio en `http://localhost/...`

### Estructura modular (SSI)
> 📸 Capturas de `includes/header.php`, `includes/nav.php` y `includes/footer.php`

### Navegación dinámica
> 📸 Captura con el título cambiando en la pestaña y la clase `active` en el menú

### Variables de entorno
> 📸 Captura del archivo `.env.example` en el repositorio

## Autor

Alan David Chiliguay — Escuela Técnica, Programación Web.
