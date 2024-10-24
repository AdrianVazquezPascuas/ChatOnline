<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_SESSION['user_id'];
    $friendId = $_POST['friend_id'];

    // Comprobar si ya existe una solicitud entre estos dos usuarios
    $stmt = $pdo->prepare("SELECT * FROM tbl_solicitudes WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
    $stmt->execute([':sender_id' => $userId, ':receiver_id' => $friendId]);
    $existingRequest = $stmt->fetch();

    if (!$existingRequest) {
        // Insertar nueva solicitud de amistad
        $stmt = $pdo->prepare("INSERT INTO tbl_solicitudes (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)");
        $stmt->execute([':sender_id' => $userId, ':receiver_id' => $friendId]);
    }

    // Redirigir al usuario a la página principal
    header("Location: paginaprincipal.php");
    exit();

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
