<h1 class="nombre-pagina">Inicia Sesion en App Salon</h1>
<?php if(isset($errores) && !empty($errores)) :?>
    <?php foreach($errores as $error):?>
        <p class="alerta error"><?php echo $error ?></p>
    <?php endforeach;?>
<?php endif; ?>

<?php if(isset($exitos) && !empty($exitos)) :?>
    <?php foreach($exitos as $exito):?>
        <p class="alerta exito"><?php echo $exito ?></p>
    <?php endforeach;?>
<?php endif; ?>

<form action="/login" class="formulario" method="post" id="loginForm">
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
        <label for="password" class="form-label" id="label-password">Contraseña:</label>
        <div class="input-container">
            <i class="fas fa-lock input-icon"></i>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="form-control" 
                autocomplete="off" 
                placeholder="Contraseña" 
                disabled>
        </div>
        <span id="passwordError" class="error-message"></span>


    </div>
    <div class="campo">
        <input 
            type="submit" 
            value="Inicia sesión" 
            class="submit">
    </div>
</form>

<div class="acciones">
    <a href="/register">
        ¿no tienes cuenta? registrate
    </a>
    <a href="/forgetPassword">
        ¿Olvidaste tu contraseña?
    </a>
</div>

<script type="module" src="build/js/loginValidator.js"></script>  