<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

session_start(); // Iniciar la sesión

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Preparar la consulta para buscar el usuario
        $stmt = $pdo->prepare("SELECT * FROM tbl_usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Establecer sesión y redirigir a la página principal
            $_SESSION['user_id'] = $user['id'];
            header("Location: paginaprincipal.php"); // Redirigir a la página principal
            exit(); // Asegurarse de que el script no siga ejecutándose
        } else {
            echo "<div class='container'>
                    <h1>Nombre de usuario o contraseña incorrectos.</h1>
                    <img src='img/carga.gif' alt='Cargando...' />
                  </div>";

            // Redirigir a login.php después de 2 segundos
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                  </script>";
        }
    }
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
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
