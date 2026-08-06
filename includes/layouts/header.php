<?php

require_once __DIR__ . '/../auth.php';

$titulo = $titulo ?? 'Sistema de Descarte';
$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($titulo) ?></title>

    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>

<body>

<div class="app">