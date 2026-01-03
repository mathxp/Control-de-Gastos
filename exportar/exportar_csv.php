<?php
/**
 * SISTEMA DE CONTROL DE GASTOS - EXPORTACIÓN DE DATOS
 * Este script genera un archivo descargable en formato CSV filtrado por mes y año.
 */

// 1. Configuración de errores: En exportaciones es vital que no se "cuele" ningún
// mensaje de error de PHP dentro del archivo de Excel, o el archivo se dañará.
error_reporting(0);
ini_set('display_errors', 0);
require_once "auth/proteger.php";
require_once __DIR__ . '/../conexion.php';

// Validar que la variable de conexión PDO exista
if (!isset($conexion)) {
    die('Error de conexión');
}

/**
 * 2. CAPTURA DE FILTROS
 * Usamos el operador null coalescing (??) para establecer valores por defecto
 * (mes y año actual) si el usuario no selecciona ninguno.
 */
$mes  = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

$filename = "movimientos_{$mes}_{$anio}.csv";

/**
 * 3. CABECERAS HTTP (HTTP HEADERS)
 * Estas líneas le dicen al navegador que lo que viene no es una página web,
 * sino un archivo descargable de tipo CSV.
 */
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=$filename");

/**
 * 4. EL TRUCO DEL BOM UTF-8 (\xEF\xBB\xBF)
 * Excel tiene problemas para reconocer tildes y eñes en archivos CSV.
 * Al imprimir estos bytes al inicio, obligamos a Excel a leerlo como UTF-8 correctamente.
 */
echo "\xEF\xBB\xBF";

// 5. Abrir el flujo de salida "php://output" para escribir directamente en la descarga
$output = fopen('php://output', 'w');

// 6. Escribir la primera fila del CSV (los títulos de las columnas)
fputcsv($output, [
    'Fecha',
    'Tipo',
    'Monto',
    'Descripción',
    'Categoría'
]);

/**
 * 7. CONSULTA A LA BASE DE DATOS
 * Obtenemos los movimientos cruzando (JOIN) con la tabla categorías.
 * Filtramos usando funciones de MySQL: MONTH() y YEAR().
 */
$sql = "
    SELECT 
        m.fecha,
        m.tipo,
        m.monto,
        m.descripcion,
        c.nombre AS categoria
    FROM movimientos m
    INNER JOIN categorias c ON m.categoria_id = c.id
    WHERE MONTH(m.fecha) = :mes
      AND YEAR(m.fecha) = :anio
    ORDER BY m.fecha DESC
";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':mes' => $mes,
    ':anio' => $anio
]);

/**
 * 8. RECORRIDO Y ESCRITURA DE DATOS
 * Mientras haya filas en la BD, se escriben en el archivo CSV usando fputcsv.
 * fputcsv se encarga de poner las comas y comillas necesarias automáticamente.
 */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['fecha'],
        ucfirst($row['tipo']), // Pone la primera letra en mayúscula (Ingreso/Gasto)
        $row['monto'],
        $row['descripcion'],
        $row['categoria']
    ]);
}

// 9. Cerrar el flujo de datos y finalizar el script
fclose($output);
exit;