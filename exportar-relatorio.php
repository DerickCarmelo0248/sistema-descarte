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

$nomeArquivo = 'relatorio_descarte_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');

$saida = fopen('php://output', 'w');

fwrite($saida, "\xEF\xBB\xBF");

fputcsv(
    $saida,
    [
        'Patrimônio',
        'Descrição',
        'Caçamba',
        'Data do descarte',
        'Laudo'
    ],
    ';'
);

foreach ($itens as $item) {

    fputcsv(
        $saida,
        [
            $item['patrimonio'],
            $item['descricao'] ?: '',
            'Caçamba ' . str_pad(
                (string) $item['cacamba_numero'],
                3,
                '0',
                STR_PAD_LEFT
            ),
            date(
                'd/m/Y',
                strtotime($item['data_descarte'])
            ),
            $item['numero_laudo'] ?: ''
        ],
        ';'
    );
}

fclose($saida);
exit;