<?php
/**
 * FORMULARIO - NUEVO MOVIMIENTO
 * Muestra la interfaz para registrar un ingreso o gasto.
 */

session_start();

// Importamos seguridad y conexión
require_once "../auth/proteger.php"; // Bloquea acceso si no hay login
require_once "../auth/csrf.php";     // Herramientas anti-hackeo de formularios
require_once "../conexion.php";

// Validamos que la sesión esté correcta (doble seguridad)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// =========================
// CARGA DE DATOS PREVIOS
// =========================
// Necesitamos las categorías para llenar el <select> del formulario.
// Sin esto, el usuario no sabría qué ID poner.
$stmt = $conexion->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Movimiento</title>
    <link rel="stylesheet" href="../static/css/crud.css">
</head>
<body>

<div class="crud-container">

    <h2>Nuevo Movimiento</h2>

    <form action="insertar.php" method="POST">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <label>Tipo</label>
        <select name="tipo" required>
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
        </select>

        <label>Monto</label>
        <input type="number" name="monto" step="0.01" min="1" required>

        <label>Fecha</label>
        <input type="date" name="fecha" required>

        <label>Categoría</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['id'] ?>">
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descripción</label>
        <input type="text" name="descripcion">

        <button type="submit">Guardar</button>
    </form>

    <a href="../index.php">⬅ Volver</a>

</div>

</body>
</html>