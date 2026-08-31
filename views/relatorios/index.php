<?php

/** @var array $itens */
/** @var array $cacambas */
/** @var string $dataInicio */
/** @var string $dataFim */
/** @var string $cacambaNumero */

?>

<section class="painel">

    <div class="painel-header">
        <div>
            <h2>Filtros</h2>
            <p>Filtre os descartes por período ou caçamba.</p>
        </div>
    </div>

    <form method="GET" action="/relatorios.php">

        <div class="formulario-item">

            <div>
                <label for="data_inicio">Data inicial</label>

                <input
                    type="date"
                    id="data_inicio"
                    name="data_inicio"
                    value="<?= htmlspecialchars($dataInicio) ?>"
                >
            </div>

            <div>
                <label for="data_fim">Data final</label>

                <input
                    type="date"
                    id="data_fim"
                    name="data_fim"
                    value="<?= htmlspecialchars($dataFim) ?>"
                >
            </div>

            <div>
                <label for="cacamba">Caçamba</label>

                <select
                    id="cacamba"
                    name="cacamba"
                >
                    <option value="">
                        Todas
                    </option>

                    <?php foreach ($cacambas as $cacamba): ?>

                        <option
                            value="<?= (int) $cacamba['numero'] ?>"
                            <?= $cacambaNumero == $cacamba['numero']
                                ? 'selected'
                                : '' ?>
                        >
                            Caçamba <?= str_pad(
                                (string) $cacamba['numero'],
                                3,
                                '0',
                                STR_PAD_LEFT
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div>
                <button
                    type="submit"
                    class="botao botao-primario"
                >
                    Filtrar
                </button>

                <a
                    href="/relatorios.php"
                    class="botao"
                >
                    Limpar
                </a>
            </div>

        </div>

    </form>

</section>


<section class="painel">

    <div class="painel-header">

        <div>
            <h2>Equipamentos descartados</h2>

            <p>
                <?= count($itens) ?>
                registro(s) encontrado(s)
            </p>
        </div>

    </div>

    <?php if ($itens): ?>

        <div class="tabela-container">

            <table class="tabela">

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

                    <?php foreach ($itens as $item): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $item['patrimonio']
                                    ) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['descricao'] ?: '-'
                                ) ?>
                            </td>

                            <td>
                                Caçamba <?= str_pad(
                                    (string) $item['cacamba_numero'],
                                    3,
                                    '0',
                                    STR_PAD_LEFT
                                ) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y',
                                    strtotime($item['data_descarte'])
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['numero_laudo'] ?: '-'
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="estado-vazio">
            Nenhum registro encontrado com os filtros informados.
        </div>

    <?php endif; ?>

</section>