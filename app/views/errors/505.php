<h1 class="nombre-pagina">Error 505</h1>
<?php if(isset($mensajes) && !empty($mensajes)):?>
    <?php foreach($mensajes as $mensaje):?>
        <p class="alerta error"><?php echo $mensaje ?></p>
    <?php endforeach;?>
<?php endif; ?>


