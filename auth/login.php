<?php
/**
 * LOGIN DE USUARIO (VISTA)
 * Muestra el formulario HTML para que el usuario ingrese sus credenciales.
 */

session_start(); // Necesario para acceder a $_SESSION donde guardaremos el token.
require_once "csrf.php"; // Traemos las herramientas de seguridad.

// Generamos el token CSRF único para esta visita.
// Esto crea el "secreto" que el formulario debe devolver para ser aceptado.
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../static/css/auth.css">
</head>
<body>

<div class="auth-container">

    <h2>Iniciar sesión</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            ❌ Email o contraseña incorrectos
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['logout'])): ?>
        <div class="success">
            ✅ Sesión cerrada correctamente
        </div>
    <?php endif; ?>

    <form action="login_post.php" method="POST">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
            autocomplete="email" 
        >
        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required
            autocomplete="current-password"
        >

        <button type="submit">Ingresar</button>
    </form>

    <a href="register.php">Crear cuenta</a>

</div>

</body>
</html>