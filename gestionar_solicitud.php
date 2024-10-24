<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

// Crear conexión usando MySQLi
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Obtener el ID del usuario actual
$userId = $_SESSION['user_id'];

// Verificar si se ha enviado una acción y un ID de solicitud
if (isset($_POST['action']) && isset($_POST['friend_id'])) {
    $friendId = $_POST['friend_id'];

    if ($_POST['action'] === 'accept') {
        // Actualizar el estado de la solicitud
        $stmt = mysqli_prepare($conn, "UPDATE tbl_solicitudes SET status = 'accepted' WHERE sender_id = ? AND receiver_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $friendId, $userId);
        mysqli_stmt_execute($stmt);

        // Verificar si ya son amigos
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM tbl_amigos WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        mysqli_stmt_bind_param($stmt, "iiii", $userId, $friendId, $friendId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $areFriends);
        mysqli_stmt_fetch($stmt);

        // Solo insertar si no son amigos
        if ($areFriends == 0) {
            // Insertar ambos lados de la amistad en la tabla de amigos
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_amigos (user_id, friend_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $userId, $friendId);
            mysqli_stmt_execute($stmt);

            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_amigos (user_id, friend_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $friendId, $userId);
            mysqli_stmt_execute($stmt);
        }
    } elseif ($_POST['action'] === 'reject') {
        // Eliminar la solicitud de amistad
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_solicitudes WHERE sender_id = ? AND receiver_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $friendId, $userId);
        mysqli_stmt_execute($stmt);
    }
}

// Redirigir al usuario a la página principal
header("Location: paginaprincipal.php");
exit();

// Cerrar la conexión
mysqli_close($conn);
?>
