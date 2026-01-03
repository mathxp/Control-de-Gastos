<?php
/**
 * FORMULARIO Y LÓGICA - EDITAR MOVIMIENTO
 * Este archivo es "híbrido": muestra el formulario y procesa la actualización.
 */

session_start();

require_once "../auth/proteger.php";
require_once "../auth/csrf.php";
require_once "../conexion.php";

// =========================
// 1. VALIDAR ID DE LA URL
// =========================
// Capturamos ?id=X de la barra de direcciones.
$id = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

// Si no hay ID o si ponen letras (?id=abc), los expulsamos.
if (!$id || !is_numeric($id)) {
    header("Location: ../index.php");
    exit;
}

// =========================
// 2. SEGURIDAD IDOR (CRÍTICO)
// =========================
// Buscamos el movimiento, PERO exigimos que el 'usuario_id' coincida con la sesión.
// Si el movimiento 50 existe pero es de otro usuario, esta consulta no devolverá nada.
$stmt = $conexion->prepare("
    SELECT * FROM movimientos
    WHERE id = :id AND usuario_id = :usuario_id
");
$stmt->execute([
    ":id" => $id,
    ":usuario_id" => $usuario_id
]);

$movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encontró (o no es suyo), fuera.
if (!$movimiento) {
    header("Location: ../index.php");
    exit;
}

// Carga de categorías para el desplegable
$categorias = $conexion->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

// =========================
// 3. PROCESO DE ACTUALIZACIÓN (MÉTODO POST)
// =========================
// Este bloque solo se ejecuta cuando le das al botón "Actualizar".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🛡️ Verificar que el formulario viene de nuestro sitio
    csrf_validar($_POST['csrf_token'] ?? '');

    // VALIDACIONES DE DATOS
    if (
        empty($_POST['tipo']) ||
        empty($_POST['monto']) ||
        empty($_POST['fecha']) ||
        empty($_POST['categoria_id'])
    ) {
        die("❌ Datos incompletos");
    }

    // WHITELISTING: Solo permitimos estas dos palabras exactas.
    if (!in_array($_POST['tipo'], ['ingreso', 'gasto'])) {
        die("❌ Tipo inválido");
    }

    if (!is_numeric($_POST['monto']) || $_POST['monto'] <= 0) {
        die("❌ Monto inválido");
    }

    // SANITIZAR VARIABLES
    $tipo        = $_POST['tipo'];
    $monto       = $_POST['monto'];
    $fecha       = $_POST['fecha'];
    $categoriaId = $_POST['categoria_id'];
    $descripcion = trim($_POST['descripcion'] ?? '');

    // UPDATE SQL
    // De nuevo, incluimos "AND usuario_id = :usuario_id" en el UPDATE.
    // Es una medida de seguridad redundante pero necesaria.
    $sql = "
    UPDATE movimientos SET
        tipo = :tipo,
        monto = :monto,
        fecha = :fecha,
        descripcion = :descripcion,
        categoria_id = :categoria_id
    WHERE id = :id AND usuario_id = :usuario_id
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ":tipo"         => $tipo,
        ":monto"        => $monto,
        ":fecha"        => $fecha,
        ":descripcion"  => $descripcion,
        ":categoria_id" => $categoriaId,
        ":id"           => $id,
        ":usuario_id"   => $usuario_id
    ]);

    // Éxito: volvemos a la página principal
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Movimiento</title>
    <link rel="stylesheet" href="../static/css/crud.css">
</head>
<body>

<div class="crud-container">

    <h2>Editar Movimiento</h2>

    <form method="POST">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <label>Tipo</label>
        <select name="tipo">
            <option value="ingreso" <?= $movimiento['tipo'] === 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
            <option value="gasto" <?= $movimiento['tipo'] === 'gasto' ? 'selected' : '' ?>>Gasto</option>
        </select>

        <label>Monto</label>
        <input type="number" step="0.01" name="monto" value="<?= $movimiento['monto'] ?>" required>

        <label>Fecha</label>
        <input type="date" name="fecha" value="<?= $movimiento['fecha'] ?>" required>

        <label>Categoría</label>
        <select name="categoria_id">
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= $movimiento['categoria_id'] == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descripción</label>
        <input type="text" name="descripcion" value="<?= htmlspecialchars($movimiento['descripcion']) ?>">

        <button type="submit">Actualizar</button>
    </form>

    <a href="../index.php">⬅ Volver</a>

</div>

</body>
</html>