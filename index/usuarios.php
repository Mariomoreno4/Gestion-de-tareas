<?php
require_once 'conexion.php'; 

session_start(); // Iniciar la sesión

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener todos los usuarios
$query_usuarios = "SELECT id_usuario, usuario, tipo FROM logins";
$resultado_usuarios = mysqli_query($conn, $query_usuarios);

if (!$resultado_usuarios) {
    die("Error en la consulta de usuarios: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</head>
<body>
    <header class="bg-primary text-white text-center py-3 mb-4">
        <div class="container">
            <h1>Usuarios Registrados</h1>
        </div>
    </header>
    <div class="container">
        <h2 class="text-center">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
        <div class="text-end">
            <a href="/Proyecto/logout.php" class="btn btn-danger">Cerrar sesión</a>
            <a href="/Proyecto/index/tarea.php" class="btn btn-primary">Volver</a>
        </div>
        <section class="mt-4">
            <h3>Usuarios Registrados</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($usuario['usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($usuario['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <button class="btn btn-warning" onclick="editarUsuario('<?php echo $usuario['id_usuario']; ?>', '<?php echo $usuario['usuario']; ?>', '<?php echo $usuario['tipo']; ?>')">Editar</button>
                                <button class="btn btn-danger" onclick="eliminarUsuario('<?php echo $usuario['id_usuario']; ?>')">Eliminar</button>
                                
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>
    <footer class="bg-primary text-white text-center py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarUsuarioForm">
                        <input type="hidden" id="id_usuario">
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                            <select class="form-control" id="tipo_usuario" required>
                                <option value="1">Administrador</option>
                                <option value="0">Usuario</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editarUsuario(id, nombre, tipo) {
            console.log("Abrir modal para editar usuario: ", id, nombre, tipo); // Para depuración
            $('#id_usuario').val(id);
            $('#nombre_usuario').val(nombre);
            $('#tipo_usuario').val(tipo);
            $('#editarUsuarioModal').modal('show');
        }
        $('#editarUsuarioModal').modal('show');

        function eliminarUsuario(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                $.post('eliminar_usuario.php', { id_usuario: id }, function(response) {
                    if (response === 'success') {
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        console.log(response); // Mostrar el error en la consola
                        alert('Error al eliminar el usuario: ' + response);
                    }
                });
            }
        }

        $('#editarUsuarioForm').submit(function(e) {
            e.preventDefault();
            var id = $('#id_usuario').val();
            var nombre = $('#nombre_usuario').val();
            var tipo = $('#tipo_usuario').val();
            
            $.post('editar_usuario.php', { id_usuario: id, usuario: nombre, tipo: tipo }, function(response) {
                if (response === 'success') {
                    location.reload(); // Recargar la página para ver los cambios
                } else {
                    console.log(response); // Mostrar el error en la consola
                    alert('Error al actualizar el usuario: ' + response);
                }
            });
        });
    </script>
</body>
</html>
