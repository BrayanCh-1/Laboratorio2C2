<?php
date_default_timezone_set('America/El_Salvador');

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $habito = trim($_POST['habito']);
    $fecha = $_POST['fecha'];
    $completado = $_POST['completado'];
    $observaciones = trim($_POST['observaciones']);
    
    $errores = [];
    
    if (empty($habito)) {
        $errores[] = "El hábito es obligatorio";
    }
    if (empty($fecha)) {
        $errores[] = "La fecha es obligatoria";
    }
    if ($fecha > date('Y-m-d')) {
        $errores[] = "La fecha no puede ser futura";
    }
    if ($completado != 'si' && $completado != 'no') {
        $errores[] = "Estado inválido";
    }
    
    if (empty($errores)) {
        $sql = "INSERT INTO habitos (usuario_id, habito, fecha, completado, observaciones) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issss", $_SESSION['usuario_id'], $habito, $fecha, $completado, $observaciones);
        $stmt->execute();
        header("Location: dashboard.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $habito = trim($_POST['habito']);
    $fecha = $_POST['fecha'];
    $completado = $_POST['completado'];
    $observaciones = trim($_POST['observaciones']);
    
    $errores_editar = [];
    
    if (empty($habito)) {
        $errores_editar[] = "El hábito es obligatorio";
    }
    if (empty($fecha)) {
        $errores_editar[] = "La fecha es obligatoria";
    }
    if ($fecha > date('Y-m-d')) {
        $errores_editar[] = "La fecha no puede ser futura";
    }
    
    if (empty($errores_editar)) {
        $sql = "UPDATE habitos SET habito = ?, fecha = ?, completado = ?, observaciones = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssii", $habito, $fecha, $completado, $observaciones, $id, $_SESSION['usuario_id']);
        $stmt->execute();
        header("Location: dashboard.php");
        exit();
    }
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM habitos WHERE id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION['usuario_id']);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

$sql = "SELECT * FROM habitos WHERE usuario_id = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$resultado = $stmt->get_result();
$habitos = $resultado->fetch_all(MYSQLI_ASSOC);

$editando = null;
if (isset($_GET['editar_id'])) {
    $id_editar = $_GET['editar_id'];
    $sql = "SELECT * FROM habitos WHERE id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id_editar, $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $editando = $resultado->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Hábitos</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
            <a href="logout.php" class="logout">Cerrar Sesión</a>
        </div>
        
        <div class="form-container">
            <?php if ($editando): ?>
                <h2>Editar Hábito</h2>
                <?php if (!empty($errores_editar)): ?>
                    <?php foreach($errores_editar as $error): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $editando['id']; ?>">
                    <input type="text" name="habito" value="<?php echo htmlspecialchars($editando['habito']); ?>" required>
                    <input type="date" name="fecha" value="<?php echo $editando['fecha']; ?>" required>
                    <select name="completado" required>
                        <option value="no" <?php echo $editando['completado'] == 'no' ? 'selected' : ''; ?>>No completado</option>
                        <option value="si" <?php echo $editando['completado'] == 'si' ? 'selected' : ''; ?>>Completado</option>
                    </select>
                    <textarea name="observaciones" rows="2"><?php echo htmlspecialchars($editando['observaciones'] ?? ''); ?></textarea>
                    <button type="submit" name="editar">Actualizar Hábito</button>
                    <a href="dashboard.php" class="btn-cancelar">Cancelar</a>
                </form>
            <?php else: ?>
                <h2>Agregar Hábito</h2>
                <?php if (!empty($errores)): ?>
                    <?php foreach($errores as $error): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form method="POST">
                    <input type="text" name="habito" placeholder="Hábito (ej: Hacer ejercicio)" required>
                    <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                    <select name="completado" required>
                        <option value="no">No completado</option>
                        <option value="si">Completado</option>
                    </select>
                    <textarea name="observaciones" placeholder="Observaciones (opcional)" rows="2"></textarea>
                    <button type="submit" name="agregar">Agregar Hábito</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="table-container">
            <h2>Mis Hábitos</h2>
            <?php if (count($habitos) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Hábito</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($habitos as $habito): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($habito['habito']); ?></td>
                                <td><?php echo $habito['fecha']; ?></td>
                                <td class="<?php echo $habito['completado'] == 'si' ? 'completado' : 'pendiente'; ?>">
                                    <?php echo $habito['completado'] == 'si' ? 'Completado' : 'Pendiente'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($habito['observaciones'] ?? ''); ?></td>
                                <td class="acciones">
                                    <a href="dashboard.php?editar_id=<?php echo $habito['id']; ?>" class="btn-editar">✏️ Editar</a>
                                    <a href="dashboard.php?eliminar=<?php echo $habito['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este hábito?')">🗑️ Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="sin-datos">Aún no tienes hábitos registrados. ¡Agrega uno!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>