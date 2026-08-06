<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/controllers/CacambaController.php';

$controller = new CacambaController();
$acao = $_GET['acao'] ?? 'listar';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($acao) {
        case 'adicionar':
            $controller->adicionar();
            break;

        case 'remover':
            $controller->remover();
            break;

        case 'finalizar':
            $controller->finalizar();
            break;

        default:
            header('Location: /cacambas.php');
            exit;
    }
}

$dados = $controller->paginaAtual();

$cacamba = $dados['cacamba'];
$itens = $dados['itens'];

$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);

$titulo = 'Caçamba atual';

require_once __DIR__ . '/includes/layouts/header.php';
require_once __DIR__ . '/includes/layouts/menu.php';
?>

<main class="main-content">

    <header class="topbar">

        <div>
            <h1>Caçamba atual</h1>

            <p class="topbar-subtitulo">
                Controle dos equipamentos aguardando descarte
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

        <?php require __DIR__ . '/views/cacambas/atual.php'; ?>

    </section>

<?php require_once __DIR__ . '/includes/layouts/footer.php'; ?>