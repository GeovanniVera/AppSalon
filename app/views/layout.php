<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Salón</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="build/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <div class="contenedor-app">
        <div class="imagen">
            <div class="contenedor-sombreado">
                <div class="sombreado"></div>
            </div>
        </div>
        <div class="app">
            <?php echo $contenido; ?>
        </div>
    </div>
    
</body>
</html>