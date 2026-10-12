<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= defined('APP_NAME') ? APP_NAME : 'Sistema de Tickets - UTIC' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FUENTES SOFISTICADAS: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- Custom CSS (Ruta absoluta a la carpeta public) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css?v=1.0">
    <script src="https://www.google.com/recaptcha/api.js?render=6Lc5KewtAAAAAPTOfJ-K9jK6tSUbaxglyTzkHHi"></script>

</head>

<body>
    <canvas id="particles-canvas"></canvas>

    <div class="main-wrapper">