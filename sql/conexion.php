<?php
// =========================
// Datos de conexión a MySQL
// =========================

// Servidor de base de datos (en Wamp es localhost)
$host = "localhost";

// Nombre de la base de datos
$dbname = "control_gastos";

// Usuario de MySQL (por defecto en Wamp)
$user = "root";

// Contraseña (en Wamp suele estar vacía)
$password = "";

// =========================
// Intento de conexión
// =========================
try {

    // Creamos un nuevo objeto PDO
    // mysql:host=... indica que usamos MySQL
    // charset=utf8mb4 permite caracteres especiales y acentos
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    // Configuramos PDO para que lance excepciones en caso de error
    // Esto hace que los errores sean visibles y fáciles de depurar
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    // Si la conexión falla, se detiene el programa
    // y muestra el error
    die("Error de conexión: " . $e->getMessage());
}
