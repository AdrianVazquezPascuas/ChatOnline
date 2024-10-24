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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el ID del usuario actual
    $userId = $_SESSION['user_id'];

    // Verificar si se ha enviado una acción y un ID de solicitud
    if (isset($_POST['action']) && isset($_POST['friend_id'])) {
        $friendId = $_POST['friend_id'];

        if ($_POST['action'] === 'accept') {
            // Actualizar el estado de la solicitud
            $stmt = $pdo->prepare("UPDATE tbl_solicitudes SET status = 'accepted' WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
            $stmt->execute([':sender_id' => $friendId, ':receiver_id' => $userId]);

            // Verificar si ya son amigos
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_amigos WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)");
            $stmt->execute([':user_id' => $userId, ':friend_id' => $friendId]);
            $areFriends = $stmt->fetchColumn();

            // Solo insertar si no son amigos
            if ($areFriends == 0) {
                // Insertar ambos lados de la amistad en la tabla de amigos
                $stmt = $pdo->prepare("INSERT INTO tbl_amigos (user_id, friend_id) VALUES (:user_id, :friend_id)");
                $stmt->execute([':user_id' => $userId, ':friend_id' => $friendId]);

                $stmt = $pdo->prepare("INSERT INTO tbl_amigos (user_id, friend_id) VALUES (:friend_id, :user_id)");
                $stmt->execute([':user_id' => $userId, ':friend_id' => $friendId]);
            }
        } elseif ($_POST['action'] === 'reject') {
            // Eliminar la solicitud de amistad
            $stmt = $pdo->prepare("DELETE FROM tbl_solicitudes WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
            $stmt->execute([':sender_id' => $friendId, ':receiver_id' => $userId]);
        }
    }

    header("Location: paginaprincipal.php");
    exit();

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
