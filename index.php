<?php 
session_start();
include("db.php");

$alert = "";

// PROCESAR LOGIN
if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // VALIDACIONES
    if(empty($username) || strlen($username) > 50 || empty($password) || strlen($password) > 50){
        $alert = "Swal.fire('Error','Datos inválidos','error')";
    } else {

        $stmt = $conexion->prepare("SELECT password FROM usuarios WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $stmt->bind_result($hash);
            $stmt->fetch();

            if(password_verify($password, $hash)){
                $_SESSION['usuario'] = $username;
                header("Location: galeria.php");
                exit();
            } else {
                $alert = "Swal.fire('Error','Usuario o contraseña incorrectos','error')";
            }

        } else {
            $alert = "Swal.fire('Error','Usuario o contraseña incorrectos','error')";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="/galeria_app/css/styles.css">
<title>Login</title>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<div class="container">
    <h2>Iniciar Sesión</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Usuario" required maxlength="50">

        <div style="position: relative;">
            <input type="password" id="password" name="password" placeholder="Contraseña" required maxlength="50">
            <span onclick="togglePassword('password', this)" 
                style="position:absolute; right:20px; top:15px; cursor:pointer;">
                👁️
            </span>
        </div>

        <button name="login">Entrar</button>
    </form>

    <a href="registro.php" class="link">Crear cuenta</a>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈";
    } else {
        input.type = "password";
        icon.textContent = "👁️";
    }
}
</script>

<!-- MOSTRAR ALERTA -->
<?php if(!empty($alert)): ?>
<script>
    <?php echo $alert; ?>
</script>
<?php endif; ?>

</body>
</html>