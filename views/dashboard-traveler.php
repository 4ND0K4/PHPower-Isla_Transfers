<?php
session_start();
$isTraveler = isset($_SESSION['travelerUser']);
$emailCliente = $isTraveler ? $_SESSION['travelerUser'] : ''; // Usa el email de la sesión si es un traveler
?>
<?php include '../controllers/travelers/getSession.php' ?>
<?php include '../controllers/travelers/update.php' ?>
<?php include '../views/modal.php' ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Panel de Administración</title>
    <meta name="author" content="PHPOWER" />
    <meta name="description" content="La página de inicio del panel de Clientes particulares (Viajeros) de Isla Transfer
    es accesible cuando el usuario viajero se identifica con sus credenciales. Desde aquí se puede acceder
    a la gestión de todas las acciones disponibles en la aplicación web para este tipo de usuario" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Paaji+2:wght@400..800&family=Caveat&family=Roboto+Flex:opsz@8..144&display=swap" rel="stylesheet">
    <!-- Enlaces CDN -->
    <!-- Enlaces CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Enlaces Hojas Estilo-->
    <link rel="stylesheet" href="../assets/css/general.css">
    <link rel="stylesheet" href="../assets/css/traveler.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- FullCalendar.io -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: "es",
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '../controllers/bookings/getCalendar.php',
                eventDidMount: function(info) {
                    console.log(info.event.extendedProps);
                        // Verifica el creador de la reserva y cambia el color del evento
                        if (info.event.extendedProps.creado_por === 'admin') {
                            info.el.style.backgroundColor = '#343a40'; // Color para reservas creadas por el admin (por ejemplo, gris oscuro)
                        } else if (info.event.extendedProps.creado_por === 'traveler') {
                            info.el.style.backgroundColor = '#6c757d'; // Color para reservas creadas por el traveler (por ejemplo, gris claro)
                        }
                },
                eventClick: function(info) {
                    Swal.fire({
                        title: 'Detalles de la Reserva',
                        html: `
                    <strong>Tipo de Reserva:</strong> ${info.event.extendedProps.id_tipo_reserva == 1 ? 'Aeropuerto-Hotel' : 'Hotel-Aeropuerto'}<br>
                    <strong>Dia llegada:</strong> ${info.event.start} <br>
                    <strong>Hora</strong> ${info.event.extendedProps.hora_entrada} <br>
                    <strong>Hora</strong> ${info.event.extendedProps.hora_vuelo_salida} <br>
                    <strong>Hotel</strong> ${info.event.title} <br>
                    <strong>Cliente:</strong> ${info.event.extendedProps.email_cliente} <br>
                    <strong>Origen vuelo</strong> ${info.event.extendedProps.origen_vuelo_entrada} <br>
                    <strong>Nº viajeros</strong> ${info.event.extendedProps.num_viajeros} <br>
                    <strong>Vehículo</strong> ${info.event.extendedProps.id_vehiculo} <br>
                    <strong>Localizador</strong> ${info.event.extendedProps.localizador} <br>
                    <strong>Reserva ID:</strong> ${info.event.id} <br>
                `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            confirmButton: 'my-confirm-button'
                        }
                    });
                }
            });
            calendar.render();
        });
    </script>
</head>
<body>
<nav class="navbar navbar-expand-xl bg-transparent">
    <div class="container-fluid">
        <a class="navbar-brand fs-4 ps-5" href="dashboard-traveler.php" id="logo">
            <img src="../assets/img/logo.png" alt="" width="30" height="24" class="d-inline-block align-text-top">
            Isla Transfer
        </a>
        <ul class="nav nav-pills justify-content-end">
            <li class="nav-item">
                <!-- BOTÓN ACTUALIZAR -->
                <button onclick="abrirModalActualizar(<?php echo htmlspecialchars(json_encode($travelerData)); ?>)" class="btn btn-primary bg-transparent border-0 fs-5 fw-bold text-secondary" data-bs-toggle="modal" data-bs-target="#updateTravelerModal"><i class="bi bi-person-gear px-2 text-secondary"></i>Perfil</button>
            </li>
            <li class="pt-2">
                <!-- En tu archivo dashboard-traveler.php -->
                <a href="../controllers/travelers/login.php?action=logout" class="fs-5 pt-3 text-decoration-none text-danger"><i class="bi bi-plug"></i>Cerrar sesión</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h1 class="text-center pt-3">Panel de Usuario</h1>
    <div class="container-fluid">
        <div class="col text-start  py-1">
            <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                Nueva reserva
            </button>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'update_exitoso'): ?>
        <div class="alert alert-success" role="alert">
            Perfil actualizado correctamente.
        </div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'update_fallido'): ?>
        <div class="alert alert-danger" role="alert">
            Error al actualizar el perfil. Inténtelo de nuevo.
        </div>
    <?php endif; ?>
</div>

<div class="container">
    <div class="col-xl">
        <div id="calendar"></div>
    </div>
</div>
<!-- Modal de actualizar Perfil viajero -->
<div class="modal fade" id="updateTravelerModal" tabindex="-1" aria-labelledby="updateTravelerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Modificar Perfil</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../controllers/travelers/update.php" method="POST">
                    <!-- Id Viajero -->
                    <div class="container mt-4">
                        <input type="hidden" name="id_traveler" id="updateIdTravelerInput">
                    </div>
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label text-warning" for="updateNameInput">Nombre</label>
                        <input class="form-control" type="text" name="name" id="updateNameInput" placeholder="Introduce tu nombre">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label text-warning" for="updateSurname1Input">Apellido1</label>
                        <input class="form-control" type="text" name="surname1" id="updateSurname1Input" placeholder="Introduce tu primer apellido">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label text-warning" for="updateSurname2Input">Apellido2</label>
                        <input class="form-control" type="text" name="surname2" id="updateSurname2Input" placeholder="Introduce tu segundo apellido">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label text-warning" for="updateEmailInput">Email</label>
                        <input class="form-control" type="email" name="email" id="updateEmailInput" placeholder="Introduce tu email">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label text-warning" for="updatePasswordInput">Password</label>
                        <input class="form-control" type="password" name="password" id="updatePasswordInput" placeholder="Introduce tu contraseña">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label" for="updateAddressInput">Dirección</label>
                        <input class="form-control" type="text" name="address" id="updateAddressInput" placeholder="Introduce tu dirección aquí">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label" for="updateZipCodeInput">Código Postal</label>
                        <input class="form-control" type="text" name="zipCode" id="updateZipCodeInput" placeholder="Introduce tu código postal">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label" for="updateCityInput">Ciudad</label>
                        <input class="form-control" type="text" name="city" id="updateCityInput" placeholder="Introduce tu ciudad">
                    </div>
                    <!-- ID Zona -->
                    <div class="mb-3">
                        <label class="form-label" for="updateCountryInput">País</label>
                        <input class="form-control" type="text" name="country" id="updateCountryInput" placeholder="Introduce tu país">
                    </div>
            </div>
            <!-- Botones de envio y cierre -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" name="updateTraveler">Modificar</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    //
    <!-- Función que crea las reservas según el tipo de reserva -->
    function mostrarCampos() {
        var tipoReserva = document.getElementById("tipo_reserva").value;
        document.getElementById("aeropuerto-hotel-fields").style.display = (tipoReserva == "1" || tipoReserva == "idayvuelta") ? "block" : "none";
        document.getElementById("hotel-aeropuerto-fields").style.display = (tipoReserva == "2" || tipoReserva == "idayvuelta") ? "block" : "none";
    }
</script>
<script>
        const travelerData = <?php echo isset($travelerData) ? json_encode($travelerData) : 'null'; ?>;
</script>
<script>
//
function abrirModalActualizar() {
    console.log('Se ejecuta la función abrirModalActualizar');
    console.log('Datos de travelerData:', travelerData);
    document.querySelector('#updateIdTravelerInput').value = travelerData.id_traveler || '';
    document.querySelector('#updateNameInput').value = travelerData.name || '';
    document.querySelector('#updateSurname1Input').value = travelerData.surname1 || '';
    document.querySelector('#updateSurname2Input').value = travelerData.surname2 || '';
    document.querySelector('#updateEmailInput').value = travelerData.email || '';
    document.querySelector('#updateAddressInput').value = travelerData.address || '';
    document.querySelector('#updateZipCodeInput').value = travelerData.zipCode || '';
    document.querySelector('#updateCityInput').value = travelerData.city || '';
    document.querySelector('#updateCountryInput').value = travelerData.country || '';
    document.querySelector('#updatePasswordInput').value = ''; // Deja la contraseña en blanco

    var modal = new bootstrap.Modal(document.getElementById('updateTravelerModal'));
    modal.show();
}
</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>