<?php

$titulo = 'Dashboard';

require_once __DIR__ . '/includes/layouts/header.php';
require_once __DIR__ . '/controllers/DashboardController.php';

$controller = new DashboardController();
$dados = $controller->obterDados();

$cacambaAtual = $dados['cacambaAtual'];
$ultimaCacamba = $dados['ultimaCacamba'];
$totalDescartados = $dados['totalDescartados'];
$totalCacambas = $dados['totalCacambas'];
$ultimosItens = $dados['ultimosItens'];

require_once __DIR__ . '/includes/layouts/menu.php';
?>

<main class="main-content">

    <header class="topbar">

        <div>
            <h1>Dashboard</h1>

            <p class="topbar-subtitulo">
                Visão geral do descarte de equipamentos
            </p>
        </div>

        <div class="topbar-user">
            <strong>
                <?= htmlspecialchars($usuario['nome']) ?>
            </strong>

            <span>
                <?= htmlspecialchars($usuario['tipo']) ?>
            </span>
        </div>

    </header>

    <section class="page-content">

        <?php require_once __DIR__ . '/views/dashboard/index.php'; ?>

    </section>

<?php require_once __DIR__ . '/includes/layouts/footer.php'; ?>