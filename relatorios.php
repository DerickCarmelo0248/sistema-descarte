<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$pdo = Database::connect();

$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$cacambaNumero = $_GET['cacamba'] ?? '';

$sql = "
    SELECT
        ci.patrimonio,
        ci.descricao,
        ci.data_adicao,
        c.numero AS cacamba_numero,
        c.data_descarte,
        c.numero_laudo
    FROM cacamba_itens ci
    INNER JOIN cacambas c
        ON c.id = ci.cacamba_id
    WHERE c.status = 'descartada'
";

$parametros = [];

if ($dataInicio !== '') {
    $sql .= " AND c.data_descarte >= :data_inicio";
    $parametros[':data_inicio'] = $dataInicio;
}

if ($dataFim !== '') {
    $sql .= " AND c.data_descarte <= :data_fim";
    $parametros[':data_fim'] = $dataFim;
}

if ($cacambaNumero !== '') {
    $sql .= " AND c.numero = :cacamba";
    $parametros[':cacamba'] = (int) $cacambaNumero;
}

$sql .= "
    ORDER BY
        c.data_descarte DESC,
        c.numero DESC,
        ci.data_adicao DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);

$itens = $stmt->fetchAll();

$cacambas = $pdo->query("
    SELECT numero
    FROM cacambas
    WHERE status = 'descartada'
    ORDER BY numero DESC
")->fetchAll();

$titulo = 'Relatórios';

require_once __DIR__ . '/includes/layouts/header.php';
require_once __DIR__ . '/includes/layouts/menu.php';
?>

<main class="main-content">

    <header class="topbar">

        <div>
            <h1>Relatórios</h1>

            <p class="topbar-subtitulo">
                Consulte os equipamentos descartados
            </p>
        </div>

        <div class="topbar-user">
            <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
            <span><?= htmlspecialchars($usuario['tipo']) ?></span>
        </div>

    </header>

    <section class="page-content">

        <?php require __DIR__ . '/views/relatorios/index.php'; ?>

    </section>

<?php require_once __DIR__ . '/includes/layouts/footer.php'; ?>