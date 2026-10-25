
<?php

require_once __DIR__ . "/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$mensaje = trim($_POST["mensaje"] ?? "");

if (
    $nombre === "" ||
    $email === "" ||
    $mensaje === "" ||
    mb_strlen($nombre) > 100 ||
    mb_strlen($email) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header("Location: ../index.php?estado=error#contacto");
    exit;
}

$sql = "INSERT INTO solicitudes (nombre, email, mensaje)
        VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    error_log("Error al preparar la consulta de contacto.");
    header("Location: ../index.php?estado=error#contacto");
    exit;
}

$stmt->bind_param("sss", $nombre, $email, $mensaje);

if ($stmt->execute()) {
    $estado = "exito";
} else {
    error_log("Error al guardar una solicitud de contacto.");
    $estado = "error";
}

$stmt->close();
$conexion->close();

header("Location: ../index.php?estado=" . $estado . "#contacto");
exit;
