<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
<style>
        body {
            background-color: #42C7E8; /* Corregido el color hexadecimal */
        }
      
    </style>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>Registro</h1>
                    </div>
                    <div class="card-body">
                        <form action="registro.php" method="POST" onsubmit="return validarFormulario()">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="contrasena" id="contrasena" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirma tu Contraseña</label>
                                <input type="password" class="form-control" name="confirmar_contrasena" id="confirmar_contrasena" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>¿Ya tienes cuenta?</small><br>
                        <a href="index.php" class="btn btn-link">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+fqswPeP5M5vyDoM26kM4jB85d4Ji" crossorigin="anonymous"></script>
</body>
</html>
