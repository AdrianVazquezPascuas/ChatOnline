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

// Conectar a la base de datos
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$userId = $_SESSION['user_id'];
$friendId = $_POST['friend_id'];

// Comprobar si ya existe una solicitud entre estos dos usuarios
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_solicitudes WHERE sender_id = ? AND receiver_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $userId, $friendId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existingRequest = mysqli_fetch_assoc($result);

if (!$existingRequest) {
    // Insertar nueva solicitud de amistad
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_solicitudes (sender_id, receiver_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $friendId);
    mysqli_stmt_execute($stmt);
}

// Redirigir al usuario a la página principal
header("Location: paginaprincipal.php");
exit();

mysqli_close($conn);
?>
