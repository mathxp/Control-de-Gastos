<?php
// auth/proteger.php
// ESTE ARCHIVO ES UN "MIDDLEWARE" DE SEGURIDAD.
// Se debe incluir al principio de cualquier archivo que requiera estar logueado.

// 1. GESTIÓN INTELIGENTE DE LA SESIÓN
// El problema común: Si index.php ya hizo session_start() y llamas a este archivo,
// PHP lanzaría un error "Notice".
// La solución: Preguntamos "¿Está la sesión apagada (NONE)?". 
// Solo si está apagada, la iniciamos. Si ya está activa, no hacemos nada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. VALIDACIÓN DE IDENTIDAD
// Verificamos si existe la variable 'usuario_id' en la sesión del servidor.
// Esta variable solo se crea si el usuario pasó exitosamente por login_post.php.
if (!isset($_SESSION['usuario_id'])) {
    
    // 3. EXPULSIÓN (REDIRECCIÓN)
    // Si no está la variable, el usuario no está logueado. Lo mandamos al login.
    
    // IMPORTANTE SOBRE LA RUTA:
    // Usamos "/control-gastos/auth/login.php" (Ruta Absoluta).
    // Esto asegura que el enlace funcione igual si estás en la carpeta raíz 
    // o si estás metido en subcarpetas profundas (ej: movimientos/detalle/ver.php).
    header("Location: /control-gastos/auth/login.php");
    
    // exit: VITAL. Detiene la ejecución del script inmediatamente.
    // Si no pones exit, el código de abajo (el contenido protegido) se seguiría ejecutando 
    // en segundo plano aunque el navegador cambie de página.
    exit;
}

// Si el código llega hasta aquí, significa que el usuario tiene permiso.
// El script que llamó a este archivo continuará ejecutándose normalmente.
?>