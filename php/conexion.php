
<?php

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "el_molde_errante";

$conexion = new mysqli(
    $host,
    $usuario,
    $password,
    $base_datos
);

if ($conexion->connect_error) {
    error_log("Error de conexión MySQL: " . $conexion->connect_error);
    exit("No se pudo conectar con la base de datos.");
}

$conexion->set_charset("utf8mb4");
