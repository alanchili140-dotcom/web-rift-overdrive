<?php
/**
 * logout.php — Cierre de sesión (TP8)
 *
 * 1) Vacía $_SESSION  2) Borra la cookie de sesión del navegador
 * 3) Destruye la sesión en el servidor  4) Redirige al login
 * (los tres pasos están en cerrar_sesion_completa(), config/sesion.php)
 */
require_once __DIR__ . '/config/sesion.php';

iniciar_sesion_segura();
cerrar_sesion_completa();

redirigir('login.php?salio=1');
