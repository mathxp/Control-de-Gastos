<?php
/**
 * SISTEMA DE CONTROL DE GASTOS - PROCESO DE AUTENTICACIÓN
 * Recibe los datos del formulario de login y valida al usuario.
 */

session_start(); // Iniciamos sesión para poder guardar datos del usuario si entra.

// Importamos la conexión a la BD y las funciones de seguridad CSRF
require_once "../conexion.php";
require_once "csrf.php";

// ==============================
// 1. VALIDAR MÉTODO DE ENVÍO
// ==============================
// Si alguien intenta entrar a este archivo escribiendo la URL directamente (GET),
// lo expulsamos. Solo aceptamos datos enviados por el formulario (POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// ==============================
// 2. VERIFICACIÓN DE SEGURIDAD (CSRF)
// ==============================
// Usamos la función que creamos arriba. Si el token no coincide, el script muere aquí.
// El '??' es un "null coalesce": si $_POST['csrf_token'] no existe, usa una cadena vacía ''.
csrf_validar($_POST['csrf_token'] ?? '');

// ==============================
// 3. SANITIZAR Y RECIBIR DATOS
// ==============================
// filter_input: Limpia el email. Si tiene caracteres ilegales, devuelve false.
$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? ''; // La contraseña no se sanitiza (puede tener símbolos raros), se toma tal cual.

// Si falta el email o la contraseña, devolvemos con error.
if (!$email || empty($password)) {
    header("Location: login.php?error=1");
    exit;
}

// ==============================
// 4. CONSULTA A LA BASE DE DATOS
// ==============================
// Buscamos al usuario por su email. 
// LIMIT 1: Buena práctica, le dice a la BD que pare de buscar apenas encuentre uno.
$sql = "SELECT id, nombre, password FROM usuarios WHERE email = :email LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->execute([
    ":email" => $email
]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obtenemos el resultado como un array asociativo.

// ==============================
// 5. VERIFICAR CONTRASEÑA (HASHING)
// ==============================
// password_verify: Magia pura. Compara la contraseña "plana" (ej: 12345) 
// contra el HASH encriptado de la base de datos (ej: $2y$10$Af2...).
if ($usuario && password_verify($password, $usuario['password'])) {

    // 🔐 SEGURIDAD CRÍTICA: Prevenir "Session Fixation"
    // Borra la ID de sesión anterior y genera una nueva limpia.
    // Esto evita que un hacker que haya capturado una ID de sesión vieja pueda usarla ahora que te logueaste.
    session_regenerate_id(true);

    // Guardamos los datos mínimos necesarios en la sesión
    $_SESSION['usuario_id']     = (int) $usuario['id']; // Forzamos a que sea número (int) por seguridad
    $_SESSION['usuario_nombre'] = $usuario['nombre'];

    // ¡Éxito! Redirigimos al panel principal
    header("Location: ../index.php");
    exit;
}

// ==============================
// 6. ERROR DE LOGIN
// ==============================
// Si llegamos aquí es porque el usuario no existe O la contraseña estaba mal.
// No le decimos al usuario cuál de los dos falló (por seguridad), solo "error genérico".
header("Location: login.php?error=1");
exit;
?>