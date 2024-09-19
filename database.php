<?php
try {
    $conexion = new PDO("mysql:host=localhost;port=3306;dbname=paintball_db", "root", "");
} catch (PDOException) {
    echo $error->getMessage();
    die();
}

$nombre = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$fecha = isset($_POST['date']) ? $_POST['date'] : '';
$hora = isset($_POST['time']) ? $_POST['time'] : '';
$cantidad_jugadores = isset($_POST['players']) ? $_POST['players'] : '';
$sede = isset($_POST['sede']) ? $_POST['sede'] : '';

date_default_timezone_set("America/Argentina/Buenos_Aires");
$fecha_actual = date('Y-m-d');

echo $nombre;
echo $email;

if ($fecha < $fecha_actual) {
    header("Location: ./index.html?error=date");
    exit();
}

echo $hora;

$pdo1 = $conexion->prepare('SELECT horario_id FROM horarios WHERE hora_inicio = ?');
$pdo1->bindParam(1, $hora);
$pdo1->execute();
$horario = $pdo1->fetch(PDO::FETCH_ASSOC);
$horario_id = $horario['horario_id'];

echo $cantidad_jugadores;

$pdo5 = $conexion->prepare('SELECT campo_id FROM sedes WHERE capacidad >= ? and nombre = ?');
$pdo5->bindParam(1, $cantidad_jugadores);
$pdo5->bindParam(2, $sede);
$pdo5->execute();
$sede = $pdo5->fetchAll(PDO::FETCH_ASSOC);
try {
    $sede = $sede[1];
} catch (PDOException) {
    $sede = $sede[0];
}

$pdo1 = $conexion->prepare('SELECT * FROM reservas WHERE fecha = ? and horario = ? and sede = ?');
$pdo1->bindParam(1, $fecha);
$pdo1->bindParam(2, $horario_id);
$pdo1->bindParam(3, $sede['campo_id']);

$pdo1->execute();
$reservas = $pdo1->fetch(PDO::FETCH_ASSOC);
if ($reservas) {
    header("Location: ./index.html?error=overlap");
    exit();
}

$pdo2 = $conexion->prepare('SELECT cliente_id FROM clientes WHERE email = ?');
$pdo2->bindParam(1, $email);
$pdo2->execute();
$cliente = $pdo2->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    $cliente_id = $cliente['cliente_id'];
} else {
    $pdo3 = $conexion->prepare('
        INSERT INTO clientes VALUES (NULL, ?, ?)
    ');
    $pdo3->bindParam(1, $nombre);
    $pdo3->bindParam(2, $email);
    $pdo3->execute();
    
    $cliente_id = $conexion->lastInsertId();
}


$pdo4 = $conexion->prepare('
    INSERT INTO reservas VALUES (NULL, ?, ?, ?, ?, ?)
');
$pdo4->bindParam(1, $cliente_id);
$pdo4->bindParam(2, $fecha);
$pdo4->bindParam(3, $horario_id);
$pdo4->bindParam(4, $cantidad_jugadores);
$pdo4->bindParam(5, $sede['campo_id']);

$pdo4->execute();

$reserva_id = $conexion->lastInsertId();


echo "Reserva registrada con éxito";
