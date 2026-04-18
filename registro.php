<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Activar excepciones MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("db.php");

$mensaje = "";
$tipo = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    try {

        $username = trim($_POST['username']);
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $password_raw = $_POST['password'];

        // VALIDACIONES BÁSICAS
        if(
            empty($username) || strlen($username) > 50 ||
            empty($nombre) || strlen($nombre) > 50 ||
            empty($apellido) || strlen($apellido) > 50 ||
            empty($email) || strlen($email) > 100 ||
            empty($password_raw) || strlen($password_raw) > 50
        ){
            throw new Exception("Datos inválidos");
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception("Email inválido");
        }
        if(!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $nombre)){
            throw new Exception("El nombre solo puede contener letras");
        }

        if(!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/", $apellido)){
            throw new Exception("El apellido solo puede contener letras");
        }
        if(!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password_raw)){
            throw new Exception("La contraseña debe tener mínimo 8 caracteres, incluir una mayúscula, un número y un carácter especial.");
        }
        // ENCRIPTAR PASSWORD
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // INSERTAR DIRECTO (SIN VALIDAR ANTES)
        $stmt = $conexion->prepare("INSERT INTO usuarios (username,nombre,apellido,email,password) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$username,$nombre,$apellido,$email,$password);
        $stmt->execute();

        $mensaje = "Usuario registrado correctamente";
        $tipo = "success";

    } catch (mysqli_sql_exception $e) {

        // ERROR DUPLICADO (username o email)
        if ($e->getCode() == 1062) {
            $mensaje = "El nombre de usuario o correo ya existe";
            $tipo = "error";
        } else {
            $mensaje = "Error en el servidor";
            $tipo = "error";
        }

    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/galeria_app/css/styles.css">
<title>Registro</title>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="container">
    <h2>Registro</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Usuario" required maxlength="50">
        <input type="text" name="nombre" placeholder="Nombre" onkeypress="soloLetras(event)" required maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+">
        <input type="text" name="apellido" placeholder="Apellido" onkeypress="soloLetras(event)" required maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+">
        <input type="email" name="email" placeholder="Email" required maxlength="100">

        <div style="position: relative;">
            <input 
                type="password" 
                id="password_reg" 
                name="password" 
                placeholder="Contraseña" 
                required 
                maxlength="50"
                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}"
                title="Mínimo 8 caracteres, una mayúscula, un número y un carácter especial">
            <span onclick="togglePassword('password_reg', this)" 
                style="position:absolute; right:20px; top:15px; cursor:pointer;">
                👁️
            </span>
        </div>

        <button>Registrarse</button>
    </form>

    <a href="index.php" class="link">Volver</a>
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

<?php if($mensaje != ""): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {

    Swal.fire({
        icon: '<?php echo $tipo; ?>',
        title: '<?php echo $tipo == "success" ? "Éxito" : "Error"; ?>',
        text: '<?php echo $mensaje; ?>',
        confirmButtonText: 'OK'
    }).then(() => {

        <?php if($tipo == "success"): ?>
            window.location = "index.php";
        <?php else: ?>
            window.location = "registro.php";
        <?php endif; ?>

    });

});
</script>
<?php endif; ?>
<script>
function soloLetras(e){
    let char = String.fromCharCode(e.which);
    let regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/;
    if(!regex.test(char)){
        e.preventDefault();
    }
}
</script>
</body>
</html>