<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar si el nombre de usuario ya existe
    $sql = "SELECT * FROM tbl_usuarios WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existingUser = mysqli_fetch_assoc($result);

    if ($existingUser) {
        echo "<div class='container'>
                <h1>El nombre de usuario ya está en uso.</h1>
                <img src='img/carga.gif' alt='Cargando...' />
              </div>";
        // Esperar 2 segundos antes de redirigir a login.php
        sleep(2);
        header("Location: login.php");
        exit();
    }

    // Encriptar la contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta para insertar el nuevo usuario
    $sql = "INSERT INTO tbl_usuarios (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Usuario registrado exitosamente
        echo "<div class='container'>
                <h1>Usuario registrado exitosamente.</h1>
                <img src='img/carga.gif' alt='Cargando...' />
              </div>";
        // Esperar 2 segundos antes de redirigir a login.php
        sleep(2);
        header("Location: login.php");
        exit();
    } else {
        echo "<div class='container'>
                <h1>Error al registrar el usuario.</h1>
              </div>";
    }
}

mysqli_close($conn);
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
        color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #1f1f1f;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        width: 80%;
        max-width: 400px;
        margin: 20px auto;
        color: #f4f4f4;
        text-align: center;
    }

    img {
        max-width: 100px;
        margin-top: 20px;
    }
</style>
