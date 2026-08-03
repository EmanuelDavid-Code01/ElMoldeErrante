<section id="contacto" class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2>Contacto</h2>

            <p>Solicitá tu presupuesto sin compromiso.</p>

        </div>

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <form action="./php/contacto.php" method="POST">

                    <div class="mb-3">

                        <label class="form-label">Nombre</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nombre"
                            placeholder="Ingrese su nombre">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Correo electrónico</label>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            placeholder="Ingrese su email">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Mensaje</label>

                        <textarea
                            class="form-control"
                            name="mensaje"
                            rows="5"
                            placeholder="Escriba su consulta"></textarea>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enviar consulta
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>