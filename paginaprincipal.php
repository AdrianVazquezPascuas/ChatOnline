<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_SESSION['user_id'];

    // Manejo de búsqueda de usuarios
    if (isset($_POST['search'])) {
        $searchTerm = $_POST['search_term'];
        $stmt = $pdo->prepare("SELECT * FROM tbl_usuarios WHERE username LIKE :searchTerm");
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $users = [];
    }

    // Manejo de solicitudes de amistad
    if (isset($_POST['send_friend_request'])) {
        $friendId = $_POST['friend_id'];
        $stmt = $pdo->prepare("INSERT INTO tbl_solicitudes (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)");
        $stmt->execute([':sender_id' => $userId, ':receiver_id' => $friendId]);
    }

    // Obtener solicitudes de amistad recibidas
    $stmt = $pdo->prepare("SELECT * FROM tbl_solicitudes WHERE receiver_id = :receiver_id");
    $stmt->execute([':receiver_id' => $userId]);
    $friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Manejo de aceptación de solicitudes de amistad
    if (isset($_POST['accept_request'])) {
        $requestId = $_POST['request_id'];
        $stmt = $pdo->prepare("SELECT * FROM tbl_solicitudes WHERE id = :id");
        $stmt->execute([':id' => $requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Eliminar la solicitud de amistad
            $stmt = $pdo->prepare("DELETE FROM tbl_solicitudes WHERE id = :id");
            $stmt->execute([':id' => $requestId]);

            // Insertar en la tabla de amigos
            $stmt = $pdo->prepare("INSERT INTO tbl_amigos (user1_id, user2_id) VALUES (:user1_id, :user2_id)");
            $stmt->execute([
                ':user1_id' => $request['sender_id'],
                ':user2_id' => $request['receiver_id']
            ]);
        }
    }

    // Manejo de rechazo de solicitudes de amistad
    if (isset($_POST['reject_request'])) {
        $requestId = $_POST['request_id'];
        $stmt = $pdo->prepare("DELETE FROM tbl_solicitudes WHERE id = :id");
        $stmt->execute([':id' => $requestId]);
    }

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
            color: #f4f4f4;
        }

        .container {
            padding: 30px;
            background-color: #1f1f1f;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #343a40;
            color: #f4f4f4;
        }

        .button {
            padding: 10px 20px;
            background-color: #343a40;
            color: #f4f4f4;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a la Página Principal</h1>
        
        <!-- Formulario de búsqueda de usuarios -->
        <form method="POST">
            <div class="form-group">
                <input type="text" name="search_term" placeholder="Buscar usuarios..." required>
            </div>
            <button type="submit" name="search" class="button">Buscar</button>
        </form>

        <!-- Mostrar resultados de búsqueda -->
        <?php if (!empty($users)): ?>
            <h2>Resultados de búsqueda:</h2>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <?php echo htmlspecialchars($user['username']); ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="friend_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="send_friend_request" class="button">Enviar solicitud de amistad</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Notificaciones de solicitudes de amistad -->
        <?php if (!empty($friendRequests)): ?>
            <h2>Solicitudes de amistad:</h2>
            <ul>
                <?php foreach ($friendRequests as $request): ?>
                    <li>
                        Usuario <?php echo htmlspecialchars($request['sender_id']); ?> te ha enviado una solicitud de amistad.
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="accept_request" class="button">Aceptar</button>
                            <button type="submit" name="reject_request" class="button">Rechazar</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
