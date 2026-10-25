

<section id="contacto" class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2>Contacto</h2>

            <p>Solicitá tu presupuesto sin compromiso.</p>

        </div>

        <?php if (isset($_GET["estado"]) && $_GET["estado"] === "exito"): ?>

            <div class="alert alert-success" role="alert">
                ¡Tu consulta fue enviada correctamente! Nos pondremos en contacto con vos.
            </div>

        <?php elseif (isset($_GET["estado"]) && $_GET["estado"] === "error"): ?>

            <div class="alert alert-danger" role="alert">
                No pudimos enviar tu consulta. Revisá los datos e intentá nuevamente.
            </div>

        <?php endif; ?>

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <form action="./php/contacto.php" method="POST">

                    <div class="mb-3">

                        <label for="nombre" class="form-label">Nombre</label>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            name="nombre"
                            placeholder="Ingrese su nombre"
                            maxlength="100"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="email" class="form-label">Correo electrónico</label>

                        <input
                            type="email"
                            id="email"
                            class="form-control"
                            name="email"
                            placeholder="Ingrese su email"
                            maxlength="150"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label for="mensaje" class="form-label">Mensaje</label>

                        <textarea
                            id="mensaje"
                            class="form-control"
                            name="mensaje"
                            rows="5"
                            placeholder="Escriba su consulta"
                            required
                        ></textarea>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enviar consulta
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>
