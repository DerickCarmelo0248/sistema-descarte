<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Cacamba.php';

$model = new Cacamba();

$cacambas = $model->listarHistorico();

$titulo = 'Histórico de Caçambas';

require_once __DIR__ . '/includes/layouts/header.php';
require_once __DIR__ . '/includes/layouts/menu.php';
?>

<main class="main-content">

    <header class="topbar">

        <div>
            <h1>Histórico de Caçambas</h1>

            <p class="topbar-subtitulo">
                Consulte os lotes de descarte já finalizados
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

        <?php require __DIR__ . '/views/cacambas/historico.php'; ?>

    </section>

<?php require_once __DIR__ . '/includes/layouts/footer.php'; ?>