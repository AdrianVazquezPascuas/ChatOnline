<?php
session_start(); // Asegúrate de que la sesión está iniciada

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'db_usuarios';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idSolicitante = $_POST['id_solicitante'];
        $idReceptor = $_POST['id_receptor'];

        // Verificar si la solicitud ya existe
        $stmt = $pdo->prepare("SELECT * FROM tbl_solicitudes_amistad WHERE (id_solicitante = :id_solicitante AND id_receptor = :id_receptor) OR (id_solicitante = :id_receptor AND id_receptor = :id_solicitante)");
        $stmt->execute([':id_solicitante' => $idSolicitante, ':id_receptor' => $idReceptor]);
        $solicitudExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($solicitudExistente) {
            echo "<div class='container'>
                    <h1>La solicitud ya ha sido enviada o ya son amigos.</h1>
                  </div>";
        } else {
            // Insertar nueva solicitud
            $stmt = $pdo->prepare("INSERT INTO tbl_solicitudes_amistad (id_solicitante, id_receptor, estado) VALUES (:id_solicitante, :id_receptor, 'pendiente')");
            $stmt->execute([':id_solicitante' => $idSolicitante, ':id_receptor' => $idReceptor]);
            echo "<div class='container'>
                    <h1>Solicitud de amistad enviada exitosamente.</h1>
                  </div>";
        }
    }
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
