<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

$periodo = 'Todos os períodos';

if ($dataInicio !== '' && $dataFim !== '') {
    $periodo =
        date('d/m/Y', strtotime($dataInicio))
        . ' até '
        . date('d/m/Y', strtotime($dataFim));
} elseif ($dataInicio !== '') {
    $periodo =
        'A partir de '
        . date('d/m/Y', strtotime($dataInicio));
} elseif ($dataFim !== '') {
    $periodo =
        'Até '
        . date('d/m/Y', strtotime($dataFim));
}

$cacambaTexto = 'Todas';

if ($cacambaNumero !== '') {
    $cacambaTexto = 'Caçamba ' . str_pad(
        (string) $cacambaNumero,
        3,
        '0',
        STR_PAD_LEFT
    );
}

$html = '
<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<style>

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #222;
        margin: 30px;
    }

    h1 {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .subtitulo {
        color: #666;
        margin-bottom: 25px;
    }

    .informacoes {
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ddd;
        background: #f7f7f7;
    }

    .informacoes p {
        margin: 4px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #eeeeee;
        padding: 8px;
        text-align: left;
        border: 1px solid #cccccc;
    }

    td {
        padding: 7px;
        border: 1px solid #dddddd;
    }

    .total {
        margin-top: 15px;
        font-weight: bold;
    }

    .rodape {
        margin-top: 30px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        font-size: 9px;
        color: #666;
    }

</style>

</head>

<body>

<h1>Relatório de Equipamentos Descartados</h1>

<div class="subtitulo">
    Sistema de Descarte de Equipamentos de TI
</div>

<div class="informacoes">

    <p>
        <strong>Período:</strong>
        ' . htmlspecialchars($periodo) . '
    </p>

    <p>
        <strong>Caçamba:</strong>
        ' . htmlspecialchars($cacambaTexto) . '
    </p>

    <p>
        <strong>Total de equipamentos:</strong>
        ' . count($itens) . '
    </p>

</div>

<table>

<thead>

<tr>
    <th>Patrimônio</th>
    <th>Descrição</th>
    <th>Caçamba</th>
    <th>Data do descarte</th>
    <th>Laudo</th>
</tr>

</thead>

<tbody>
';

foreach ($itens as $item) {

    $html .= '
    <tr>

        <td>
            ' . htmlspecialchars($item['patrimonio']) . '
        </td>

        <td>
            ' . htmlspecialchars($item['descricao'] ?: '-') . '
        </td>

        <td>
            Caçamba ' . str_pad(
                (string) $item['cacamba_numero'],
                3,
                '0',
                STR_PAD_LEFT
            ) . '
        </td>

        <td>
            ' . date(
                'd/m/Y',
                strtotime($item['data_descarte'])
            ) . '
        </td>

        <td>
            ' . htmlspecialchars($item['numero_laudo'] ?: '-') . '
        </td>

    </tr>
    ';
}

$html .= '
</tbody>

</table>

<div class="total">
    Total: ' . count($itens) . ' equipamento(s)
</div>

<div class="rodape">
    Relatório gerado em ' . date('d/m/Y H:i') . '
</div>

</body>

</html>
';

$options = new Options();

$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', false);

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$nomeArquivo =
    'relatorio_descarte_'
    . date('Y-m-d_H-i-s')
    . '.pdf';

$dompdf->stream(
    $nomeArquivo,
    [
        'Attachment' => true
    ]
);

exit;