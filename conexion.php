<?php
/**
 * SISTEMA DE CONTROL DE GASTOS - CONFIGURACIÓN DE LA BASE DE DATOS
 * Este archivo centraliza la conexión para ser reutilizado en todo el proyecto.
 */

// 1. Parámetros de configuración del servidor
$host     = "";      // Servidor de la BD (en WampServer es localhost)
$dbname   = ""; // El nombre exacto de tu base de datos en phpMyAdmin
$user     = "";           // Usuario por defecto de MySQL en WampServer
$password = "";               // Contraseña (por defecto está vacía en WampServer)

/**
 * 2. BLOQUE TRY-CATCH
 * El bloque 'try' intenta ejecutar el código. Si algo falla (como que la BD no exista),
 * el bloque 'catch' captura el error para que la aplicación no "explote" con un error feo.
 */
try {
    /**
     * 3. INSTANCIA DE PDO
     * Creamos el objeto de conexión. 
     * charset=utf8mb4: Fundamental para que se guarden correctamente tildes, eñes y emojis.
     */
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    /**
     * 4. CONFIGURACIÓN DE ATRIBUTOS
     * ATTR_ERRMODE: Le decimos a PDO que, si ocurre un error en el SQL,
     * lance una "Excepción" (ERRMODE_EXCEPTION). Esto facilita mucho encontrar errores de sintaxis.
     */
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    /**
     * 5. MANEJO DE ERRORES
     * die(): Detiene la ejecución del programa y muestra el mensaje del error.
     */
    die("Error de conexión: " . $e->getMessage());
}