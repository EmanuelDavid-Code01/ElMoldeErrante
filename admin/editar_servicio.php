
<?php

session_start();

// Verificar que el administrador haya iniciado sesión.
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

$error = "";

// Obtener el ID del servicio desde la URL.
$id_servicio = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

// Si se envió el formulario, usar el ID recibido por POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_servicio = filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $icono = trim($_POST["icono"] ?? "");

    if (
        $id_servicio === false ||
        $id_servicio === null ||
        $id_servicio <= 0
    ) {
        $error = "El identificador del servicio no es válido.";
    } elseif (
        $nombre === "" ||
        $descripcion === "" ||
        $icono === "" ||
        mb_strlen($nombre) > 100 ||
        mb_strlen($descripcion) > 1000 ||
        mb_strlen($icono) > 100
    ) {
        $error = "Completá todos los campos respetando sus límites.";
    } else {
        $sql = "UPDATE servicios
                SET nombre = ?, descripcion = ?, icono = ?
                WHERE id = ?";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                "sssi",
                $nombre,
                $descripcion,
                $icono,
                $id_servicio
            );

            if ($stmt->execute()) {
                $stmt->close();
                $conexion->close();

                header("Location: servicios.php?estado=editado");
                exit;
            }

            error_log("No se pudo editar el servicio.");
            $error = "No se pudieron guardar los cambios.";

            $stmt->close();
        } else {
            error_log("No se pudo preparar la edición del servicio.");
            $error = "Ocurrió un error al preparar la actualización.";
        }
    }
}

// Buscar los datos actuales del servicio.
if (
    $id_servicio === false ||
    $id_servicio === null ||
    $id_servicio <= 0
) {
    $conexion->close();
    http_response_code(400);
    exit("El identificador del servicio no es válido.");
}

$sql = "SELECT id, nombre, descripcion, icono
        FROM servicios
        WHERE id = ?
        LIMIT 1";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    $conexion->close();
    http_response_code(500);
    exit("No se pudo consultar el servicio.");
}

$stmt->bind_param("i", $id_servicio);
$stmt->execute();

$resultado = $stmt->get_result();
$servicio = $resultado->fetch_assoc();

$stmt->close();

if (!$servicio) {
    $conexion->close();
    http_response_code(404);
    exit("No se encontró el servicio solicitado.");
}

// Si el formulario tuvo errores, conservar los valores ingresados.
if ($_SERVER["REQUEST_METHOD"] === "POST" && $error !== "") {
    $servicio["nombre"] = $nombre;
    $servicio["descripcion"] = $descripcion;
    $servicio["icono"] = $icono;
}

$conexion->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar servicio | El Molde Errante</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">
            El Molde Errante | Administración
        </span>

        <a href="servicios.php" class="btn btn-outline-light btn-sm">
            Volver a servicios
        </a>
    </div>
</nav>

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <section class="card shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h3 mb-4">Editar servicio</h1>

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars(
                                $error,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </div>
                    <?php endif; ?>

                    <form action="editar_servicio.php" method="POST">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $servicio["id"] ?>"
                        >

                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                maxlength="100"
                                value="<?= htmlspecialchars(
                                    $servicio["nombre"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                Descripción
                            </label>

                            <textarea
                                class="form-control"
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                                maxlength="1000"
                                required
                            ><?= htmlspecialchars(
                                $servicio["descripcion"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="icono" class="form-label">
                                Clase del ícono de Bootstrap Icons
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="icono"
                                name="icono"
                                maxlength="100"
                                value="<?= htmlspecialchars(
                                    $servicio["icono"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>"
                                required
                            >

                            <div class="form-text">
                                Por ejemplo: bi-printer o bi-box.
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Guardar cambios
                            </button>

                            <a
                                href="servicios.php"
                                class="btn btn-secondary"
                            >
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>
            </section>

        </div>
    </div>
</main>

</body>
</html>
