<?php
/**
 * ELIMINACIÓN DE MOVIMIENTOS (CONFIRMACIÓN + ACCIÓN)
 * Este archivo maneja el borrado seguro de registros.
 */

session_start();

require_once "../auth/proteger.php";
require_once "../auth/csrf.php";
require_once "../conexion.php";

// =========================
// 1. OBTENCIÓN Y VALIDACIÓN BÁSICA DEL ID
// =========================
$id = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

// Si no hay ID en la URL, devolvemos al usuario al inicio.
if (!$id || !is_numeric($id)) {
    header("Location: ../index.php");
    exit;
}

// =========================
// 2. SEGURIDAD CRÍTICA (IDOR)
// =========================
// Antes de mostrar siquiera el botón de "Eliminar", verificamos la propiedad.
// Pregunta: "¿Existe este ID Y pertenece al usuario logueado?"
$stmt = $conexion->prepare("
    SELECT id FROM movimientos
    WHERE id = :id AND usuario_id = :usuario_id
");
$stmt->execute([
    ":id" => $id,
    ":usuario_id" => $usuario_id
]);

// Si el fetch falla, significa que el movimiento no existe O es de otro usuario.
// Lo sacamos inmediatamente.
if (!$stmt->fetch()) {
    header("Location: ../index.php");
    exit;
}

// =========================
// 3. PROCESAR LA ELIMINACIÓN (SOLO SI ES POST)
// =========================
// El código dentro de este bloque solo corre cuando el usuario hace clic en "Sí, eliminar".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🛡️ CSRF: Verificamos que la petición venga de nuestro formulario real.
    csrf_validar($_POST['csrf_token'] ?? '');

    // Ejecutamos el borrado real.
    // Volvemos a poner la condición 'AND usuario_id = ...' por redundancia de seguridad.
    $stmt = $conexion->prepare("
        DELETE FROM movimientos
        WHERE id = :id AND usuario_id = :usuario_id
    ");

    $stmt->execute([
        ":id" => $id,
        ":usuario_id" => $usuario_id
    ]);

    // Éxito: volvemos al panel principal.
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Movimiento</title>
    <link rel="stylesheet" href="../static/css/crud.css">
</head>
<body>

<div class="crud-container">

    <h2>Eliminar Movimiento</h2>

    <p class="alert alert-danger">
        ⚠️ ¿Estás seguro de eliminar este movimiento?<br>
        Esta acción no se puede deshacer.
    </p>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <button type="submit" class="btn-danger">Sí, eliminar</button>
    </form>

    <br>
    <a href="../index.php">Cancelar</a>

</div>

</body>
</html>