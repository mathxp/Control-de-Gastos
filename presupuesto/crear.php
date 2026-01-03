<?php
/**
 * PRESUPUESTO MENSUAL - VISTA DE FORMULARIO
 * Permite al usuario definir o editar su límite de gastos para un mes específico.
 */

session_start();

require_once "../auth/proteger.php";
require_once "../auth/csrf.php";
require_once "../conexion.php";

/* =========================
   1. CAPTURA Y VALIDACIÓN DE LA URL (GET)
========================= */
// filter_input es más seguro que usar $_GET directo.
// Aquí validamos que 'mes' sea un entero entre 1 y 12.
// Si la URL no trae nada (null), usamos el operador '??' para poner el mes actual (date('m')).
$mes  = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 12]
]) ?? date('m');

// Lo mismo para el año. Validamos un rango razonable (2000-2100).
$anio = filter_input(INPUT_GET, 'anio', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 2000, 'max_range' => 2100]
]) ?? date('Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto mensual</title>
    <link rel="stylesheet" href="../static/css/crud.css">
</head>
<body>

<div class="crud-container">

    <h2>Definir presupuesto para <?= htmlspecialchars($mes) ?>/<?= htmlspecialchars($anio) ?></h2>

    <form action="guardar.php" method="POST">

        <input type="hidden" name="mes" value="<?= htmlspecialchars($mes) ?>">
        <input type="hidden" name="anio" value="<?= htmlspecialchars($anio) ?>">

        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <label>Monto máximo de gastos</label>
        <input
            type="number"
            name="monto"
            step="0.01"
            min="1"
            placeholder="Ej: 500000"
            required
        >

        <button type="submit">Guardar presupuesto</button>
    </form>

    <a href="../index.php">⬅ Volver</a>

</div>

</body>
</html>