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

    // Verificar si el formulario de búsqueda ha sido enviado
    if (isset($_POST['search'])) {
        $searchTerm = $_POST['search_term'];

        // Buscar usuarios que coincidan con el término de búsqueda
        $stmt = $pdo->prepare("SELECT id, username FROM tbl_usuarios WHERE username LIKE :search AND id != :user_id");
        $stmt->execute([
            ':search' => '%' . $searchTerm . '%',
            ':user_id' => $userId
        ]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Consultar las solicitudes de amistad recibidas pendientes
    $stmt = $pdo->prepare("SELECT s.id, u.username FROM tbl_solicitudes s JOIN tbl_usuarios u ON s.sender_id = u.id WHERE s.receiver_id = :user_id AND s.status = 'pending'");
    $stmt->execute([':user_id' => $userId]);
    $friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consultar los amigos del usuario
    $stmt = $pdo->prepare("SELECT u.username FROM tbl_amigos a JOIN tbl_usuarios u ON a.friend_id = u.id WHERE a.user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar el número total de amigos
    $totalFriends = count($friends);

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
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Cambiado para alinear el contenido al inicio verticalmente */
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            color: #f4f4f4;
            position: relative; /* Para posicionar el enlace de regreso en relación a este contenedor */
        }

        h1, h2 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        input[type="text"] {
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #343a40;
            color: #f4f4f4;
            width: 300px;
            margin-right: 10px;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .search-results {
            margin-top: 20px;
        }

        .friend-requests, .friends-list {
            margin-top: 20px;
        }

        .no-results {
            text-align: center;
            color: #ccc;
        }

        .back-link {
            position: absolute; /* Permite colocar la imagen en una posición específica */
            top: 10px;
            left: 10px;
        }

        .back-link img {
            width: 40px; /* Ajusta el tamaño según necesites */
            height: auto;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-link">
            <a href="index.php"><img src="img/flecha.png" alt="Ir a la página principal"></a>
        </div>
        <h1>Bienvenido a tu página principal</h1>

        <!-- Buscador de amigos -->
        <form action="paginaprincipal.php" method="POST">
            <input type="text" name="search_term" placeholder="Buscar amigos por nombre" required>
            <button type="submit" name="search">Buscar</button>
        </form>

        <!-- Resultados de búsqueda -->
        <div class="search-results">
            <h2>Resultados de la búsqueda</h2>
            <?php if (isset($searchResults) && count($searchResults) > 0): ?>
                <?php foreach ($searchResults as $user): ?>
                    <div>
                        <?= htmlspecialchars($user['username']) ?>
                        <form action="enviar_solicitud.php" method="POST" style="display:inline;">
                            <input type="hidden" name="friend_id" value="<?= $user['id'] ?>">
                            <button type="submit">Enviar solicitud de amistad</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php elseif (isset($searchResults)): ?>
                <p class="no-results">No se encontraron usuarios.</p>
            <?php endif; ?>
        </div>

        <!-- Solicitudes de amistad -->
        <div class="friend-requests">
            <h2>Solicitudes de amistad</h2>
            <?php if (count($friendRequests) > 0): ?>
                <?php foreach ($friendRequests as $request): ?>
                    <div>
                        <?= htmlspecialchars($request['username']) ?> te ha enviado una solicitud.
                        <form action="gestionar_solicitud.php" method="POST" style="display:inline;">
                            <input type="hidden" name="friend_id" value="<?= $request['id'] ?>">
                            <button type="submit" name="action" value="accept">Aceptar</button>
                            <button type="submit" name="action" value="reject">Rechazar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tienes solicitudes pendientes.</p>
            <?php endif; ?>
        </div>

        <!-- Lista de amigos -->
        <div class="friends-list">
            <h2>Tus amigos (<?= $totalFriends ?>)</h2> <!-- Muestra el total de amigos -->
            <?php if ($totalFriends > 0): ?>
                <ul>
                    <?php foreach ($friends as $friend): ?>
                        <li><?= htmlspecialchars($friend['username']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aún no tienes amigos agregados.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
