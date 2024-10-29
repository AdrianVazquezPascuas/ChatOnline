<?php
session_start();

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
$action = $_POST['action'];

// Aceptar o rechazar la solicitud de amistad
if ($action === 'accept') {
    // Verificar que la amistad no exista
    $checkStmt = mysqli_prepare($conn, "
        SELECT * FROM tbl_amigos WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)
    ");
    mysqli_stmt_bind_param($checkStmt, "iiii", $userId, $friendId, $friendId, $userId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) === 0) {
        // Insertar la amistad en ambas direcciones
        $insertStmt1 = mysqli_prepare($conn, "INSERT INTO tbl_amigos (user_id, friend_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($insertStmt1, "ii", $userId, $friendId);
        mysqli_stmt_execute($insertStmt1);

        $insertStmt2 = mysqli_prepare($conn, "INSERT INTO tbl_amigos (user_id, friend_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($insertStmt2, "ii", $friendId, $userId);
        mysqli_stmt_execute($insertStmt2);

        // Actualizar el estado de la solicitud
        $updateStmt = mysqli_prepare($conn, "UPDATE tbl_solicitudes SET status = 'accepted' WHERE id = ?");
        mysqli_stmt_bind_param($updateStmt, "i", $_POST['request_id']);
        mysqli_stmt_execute($updateStmt);
    }
} elseif ($action === 'reject') {
    // Rechazar la solicitud y actualizar el estado a 'rejected'
    $updateStmt = mysqli_prepare($conn, "UPDATE tbl_solicitudes SET status = 'rejected' WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, "i", $_POST['request_id']);
    mysqli_stmt_execute($updateStmt);
}

mysqli_close($conn);
header("Location: paginaprincipal.php");
exit();
?>
