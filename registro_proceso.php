<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verificar si el nombre de usuario ya existe
        $stmt = $pdo->prepare("SELECT * FROM tbl_usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo "<div class='container'>
                    <h1>El nombre de usuario ya está en uso.</h1>
                    <img src='img/carga.gif' alt='Cargando...' />
                  </div>";
            return; // Detener el proceso si el nombre de usuario ya existe
        }

        // Encriptar la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Preparar la consulta
        $stmt = $pdo->prepare("INSERT INTO tbl_usuarios (username, password) VALUES (:username, :password)");

        // Ejecutar la consulta
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);

        // Mostrar mensaje de éxito y redirigir después de 2 segundos
        echo "<div class='container'>
                <h1>Usuario registrado exitosamente.</h1>
                <img src='img/carga.gif' alt='Cargando...' />
                <script>
                    setTimeout(function() {
                        window.location.href = 'login.php'; // Redirigir a login.php después de 2 segundos
                    }, 2000);
                </script>
              </div>";
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
    }
    img {
        max-width: 100px;
    }
</style>
