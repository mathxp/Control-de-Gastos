<?php
/**
 * REGISTRO DE USUARIO (BACKEND)
 * Recibe el formulario, valida reglas de negocio y guarda el usuario.
 */

require_once "../conexion.php";

// =========================
// 1. CAPTURA Y LIMPIEZA
// =========================
// trim(): Elimina espacios en blanco al inicio y al final accidentalmente puestos por el usuario.
// El operador ?? '' evita errores si el campo no fue enviado.
$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// =========================
// 2. VALIDACIONES BÁSICAS
// =========================
if ($nombre === '' || $email === '' || $password === '') {
    die("❌ Todos los campos son obligatorios"); // Detiene el script si falta algo.
}

// filter_var: Valida que el texto tenga formato real de email (algo@dominio.com).
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Email inválido");
}

// =========================
// 3. VALIDACIÓN FUERZA CONTRASEÑA (REGEX)
// =========================
// Esta es una Expresión Regular para obligar a usar contraseñas fuertes.
// Desglose:
// ^          -> Inicio de la cadena
// (?=.*[a-z]) -> Debe contener al menos una minúscula
// (?=.*[A-Z]) -> Debe contener al menos una mayúscula
// (?=.*\d)    -> Debe contener al menos un número
// (?=.*[\W_]) -> Debe contener al menos un símbolo (ej: ! @ # $)
// .{8,}      -> Longitud mínima de 8 caracteres
// $          -> Fin de la cadena
$regexPassword = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

if (!preg_match($regexPassword, $password)) {
    die(
        "❌ La contraseña debe tener mínimo 8 caracteres, 
        una mayúscula, una minúscula, un número y un símbolo"
    );
}

// =========================
// 4. EVITAR DUPLICADOS