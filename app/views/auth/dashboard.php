<h1 class="nombre-pagina">Hola Bienvenido <?php echo $user->getName() ?></h1>
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

