<?php

/** @var array|false $cacambaAtual */
/** @var array|false $ultimaCacamba */
/** @var int $totalDescartados */
/** @var int $totalCacambas */
/** @var array $ultimosItens */

?>

<section class="dashboard-grid">

    <article class="dashboard-card destaque">

        <div class="card-header">

            <div>

                <span class="card-label">
                    Caçamba atual
                </span>

                <h2>

                    <?php if ($cacambaAtual): ?>

                        Caçamba <?= str_pad(
                            (string) $cacambaAtual['numero'],
                            3,
                            '0',
                            STR_PAD_LEFT
                        ) ?>

                    <?php else: ?>

                        Nenhuma caçamba aberta

                    <?php endif; ?>

                </h2>

            </div>


            <?php if ($cacambaAtual): ?>

                <span class="badge badge-aberta">
                    Aberta
                </span>

            <?php endif; ?>

        </div>


        <?php if ($cacambaAtual): ?>

            <div class="cacamba-resumo">

                <div>

                    <strong>
                        <?= (int) $cacambaAtual['total_itens'] ?>
                    </strong>

                    <span>
                        itens na caçamba
                    </span>

                </div>

                <div>

                    <strong>
                        <?= date(
                            'd/m/Y',
                            strtotime($cacambaAtual['data_abertura'])
                        ) ?>
                    </strong>

                    <span>
                        data de abertura
                    </span>

                </div>

            </div>


            <a
                class="botao botao-primario"
                href="/cacambas.php"
            >
                Acessar caçamba atual
            </a>

        <?php else: ?>

            <p class="texto-secundario">
                Uma nova caçamba será criada ao iniciar o próximo lote.
            </p>

        <?php endif; ?>

    </article>


    <article class="dashboard-card">

        <span class="card-label">
            Equipamentos descartados
        </span>

        <strong class="card-valor">
            <?= (int) $totalDescartados ?>
        </strong>

        <span class="card-descricao">
            Total registrado no sistema
        </span>

    </article>


    <article class="dashboard-card">

        <span class="card-label">
            Caçambas finalizadas
        </span>

        <strong class="card-valor">
            <?= (int) $totalCacambas ?>
        </strong>

        <span class="card-descricao">
            Lotes concluídos
        </span>

    </article>


    <article class="dashboard-card">

        <span class="card-label">
            Último descarte
        </span>

        <?php if ($ultimaCacamba): ?>

            <strong class="card-valor card-valor-menor">

                Caçamba <?= str_pad(
                    (string) $ultimaCacamba['numero'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>

            </strong>

            <span class="card-descricao">

                <?= date(
                    'd/m/Y',
                    strtotime($ultimaCacamba['data_descarte'])
                ) ?>

                ·

                <?= (int) $ultimaCacamba['total_itens'] ?>
                itens

            </span>

        <?php else: ?>

            <strong class="card-valor card-valor-menor">
                Nenhum
            </strong>

            <span class="card-descricao">
                Nenhum descarte foi finalizado
            </span>

        <?php endif; ?>

    </article>

</section>


<section class="painel">

    <div class="painel-header">

        <div>

            <h2>
                Últimos itens adicionados
            </h2>

            <p>
                Equipamentos registrados nas caçambas mais recentes.
            </p>

        </div>

    </div>


    <?php if ($ultimosItens): ?>

        <div class="tabela-container">

            <table class="tabela">

                <thead>

                    <tr>
                        <th>Patrimônio</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Caçamba</th>
                        <th>Adicionado em</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($ultimosItens as $item): ?>

                        <tr>

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $item['patrimonio']
                                        ?: 'Sem patrimônio'
                                    ) ?>
                                </strong>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['tipo']
                                    ?: 'Não informado'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['descricao']
                                    ?: '-'
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
                                    'd/m/Y H:i',
                                    strtotime($item['data_adicao'])
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="estado-vazio">
            Nenhum equipamento foi adicionado ainda.
        </div>

    <?php endif; ?>

</section>