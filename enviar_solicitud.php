<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$userId = $_SESSION['user_id'];
$friendId = $_POST['friend_id'];

// Comprobar si ya existe una solicitud pendiente entre estos dos usuarios
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_solicitudes WHERE 
                               (sender_id = ? AND receiver_id = ? AND status = 'pending') 
                               OR (sender_id = ? AND receiver_id = ? AND status = 'pending')");
mysqli_stmt_bind_param($stmt, "iiii", $userId, $friendId, $friendId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existingRequest = mysqli_fetch_assoc($result);

// Comprobar si ya son amigos
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_amigos WHERE 
                               (user_id = ? AND friend_id = ?) 
                               OR (user_id = ? AND friend_id = ?)");
mysqli_stmt_bind_param($stmt, "iiii", $userId, $friendId, $friendId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$areFriends = mysqli_fetch_assoc($result);

// Si no existe una solicitud pendiente ni son amigos, insertar nueva solicitud
if (!$existingRequest && !$areFriends) {
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_solicitudes (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $friendId);
    mysqli_stmt_execute($stmt);
}

mysqli_close($conn);

// Redirigir al usuario de vuelta a la página principal
header("Location: paginaprincipal.php");
exit();
?>
