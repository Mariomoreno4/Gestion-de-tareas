<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script>
        function validarFormulario() {
            var contrasena1 = document.getElementById("contrasena").value;
            var contrasena2 = document.getElementById("confirmar_contrasena").value;

            if (contrasena1 !== contrasena2) {
                alert("Las contraseñas no coinciden. Por favor, inténtalo de nuevo.");
                return false;
            }

            if (contrasena1.length < 1 || contrasena1.length > 20) {
                alert("La contraseña debe tener entre 1 y 20 caracteres.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div>
        <form action="registro.php" method="POST" onsubmit="return validarFormulario()">
            <h1>Registro</h1><br>
            <label for="usuario">Usuario</label><br>
            <input type="text" name="usuario" id="usuario" required><br>
            <label for="contrasena">Contraseña</label><br>
            <input type="password" name="contrasena" id="contrasena" required><br>
            <label for="confirmar_contrasena">Confirma tu Contraseña</label><br>
            <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required><br>
            <button type="submit">Registrar</button>
            <label>¿Ya tienes cuenta?</label><br>
            <a href="sesion.php">Login</a>
        </form>
    </div>
</body>
</html>