<?php

/** @var array $cacamba */
/** @var array $itens */

?>

<section class="painel">

    <div class="painel-header">

        <div>

            <h2>
                Caçamba
                <?= str_pad(
                    (string) $cacamba['numero'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>
            </h2>

            <p>
                Lote finalizado e disponível somente para consulta.
            </p>

        </div>

        <a
            href="/historico-cacambas.php"
            class="botao"
        >
            Voltar
        </a>

    </div>


    <div class="detalhes-cacamba">

        <p>

            <strong>
                Data do descarte:
            </strong>

            <?= date(
                'd/m/Y',
                strtotime($cacamba['data_descarte'])
            ) ?>

        </p>


        <p>

            <strong>
                Finalizada por:
            </strong>

            <?= htmlspecialchars(
                $cacamba['finalizada_por_nome']
                ?? 'Não informado'
            ) ?>

        </p>


        <p>

            <strong>
                Laudo:
            </strong>

            <?= htmlspecialchars(
                $cacamba['numero_laudo']
                ?: '-'
            ) ?>

        </p>


        <p>

            <strong>
                Observações:
            </strong>

            <?= nl2br(
                htmlspecialchars(
                    $cacamba['observacoes']
                    ?: '-'
                )
            ) ?>

        </p>

    </div>

</section>


<section class="painel">

    <div class="painel-header">

        <div>

            <h2>
                Equipamentos descartados
            </h2>

            <p>
                <?= count($itens) ?> item(ns) neste lote
            </p>

        </div>

    </div>


    <?php if ($itens): ?>

        <div class="tabela-container">

            <table class="tabela">

                <thead>

                    <tr>
                        <th>Patrimônio</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Adicionado por</th>
                        <th>Data de adição</th>
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
                                <?= htmlspecialchars(
                                    $item['adicionado_por']
                                    ?? 'Não informado'
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
            Nenhum equipamento encontrado.
        </div>

    <?php endif; ?>

</section>