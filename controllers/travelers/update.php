<?php
if (!isset($_SESSION)) {
    session_start();
}

// Asegúrate de incluir el archivo db.php para que la función db_connect esté disponible
require_once __DIR__ . '/../../models/db.php';
require_once __DIR__ . '/../../models/traveler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db_connect();
    if (!$pdo) {
        throw new InvalidArgumentException("No se puede conectar a la base de datos");
    }

    $traveler = new Traveler($pdo);
    $traveler->Id_viajero = $_POST['id_traveler'];
    $traveler->Nombre = $_POST['name'];
    $traveler->Apellido1 = $_POST['surname1'];
    $traveler->Apellido2 = $_POST['surname2'];
    $traveler->Direccion = $_POST['address'];
    $traveler->CodigoPostal = $_POST['zipCode'];
    $traveler->Ciudad = $_POST['city'];
    $traveler->Pais = $_POST['country'];
    $traveler->Email = $_POST['email'];

    // Actualizar solo si se proporciona una nueva contraseña y hashearla
    if (!empty($_POST['password'])) {
        $traveler->Password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }

    if ($traveler->updateTraveler()) {
        header('Location: /views/dashboard-traveler.php?success=update_exitoso');
    } else {
        header('Location: /views/dashboard-traveler.php?error=update_fallido');
    }
    exit();
}