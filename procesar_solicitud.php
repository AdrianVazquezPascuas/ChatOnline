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
        $solicitudId = $_POST['solicitud_id'];
        $accion = $_POST['accion'];

        if ($accion === 'aceptar') {
            // Cambiar el estado de la solicitud a 'aceptada'
            $stmt = $pdo->prepare("UPDATE tbl_solicitudes_amistad SET estado = 'aceptada' WHERE id = :solicitud_id");
            $stmt->execute([':solicitud_id' => $solicitudId]);
            echo "<div class='container'>
                    <h1>Solicitud de amistad aceptada.</h1>
                  </div>";
        } elseif ($accion === 'rechazar') {
            // Cambiar el estado de la solicitud a 'rechazada'
            $stmt = $pdo->prepare("DELETE FROM tbl_solicitudes_amistad WHERE id = :solicitud_id");
            $stmt->execute([':solicitud_id' => $solicitudId]);
            echo "<div class='container'>
                    <h1>Solicitud de amistad rechazada.</h1>
                  </div>";
        }
    }
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
