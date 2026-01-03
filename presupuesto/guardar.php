<?php
/**
 * PROCESAR PRESUPUESTO (UPSERT)
 * Guarda un nuevo presupuesto O actualiza el existente si ya había uno.
 */

session_start();

require_once "../auth/proteger.php";
require_once "../auth/csrf.php";
require_once "../conexion.php";

// =========================
// 1. CONTROL DE MÉTODO
// =========================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// =========================
// 2. VERIFICACIÓN MANUAL DE CSRF
// =========================
// Aquí estás haciendo la verificación "a mano" en lugar de usar la función csrf_validar().
// Es válido, pero recuerda que podrías usar csrf_validar($_POST['csrf_token'] ?? '') para ahorrar líneas.
if (
    !isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ Token CSRF inválido");
}

// =========================
// 3. VALIDACIÓN DE DATOS
// =========================
if (
    empty($_POST['mes']) ||
    empty($_POST['anio']) ||
    empty($_POST['monto'])
) {
    die("❌ Datos incompletos");
}

// Validamos rangos y tipos de datos numéricos
if (
    !is_numeric($_POST['mes']) ||
    $_POST['mes'] < 1 || $_POST['mes'] > 12 ||
    !is_numeric($_POST['anio']) ||
    !is_numeric($_POST['monto']) ||
    $_POST['monto'] <= 0
) {
    die("❌ Datos inválidos");
}

// =========================
// 4. SANITIZACIÓN (CASTING)
// =========================
$usuario_id = $_SESSION['usuario_id'];
$mes        = (int) $_POST['mes'];
$anio       = (int) $_POST['anio'];
$monto      = (float) $_POST['monto'];

// =========================
// 5. LÓGICA "UPSERT" (Insert or Update)
// =========================
// Esta consulta es muy potente. Dice:
// "Intenta INSERTAR un nuevo presupuesto."
// "ON DUPLICATE KEY UPDATE...": Si la base de datos detecta que ya existe
// un registro para este usuario/mes/año (clave única duplicada), 
// ENTONCES ignora el insert y solo actualiza el monto.

$sql = "
INSERT INTO presupuestos (usuario_id, mes, anio, monto)
VALUES (:usuario_id, :mes, :anio, :monto)
ON DUPLICATE KEY UPDATE monto = :monto_update
";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    ":usuario_id"   => $usuario_id,
    ":mes"          => $mes,
    ":anio"         => $anio,
    ":monto"        => $monto,
    ":monto_update" => $monto // El nuevo valor si toca actualizar
]);

header("Location: ../index.php");
exit;
?>