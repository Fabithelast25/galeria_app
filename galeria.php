<?php
session_start();
include("db.php");

if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit();
}

$resultado = $conexion->query("SELECT * FROM fotografias");
$fotos = [];

while($row = $resultado->fetch_assoc()){
    $fotos[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/galeria_app/css/styles.css">

<title>Galería</title>
</head>

<body>

<div style="text-align:center; color:white; margin-top:20px;">
    <h2>Bienvenido <?php echo $_SESSION['usuario']; ?></h2>
    <a href="logout.php" style="color:yellow;">Cerrar sesión</a>
</div>

<div class="galeria">
    <button class="nav-btn" onclick="anterior()">⏮</button>

    <div id="foto"></div>

    <button class="nav-btn" onclick="siguiente()">⏭</button>
</div>

<script>
let fotos = <?php echo json_encode($fotos); ?>;
let index = 0;

function mostrar(){
    let f = fotos[index];

    document.getElementById("foto").innerHTML = `
    <div class="foto-card">
        <h3>${f.nombre}</h3>
        <div class="img-container">
            <img src="${f.ruta}">
        </div>
        <p>${f.descripcion}</p>
        <p>${index+1} / ${fotos.length}</p>
    </div>
`;
}

function siguiente(){
    index = (index + 1) % fotos.length;
    mostrar();
}

function anterior(){
    index = (index - 1 + fotos.length) % fotos.length;
    mostrar();
}

mostrar();
</script>

</body>
</html>