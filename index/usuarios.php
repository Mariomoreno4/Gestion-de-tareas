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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #42C7E8;
            font-family: Arial, sans-serif;
        }
        header, footer {
            background-color: #007bff;
            color: white;
        }
        .welcome-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .welcome-card .icon {
            font-size: 3rem;
            color: #007bff;
        }
        .welcome-card h2 {
            margin-top: 10px;
        }
        .table {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .table tbody tr {
            transition: background-color 0.3s;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-group .btn {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-group .btn:hover {
            transform: scale(1.05);
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .modal-header .btn-close {
            background-color: white;
        }
    </style>
</head>
<body>
    <header class="text-center py-3 mb-4">
        <div class="container">
            <h1>Usuarios Registrados</h1>
        </div>
    </header>
    <div class="container">
        <div class="welcome-card text-center">
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
        </div>
        
        <div class="text-end mb-3">
            <a href="/Proyecto/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <a href="./tarea.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <section class="mt-4">
            <h3>Usuarios Registrados</h3>
            <div class="table-responsive">
                <table class="table table-hover">
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
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-warning" onclick="editarUsuario('<?php echo $usuario['id_usuario']; ?>', '<?php echo $usuario['usuario']; ?>', '<?php echo $usuario['tipo']; ?>')"><i class="fas fa-edit"></i> Editar</button>
                                        <button class="btn btn-danger" onclick="eliminarUsuario('<?php echo $usuario['id_usuario']; ?>')"><i class="fas fa-trash"></i> Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <footer class="text-center py-3 mt-4">
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
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editarUsuario(id, nombre, tipo) {
            $('#id_usuario').val(id);
            $('#nombre_usuario').val(nombre);
            $('#tipo_usuario').val(tipo);
            $('#editarUsuarioModal').modal('show');
        }

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
