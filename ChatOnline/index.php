<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
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
            max-width: 800px; /* Ampliar el máximo ancho del contenedor */
            flex-direction: row; /* Alinear en fila */
        }

        h1 {
            margin-bottom: 20px;
            color: #f4f4f4;
            flex: 1; /* Permitir que el título ocupe el espacio restante */
        }

        .logo {
            width: 250px; /* Ancho de la imagen */
            margin-right: 30px; /* Espaciado entre el logo y el formulario */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); /* Sombra de la imagen */
            transition: transform 0.3s; /* Transición al pasar el ratón */
        }

        .logo:hover {
            transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
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
            padding: 10px 20px;
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

        /* Estilo para pantallas más pequeñas */
        @media (max-width: 700px) {
            .container {
                flex-direction: column; /* Cambiar a columna en pantallas pequeñas */
                align-items: center; /* Centrar los elementos */
            }

            .logo {
                width: 120px; /* Ajustar tamaño de la imagen para pantallas más pequeñas */
                margin: 0 0 20px 0; /* Espacio abajo en lugar de a la derecha */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="./img/logo.jpg" alt="Logo" class="logo"> 
        <div>
            <h1>Bienvenido</h1>
            <div class="form-group">
                <a href="login.php" class="button">Iniciar Sesión</a>
                <a href="registro.php" class="button">Registrar</a>
            </div>
        </div>
    </div>
</body>
</html>
