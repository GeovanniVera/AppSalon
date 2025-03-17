<h1 class="nombre-pagina">Recupera tu contraseña</h1>

<form action="/" class="formulario" method="post" id="forgetForm">
    <div class="campo">
        <label for="email" class="form-label" id="label-email">Correo Electronico:</label>
        <div class="input-container">
            <i class="fas fa-envelope input-icon"></i>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                autocomplete="off"
                placeholder="ejemplo@gmail.com">
        </div>
        <span id="emailError" class="error-message"></span>
    </div>

    <div class="campo">
        <input
            type="submit"
            value="Recuperar Contraseña"
            class="submit">
    </div>
</form>

<div class="acciones">
    <a href="/">
        ¿ya tienes cuenta? inicia sesion.
    </a>
</div>

<script type="module" src="build/js/forgetValidator.js"></script>