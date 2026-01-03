<?php
/**
 * SISTEMA DE CONTROL DE GASTOS - DASHBOARD PRINCIPAL
 * Este archivo actúa como el "panel de control".
 * Muestra resumen, tablas, alertas de presupuesto y gráficos.
 */

// Configuración de errores para desarrollo (quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "auth/proteger.php"; // Bloqueo de seguridad
require_once "conexion.php";

/* =============================
   1. CONTROL DE ACCESO
============================= */
// Aunque proteger.php ya lo hace, esta validación explícita nunca está de más.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];

/* =============================
   2. VALIDACIÓN DE FILTROS (GET)
============================= */
// Capturamos ?mes=X&anio=Y de la URL.
// Si no vienen datos, usamos el mes y año actual (date).
$mes  = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 12]
]) ?? (int) date('m');

$anio = filter_input(INPUT_GET, 'anio', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 2000, 'max_range' => 2100]
]) ?? (int) date('Y');

/* =============================
   3. CONSULTA DE MOVIMIENTOS (CON JOIN)
============================= */
// Usamos INNER JOIN para traer el nombre de la categoría (c.nombre)
// en lugar de solo mostrar el número ID.
$stmt = $conexion->prepare("
    SELECT m.*, c.nombre AS categoria
    FROM movimientos m
    JOIN categorias c ON m.categoria_id = c.id
    WHERE m.usuario_id = :usuario_id
    AND MONTH(m.fecha) = :mes
    AND YEAR(m.fecha) = :anio
    ORDER BY m.fecha DESC
");

$stmt->execute([
    ":usuario_id" => $usuario_id,
    ":mes" => $mes,
    ":anio" => $anio
]);

$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =============================
   4. CÁLCULO DE TOTALES (RESUMEN)
============================= */
// Recorremos el array en PHP para sumar ingresos y gastos.
$ingresos = 0;
$gastos   = 0;

foreach ($movimientos as $m) {
    if ($m['tipo'] === 'ingreso') {
        $ingresos += (float) $m['monto'];
    } else {
        $gastos += (float) $m['monto'];
    }
}
$balance = $ingresos - $gastos;

/* =============================
   5. LÓGICA DE PRESUPUESTO
============================= */
$stmt = $conexion->prepare("
    SELECT monto FROM presupuestos
    WHERE usuario_id = :usuario_id AND mes = :mes AND anio = :anio
");

$stmt->execute([
    ":usuario_id" => $usuario_id,
    ":mes" => $mes,
    ":anio" => $anio
]);

$presupuesto = $stmt->fetchColumn(); // Obtiene solo el valor (o false si no hay)

// Variables para la interfaz gráfica (barra de progreso y colores)
$porcentaje = null;
$estadoPresupuesto = null; // Clase CSS: 'ok', 'warning', 'danger'

if ($presupuesto && $presupuesto > 0) {
    $porcentaje = ($gastos / $presupuesto) * 100;

    // Lógica de semáforo:
    if ($porcentaje < 70) {
        $estadoPresupuesto = "ok";      // Verde (Poco gasto)
    } elseif ($porcentaje < 100) {
        $estadoPresupuesto = "warning"; // Amarillo (Cuidado)
    } else {
        $estadoPresupuesto = "danger";  // Rojo (Te pasaste)
    }
}

/* =============================
   6. DATOS PARA EL GRÁFICO (AGRUPACIÓN SQL)
============================= */
// Aquí le pedimos a la BD que agrupe los gastos por categoría y los sume.
// Es mucho más eficiente que hacerlo con un bucle en PHP.
$stmt = $conexion->prepare("
    SELECT c.nombre, SUM(m.monto) AS total
    FROM movimientos m
    JOIN categorias c ON m.categoria_id = c.id
    WHERE m.usuario_id = :usuario_id
    AND m.tipo = 'gasto'
    AND MONTH(m.fecha) = :mes
    AND YEAR(m.fecha) = :anio
    GROUP BY c.nombre
");

$stmt->execute([
    ":usuario_id" => $usuario_id,
    ":mes" => $mes,
    ":anio" => $anio
]);

$grafico = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separamos los datos en dos arrays simples para pasarlos a JavaScript
$labels  = array_column($grafico, 'nombre'); // Ej: ['Comida', 'Transporte']
$valores = array_column($grafico, 'total');  // Ej: [5000, 2000]
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Gastos</title>
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>

<div class="container">

    <h1>Hola <?= htmlspecialchars($_SESSION['usuario_nombre']) ?> 👋</h1>

    <div class="resumen">
        <div class="card ingreso">
            <h3>Ingresos</h3>
            <p>$<?= number_format($ingresos, 2) ?></p>
        </div>
        <div class="card gasto">
            <h3>Gastos</h3>
            <p>$<?= number_format($gastos, 2) ?></p>
        </div>
        <div class="card balance">
            <h3>Balance</h3>
            <p>$<?= number_format($balance, 2) ?></p>
        </div>
    </div>

    <?php if ($presupuesto): ?>
        <div class="presupuesto <?= $estadoPresupuesto ?>">
            <p>Presupuesto: $<?= number_format($presupuesto, 2) ?></p>
            <p>Uso: <?= round($porcentaje, 1) ?>%</p>
            </div>
    <?php else: ?>
        <a href="presupuesto/crear.php?mes=<?= $mes ?>&anio=<?= $anio ?>">
            ➕ Definir presupuesto
        </a>
    <?php endif; ?>

    <div class="toolbar">
        <form method="GET" class="filtro-form">
            <input type="number" name="mes" min="1" max="12" value="<?= $mes ?>">
            <input type="number" name="anio" value="<?= $anio ?>">
            <button type="submit">Filtrar</button>
        </form>

        <div class="acciones">
            <a href="movimientos/crear.php" class="btn">➕ Nuevo</a>
            <a href="exportar/exportar_csv.php?mes=<?= $mes ?>&anio=<?= $anio ?>" class="btn btn-success">📤 Exportar</a>
            <a href="auth/logout.php" class="btn btn-danger">🚪 Salir</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if ($movimientos): ?>
            <?php foreach ($movimientos as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['fecha']) ?></td>
                    <td class="<?= $m['tipo'] ?>"><?= ucfirst($m['tipo']) ?></td>
                    <td>$<?= number_format($m['monto'], 2) ?></td>
                    <td><?= htmlspecialchars($m['categoria']) ?></td>
                    <td><?= htmlspecialchars($m['descripcion']) ?></td>
                    <td>
                        <a href="movimientos/editar.php?id=<?= (int)$m['id'] ?>">✏️</a>
                        <a href="movimientos/borrar.php?id=<?= (int)$m['id'] ?>">🗑</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Sin registros este mes</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="grafico-container">
        <h2>Distribución de gastos</h2>
        <canvas id="graficoGastos"></canvas>
    </div>

</div>

<script>
// Pasamos los datos de PHP a variables de JavaScript
// json_encode convierte el array PHP en un array [Text] válido para JS.
const labels  = <?= json_encode($labels) ?>;
const valores = <?= json_encode($valores) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="static/js/charts.js"></script>

</body>
</html>