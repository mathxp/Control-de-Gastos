<?php
/**
 * INSERTAR MOVIMIENTO (BACKEND)
 * Recibe los datos del formulario de creación y los guarda en la BD.
 */

session_start();

require_once "../auth/proteger.php";
require_once "../auth/csrf.php";
require_once "../conexion.php";

// =========================
// 1. RESTRICCIÓN DE MÉTODO
// =========================
// Este archivo NO debe ser visitado directamente por la URL.
// Solo debe recibir datos enviados por el formulario (POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// =========================
// 2. SEGURIDAD CSRF
// =========================
csrf_validar($_POST['csrf_token'] ?? '');

// =========================
// 3. VALIDACIONES DE INTEGRIDAD
// =========================
// Verificamos que no falte ningún campo obligatorio.
if (
    empty($_POST['tipo']) ||
    empty($_POST['monto']) ||
    empty($_POST['fecha']) ||
    empty($_POST['categoria_id'])
) {
    die("❌ Datos incompletos");
}

// WHITELISTING (Lista Blanca):
// Solo permitimos valores exactos: 'ingreso' o 'gasto'.
// El tercer parámetro 'true' indica comparación estricta (tipo y valor).
if (!in_array($_POST['tipo'], ['ingreso', 'gasto'], true)) {
    die("❌ Tipo inválido");
}

// Validación numérica
if (!is_numeric($_POST['monto']) || $_POST['monto'] <= 0) {
    die("❌ Monto inválido");
}

// =========================
// 4. SANITIZACIÓN Y CASTING (Conversión de tipos)
// =========================
// Convertimos explícitamente los datos a su tipo correcto.
// Esto evita sorpresas si alguien intenta enviar texto en lugar de números.
$tipo        = $_POST['tipo'];
$monto       = (float) $_POST['monto']; // Fuerza a ser número decimal
$fecha       = $_POST['fecha'];
$categoriaId = (int) $_POST['categoria_id']; // Fuerza a ser entero
$descripcion = trim($_POST['descripcion'] ?? '');
$usuario_id  = (int) $_SESSION['usuario_id'];

// =========================
// 5. INSERCIÓN EN BASE DE DATOS
// =========================
$sql = "
INSERT INTO movimientos
(tipo, monto, fecha, descripcion, categoria_id, usuario_id)
VALUES
(:tipo, :monto, :fecha, :descripcion, :categoria_id, :usuario_id)
";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    ":tipo"         => $tipo,
    ":monto"        => $monto,
    ":fecha"        => $fecha,
    ":descripcion"  => $descripcion,
    ":categoria_id" => $categoriaId,
    ":usuario_id"   => $usuario_id
]);

// Redirección final tras el éxito
header("Location: ../index.php");
exit;
?>