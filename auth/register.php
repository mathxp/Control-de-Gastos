<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../static/css/auth.css">
</head>
<body>

<div class="auth-container">

    <h2>Crear cuenta</h2>

    <form action="register_post.php" method="POST">
        
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        
        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required
            minlength="8" 
            
            /* ATRIBUTO PATTERN:
               Permite poner la misma Regex que usamos en PHP.
               Si el usuario escribe una contraseña débil, el navegador
               no le dejará enviar el formulario y le mostrará una alerta.
            */
            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}"
            
            /*
               ATRIBUTO TITLE:
               Es el mensaje que el navegador muestra cuando la contraseña
               no cumple con el patrón de arriba.
            */
            title="Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo"
        />

        <button type="submit">Registrarse</button>
    </form>

    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>

</div>

</body>
</html>