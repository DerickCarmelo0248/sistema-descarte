<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/Cacamba.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: /historico-cacambas.php');
    exit;
}

$model = new Cacamba();

$cacamba = $model->buscarFinalizadaPorId($id);

if (!$cacamba) {
    die('Caçamba não encontrada.');
}

$itens = $model->listarItens($id);

$titulo = 'Visualizar Caçamba';

require_once __DIR__ . '/includes/layouts/header.php';
require_once __DIR__ . '/includes/layouts/menu.php';
?>

<main class="main-content">

    <header class="topbar">

        <div>
            <h1>
                Caçamba
                <?= str_pad(
                    (string) $cacamba['numero'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>
            </h1>

            <p class="topbar-subtitulo">
                Detalhes do lote de descarte finalizado
            </p>
        </div>

        <div class="topbar-user">
            <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
            <span><?= htmlspecialchars($usuario['tipo']) ?></span>
        </div>

    </header>

    <section class="page-content">

        <?php require __DIR__ . '/views/cacambas/visualizar.php'; ?>

    </section>

<?php require_once __DIR__ . '/includes/layouts/footer.php'; ?>