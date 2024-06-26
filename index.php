<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div>
        <form action="login.php" method="POST">
            <h1>Login</h1><br>
            <label for="usuario">Usuario</label><br>
            <input type="text" name="usuario" id="usuario" required><br>
            <label for="contrasena">Contraseña</label><br>
            <input type="password" name="contrasena" id="contrasena" required><br>
            <button type="submit">Entrar</button><br>
            <label>No tienes cuenta?</label><br>
            <a href="registar.php">Registrate</a>
        </form>
    </div>
</body>
</html>