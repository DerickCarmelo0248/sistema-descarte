<?php

/** @var array $cacambas */
/** @var array $itens */

?>

<section class="painel">

    <div class="painel-header">

        <div>
            <h2>Caçambas finalizadas</h2>

            <p>
                Os registros abaixo são somente para consulta.
            </p>
        </div>

    </div>

    <?php if ($cacambas): ?>

        <div class="tabela-container">

            <table class="tabela">

                <thead>
                    <tr>
                        <th>Caçamba</th>
                        <th>Data do descarte</th>
                        <th>Itens</th>
                        <th>Finalizada por</th>
                        <th>Laudo</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($cacambas as $cacamba): ?>

                        <tr>

                            <td>
                                <strong>
                                    Caçamba
                                    <?= str_pad(
                                        (string) $cacamba['numero'],
                                        3,
                                        '0',
                                        STR_PAD_LEFT
                                    ) ?>
                                </strong>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y',
                                    strtotime($cacamba['data_descarte'])
                                ) ?>
                            </td>

                            <td>
                                <?= (int) $cacamba['total_itens'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $cacamba['finalizada_por']
                                    ?? 'Não informado'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $cacamba['numero_laudo']
                                    ?: '-'
                                ) ?>
                            </td>

                            <td>
                                <a
                                    class="botao botao-primario"
                                    href="/visualizar-cacamba.php?id=<?= (int) $cacamba['id'] ?>"
                                >
                                    Visualizar
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="estado-vazio">
            Nenhuma caçamba foi finalizada ainda.
        </div>

    <?php endif; ?>

</section>