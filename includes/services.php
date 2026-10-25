
<?php
require_once __DIR__ . "/../php/conexion.php";

$sql = "SELECT id, nombre, descripcion, icono
        FROM servicios
        WHERE activo = 1
        ORDER BY id ASC";

$resultado = $conexion->query($sql);
?>

<section id="servicios" class="py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2>Nuestros Servicios</h2>
            <p>Soluciones adaptadas a cada necesidad.</p>
        </div>

        <div class="row g-4">

            <?php if ($resultado && $resultado->num_rows > 0): ?>

                <?php while ($servicio = $resultado->fetch_assoc()): ?>

                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">

                                <i class="bi <?= htmlspecialchars($servicio['icono'], ENT_QUOTES, 'UTF-8') ?> fs-1"></i>

                                <h4 class="mt-3">
                                    <?= htmlspecialchars($servicio['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                </h4>

                                <p>
                                    <?= htmlspecialchars($servicio['descripcion'], ENT_QUOTES, 'UTF-8') ?>
                                </p>

                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12 text-center">
                    <p>En este momento no hay servicios disponibles.</p>
                </div>

            <?php endif; ?>

        </div>
    </div>
</section>

<?php
if ($resultado instanceof mysqli_result) {
    $resultado->free();
}
?>
