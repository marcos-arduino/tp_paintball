<?php
try {
    $conexion = new PDO("mysql:host=localhost;port=3306;dbname=paintball_db", "root", "");
} catch (PDOException) {
    header("Location: index.html?rv=0");
    echo $error->getMessage();
    die();
} finally {
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    return $conexion;
}

$nombre = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$fecha = isset($_POST['date']) ? $_POST['date'] : '';
$hora = isset($_POST['time']) ? $_POST['time'] : '';
$cantidad_jugadores = isset($_POST['players']) ? $_POST['players'] : '';


$pdo2 = $conexion->prepare('SELECT cliente_id FROM clientes WHERE email = ?');
$pdo2->bindParam(1, $email);
$pdo2->execute();
$cliente = $pdo2->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    $cliente_id = $cliente['cliente_id'];
} else {
    $pdo3 = $conexion->prepare('
        INSERT INTO clientes VALUES (NULL, ?, ?, ?)
    ');
    $pdo3->bindParam(1, $nombre);
    $pdo3->bindParam(2, $email);
    $pdo3->bindParam(3, $telefono);
    $pdo3->execute();
    
    $cliente_id = $conexion->lastInsertId();
}

// $pdo4 = $conexion->prepare('
//     INSERT INTO reservas (cliente_id, fecha, hora, cantidad_jugadores) 
//     VALUES (?, ?, ?, ?)
// ');
// $pdo4->bindParam(1, $cliente_id);
// $pdo4->bindParam(2, $fecha);
// $pdo4->bindParam(3, $hora);
// $pdo4->bindParam(4, $cantidad_jugadores);
// $pdo4->execute();

// $reserva_id = $conexion->lastInsertId();


echo "Reserva registrada con éxito";
header("Location: index.html?rv=1");

?>