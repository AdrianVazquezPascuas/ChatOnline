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

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Obtener el ID del usuario actual
$usuarioId = $_SESSION['user_id'];

// Obtener el amigo con quien se está chateando
if (!isset($_GET['amigo'])) {
    echo "No se especificó un amigo para el chat.";
    exit();
}

$amigoUsername = $_GET['amigo'];

// Consultar el ID del amigo usando su nombre de usuario
$stmt = mysqli_prepare($conn, "SELECT id FROM tbl_usuarios WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $amigoUsername);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $amigoId);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Verificar si se encontró el amigo
if (!$amigoId) {
    echo "El amigo especificado no existe.";
    exit();
}

// Consultar los mensajes entre el usuario actual y el amigo
$stmt = mysqli_prepare($conn, "SELECT emisor, receptor, texto FROM mensajes WHERE (emisor = ? AND receptor = ?) OR (receptor = ? AND emisor = ?) ORDER BY fecha DESC");
mysqli_stmt_bind_param($stmt, "iiii", $usuarioId, $amigoId, $usuarioId, $amigoId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mensajes = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Insertar nuevo mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensajeTexto = $_POST['mensaje'];

    // Insertar el nuevo mensaje en la base de datos
    $stmt = mysqli_prepare($conn, "INSERT INTO mensajes (emisor, receptor, texto) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iis", $usuarioId, $amigoId, $mensajeTexto);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Recargar mensajes después de insertar
    $stmt = mysqli_prepare($conn, "SELECT emisor, receptor, texto FROM mensajes WHERE (emisor = ? AND receptor = ?) OR (receptor = ? AND emisor = ?) ORDER BY fecha DESC");
    mysqli_stmt_bind_param($stmt, "iiii", $usuarioId, $amigoId, $usuarioId, $amigoId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $mensajes = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?= htmlspecialchars($amigoUsername) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Quicksand", serif;
            background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
            color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            position: relative;
            height: 560px;
        }
        h1 {
            text-align: center;
        }
        .messages {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #444;
            padding: 10px;
            border-radius: 4px;
            background-color: #343a40;
            display: flex;
            flex-direction: column-reverse; /* Mostrar los mensajes nuevos arriba */
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            max-width: 70%;
        }
        .sender {
            background-color: #007BFF;
            color: white;
            text-align: right;
            align-self: flex-end; /* Alinear mensajes del emisor a la derecha */
        }
        .receiver {
            background-color: #6c757d;
            color: white;
            text-align: left;
            align-self: flex-start; /* Alinear mensajes del receptor a la izquierda */
        }
        .back-link {
            position: absolute;
            top: 10px;
            left: 10px;
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
        .input-group {
            display: flex; /* Hacer que el contenedor use flexbox */
            margin-top: 10px; /* Espaciado superior para el input */
        }
        form {
            flex: 1; /* Permitir que el formulario tome el espacio restante */
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #343a40;
            color: #f4f4f4;
            width: 100%; /* Asegurarse de que el input tome todo el ancho */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="back-link">
            <a href="paginaprincipal.php"><button>Volver a la página principal</button></a>
        </div>
        <h1>Chat con <?= htmlspecialchars($amigoUsername) ?></h1>
        <div class="messages" id="messages">
            <?php if (count($mensajes) > 0): ?>
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="message <?php echo $mensaje['emisor'] == $usuarioId ? 'sender' : 'receiver'; ?>">
                        <strong><?php echo $mensaje['emisor'] == $usuarioId ? 'Tú:' : htmlspecialchars($amigoUsername . ':'); ?></strong>
                        <p><?= htmlspecialchars($mensaje['texto']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay mensajes en esta conversación.</p>
            <?php endif; ?>
        </div>
        <div class="input-group">
            <form method="POST" id="chat-form" style="flex: 1;">
                <input type="text" name="mensaje" placeholder="Escribe un mensaje" required>
            </form>
            <button type="submit" form="chat-form">Enviar</button>
        </div>
    </div>
</body>
</html>
