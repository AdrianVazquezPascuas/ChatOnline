<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
            color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            width: 80%;
            max-width: 600px;
            flex-direction: column;
        }

        h1 {
            margin-bottom: 20px;
            color: #f4f4f4;
        }

        .form-group {
            margin-bottom: 15px;
            width: 100%;
            display: flex; /* Alinear los elementos en fila */
            flex-direction: column; /* Alinear verticalmente */
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #343a40;
            color: #f4f4f4;
        }

        input:focus {
            outline: none;
            border-color: #007BFF; /* Color de borde al enfocar */
        }

        .button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px; /* Ajustar padding para que se vea igual que los inputs */
            font-size: 16px;
            color: #f4f4f4; /* Texto en claro */
            background-color: #343a40; /* Fondo de botón oscuro */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            width: 100%; /* Ajustar ancho del botón al 100% */
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Sombra inicial */
        }

        .button:hover {
            background-color: #495057; /* Color más claro al pasar el ratón */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6); /* Iluminación en hover */
            transform: translateY(-3px); /* Efecto de elevación */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Cuenta</h1>
        <form action="registro_process.php" method="POST">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Registrar</button>
            <button type="button" class="button" onclick="window.location.href='login.php'" style="margin-top: 10px;">Ya tengo cuenta</button>
        </form>
    </div>
</body>
</html>
