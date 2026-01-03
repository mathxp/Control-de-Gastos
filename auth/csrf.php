<?php
/**
 * CSRF TOKEN HELPER
 * Este archivo contiene funciones para proteger los formularios contra ataques
 * Cross-Site Request Forgery (Falsificación de Petición en Sitios Cruzados).
 */

// 1. INICIO DE SESIÓN SEGURO
// Verificamos si la sesión ya está activa para no iniciarla dos veces.
// Es necesario porque el token se guarda en la variable $_SESSION.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera o devuelve el token CSRF actual.
 * Se usa dentro de los formularios HTML: <input type="hidden" name="csrf_token" ...>
 */
function csrf_token(): string
{
    // Si el usuario no tiene un token en su sesión, creamos uno nuevo.
    if (empty($_SESSION['csrf_token'])) {
        // random_bytes(32): Genera bytes criptográficamente seguros (imposibles de adivinar).
        // bin2hex(): Convierte esos bytes raros a letras y números legibles.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    // Devolvemos el token existente para ponerlo en el formulario.
    return $_SESSION['csrf_token'];
}

/**
 * Valida que el token enviado por el formulario coincida con el de la sesión.
 * @param string $token El token que llega por $_POST
 */
function csrf_validar(string $token): void
{
    if (
        empty($_SESSION['csrf_token']) || // ¿No hay token en el servidor?
        empty($token) ||                  // ¿No enviaron token en el form?
        !hash_equals($_SESSION['csrf_token'], $token) // ¿Son diferentes?
    ) {
        // hash_equals es vital: compara los strings de forma segura contra "Ataques de Tiempo".
        // Si usáramos '==', un hacker podría medir cuánto tarda la CPU en comparar
        // y adivinar el token letra por letra.
        
        http_response_code(403); // Código 403: Prohibido
        die("❌ Token CSRF inválido. La petición no es confiable."); // Detiene todo script.
    }
}
?>