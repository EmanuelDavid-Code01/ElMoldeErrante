<?php

session_start();

// Verificar que el administrador haya iniciado sesión.
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../php/conexion.php";

$error = "";
$mensaje_exito = "";

// Procesar las acciones enviadas mediante POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ACCIÓN 1: Activar o desactivar un servicio.
    if (isset($_POST["cambiar_estado"])) {

        $id_servicio = filter_input(
            INPUT_POST,
            "id_servicio",
            FILTER_VALIDATE_INT
        );

        if (
            $id_servicio !== false &&
            $id_servicio !== null &&
            $id_servicio > 0
        ) {
            $sql_estado = "UPDATE servicios
                           SET activo = IF(activo = 1, 0, 1)
                           WHERE id = ?";

            $stmt_estado = $conexion->prepare($sql_estado);

            if ($stmt_estado) {
                $stmt_estado->bind_param("i", $id_servicio);

                if ($stmt_estado->execute()) {
                    if ($stmt_estado->affected_rows > 0) {
                        $stmt_estado->close();
                        $conexion->close();

                        header("Location: servicios.php?estado=actualizado");
                        exit;
                    } else {
                        $error = "No se encontró el servicio o su estado no cambió.";
                    }
                } else {
                    error_log("No se pudo actualizar el estado del servicio.");
                    $error = "No se pudo cambiar el estado del servicio.";
                }

                $stmt_estado->close();
            } else {
                error_log("No se pudo preparar la actualización del servicio.");
                $error = "Ocurrió un error al preparar la actualización.";
            }
        } else {
            $error = "El identificador del servicio no es válido.";
        }

    // ACCIÓN 2: Agregar un servicio.
    } else {

        $nombre = trim($_POST["nombre"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $icono = trim($_POST["icono"] ?? "");

        if (
            $nombre === "" ||
            $descripcion === "" ||
            $icono === "" ||
            mb_strlen($nombre) > 100 ||
            mb_strlen($descripcion) > 1000 ||
            mb_strlen($icono) > 100
        ) {
            $error = "Completá todos los campos respetando sus límites.";
        } else {
            $sql = "INSERT INTO servicios
                    (nombre, descripcion, icono, activo)
                    VALUES (?, ?, ?, 1)";

            $stmt = $conexion->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss", $nombre, $descripcion, $icono);

                if ($stmt->execute()) {
                    $stmt->close();
                    $conexion->close();

                    header("Location: servicios.php?estado=creado");
                    exit;
                } else {
                    error_log("No se pudo registrar el servicio.");
                    $error = "No se pudo guardar el servicio.";
                }

                $stmt->close();
            } else {
                error_log("No se pudo preparar el registro del servicio.");
                $error = "Ocurrió un error al preparar el registro.";
            }
        }
    }
}

// Mostrar mensajes de confirmación.
if (($_GET["estado"] ?? "") === "creado") {
    $mensaje_exito = "¡El servicio se agregó correctamente!";
} elseif (($_GET["estado"] ?? "") === "actualizado") {
    $mensaje_exito = "El estado del servicio se actualizó correctamente.";
} elseif (($_GET["estado"] ?? "") === "editado") {
    $mensaje_exito = "¡El servicio se editó correctamente!";
}

// Consultar todos los servicios.
$sql = "SELECT id, nombre, descripcion, icono, activo
        FROM servicios
        ORDER BY id DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    error_log("No se pudieron consultar los servicios.");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Administrar servicios | El Molde Errante</title>

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

        <a href="index.php" class="btn btn-outline-light btn-sm">
            Volver al panel
        </a>
    </div>
</nav>

<main class="container py-4">

    <h1 class="h2 mb-4">Administrar servicios</h1>

    <?php if ($mensaje_exito !== ""): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($mensaje_exito, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar servicios -->
    <section class="card shadow-sm mb-4">
        <div class="card-body">

            <h2 class="h4 mb-3">Agregar un servicio</h2>

            <form action="servicios.php" method="POST">

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
                        rows="3"
                        maxlength="1000"
                        required
                    ></textarea>
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
                        placeholder="Ejemplo: bi-printer"
                        required
                    >

                    <div class="form-text">
                        Escribí el nombre de la clase, por ejemplo bi-printer.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Guardar servicio
                </button>

            </form>
        </div>
    </section>

    <!-- Listado de servicios -->
    <section class="card shadow-sm">
        <div class="card-body">

            <h2 class="h4 mb-3">Servicios registrados</h2>

            <?php if (!$resultado): ?>

                <div class="alert alert-danger">
                    No se pudieron cargar los servicios.
                </div>

            <?php elseif ($resultado->num_rows === 0): ?>

                <div class="alert alert-info">
                    No hay servicios registrados.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Ícono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($servicio = $resultado->fetch_assoc()): ?>

                                <tr>
                                    <td>
                                        <?= (int) $servicio["id"] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $servicio["nombre"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $servicio["descripcion"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $servicio["icono"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php if ((int) $servicio["activo"] === 1): ?>
                                            <span class="badge text-bg-success">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-2">

                                            <!-- Editar servicio -->
                                            <a
                                                href="editar_servicio.php?id=<?= (int) $servicio["id"] ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Editar
                                            </a>

                                            <!-- Activar o desactivar -->
                                            <form
                                                action="servicios.php"
                                                method="POST"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id_servicio"
                                                    value="<?= (int) $servicio["id"] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    name="cambiar_estado"
                                                    value="1"
                                                    class="btn btn-sm <?= (int) $servicio["activo"] === 1 ? "btn-outline-danger" : "btn-outline-success" ?>"
                                                >
                                                    <?= (int) $servicio["activo"] === 1 ? "Desactivar" : "Activar" ?>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            <?php endwhile; ?>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </section>

    <div class="mt-4">
        <a href="../index.php" class="btn btn-secondary">
            Volver al sitio público
        </a>
    </div>

</main>

</body>
</html>

<?php

if ($resultado instanceof mysqli_result) {
    $resultado->free();
}

$conexion->close();

?>
