<?php

require_once 'config/database.php';

$pdo = getDBConnection();

$error = '';

# CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $carrera = trim($_POST['carrera']);

    if(strlen($nombre) < 3 || strlen($nombre) > 100){
        $error = "El nombre debe tener entre 3 y 100 caracteres";
    }

    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Correo inválido";
    }

    else{

        $stmt = $pdo->prepare("
            INSERT INTO estudiantes(nombre,email,carrera)
            VALUES(:n,:e,:c)
        ");

        $stmt->execute([
            ':n' => htmlspecialchars($nombre),
            ':e' => $email,
            ':c' => htmlspecialchars($carrera)
        ]);

        header("Location: /");
        exit;
    }
}

# DELETE
if(isset($_GET['delete'])){

    $stmt = $pdo->prepare("
        DELETE FROM estudiantes
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => (int)$_GET['delete']
    ]);

    header("Location: /");
    exit;
}

# READ
# PAGINACIÓN

$porPagina = 5;

$paginaActual = isset($_GET['pagina'])
    ? (int)$_GET['pagina']
    : 1;

if($paginaActual < 1){
    $paginaActual = 1;
}

$offset = ($paginaActual - 1) * $porPagina;

# TOTAL REGISTROS

$totalStmt = $pdo->query("
    SELECT COUNT(*) as total
    FROM estudiantes
");

$totalRegistros = $totalStmt->fetch()['total'];

$totalPaginas = ceil($totalRegistros / $porPagina);

# CONSULTA PAGINADA

$stmt = $pdo->prepare("
    SELECT *
    FROM estudiantes
    ORDER BY creado_en DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$estudiantes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>CRUD Estudiantes</title>

<style>

body{
    font-family: Arial;
    margin:40px;
}

input{
    padding:10px;
    margin:5px;
}

button{
    padding:10px;
}

table{
    border-collapse: collapse;
    width:100%;
    margin-top:20px;
}

th,td{
    border:1px solid #ccc;
    padding:10px;
}

.error{
    color:red;
}

</style>

</head>

<body>

<h1>CRUD Estudiantes</h1>

<?php if($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">

<input
name="nombre"
placeholder="Nombre"
required
>

<input
name="email"
type="email"
placeholder="Email"
required
>

<input
name="carrera"
placeholder="Carrera"
required
>

<button type="submit">
Guardar
</button>

</form>

<h2>
Lista de estudiantes (<?= count($estudiantes) ?>)
</h2>

<table>

<tr>
<th>ID</th>
<th>Nombre</th>
<th>Email</th>
<th>Carrera</th>
<th>Acciones</th>
</tr>

<?php foreach($estudiantes as $e): ?>

<tr>

<td><?= $e['id'] ?></td>

<td><?= htmlspecialchars($e['nombre']) ?></td>

<td><?= htmlspecialchars($e['email']) ?></td>

<td><?= htmlspecialchars($e['carrera']) ?></td>

<td>

<a
href="?delete=<?= $e['id'] ?>"
onclick="return confirm('¿Eliminar?')"
>
Eliminar
</a>

</td>

</tr>

<?php endforeach; ?>

</table>
<div style="margin-top:20px;">

<?php if($paginaActual > 1): ?>

<a href="?pagina=<?= $paginaActual - 1 ?>">

<button>
Anterior
</button>

</a>

<?php endif; ?>

<span style="margin:0 10px;">
Página <?= $paginaActual ?> de <?= $totalPaginas ?>
</span>

<?php if($paginaActual < $totalPaginas): ?>

<a href="?pagina=<?= $paginaActual + 1 ?>">

<button>
Siguiente
</button>

</a>

<?php endif; ?>

</div>
</body>
</html>