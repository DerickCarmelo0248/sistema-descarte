<?php

require_once __DIR__ . '/config/session.php';

if (isset($_SESSION['usuario'])) {
    header('Location: /index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/controllers/LoginController.php';

    $controller = new LoginController();
    $controller->autenticar();
}

require_once __DIR__ . '/views/login/index.php';