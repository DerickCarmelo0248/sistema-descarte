<?php

/** @var array $cacamba */
/** @var array $itens */
/** @var array|null $mensagem */

?>

<?php if ($mensagem): ?>

    <div class="alerta alerta-<?= htmlspecialchars($mensagem['tipo']) ?>">
        <?= htmlspecialchars($mensagem['texto']) ?>
    </div>

<?php endif; ?>


<section class="painel">

    <div class="cabecalho-cacamba">

        <div>

            <span class="card-label">
                Lote atual
            </span>

            <h2>
                Caçamba <?= str_pad(
                    (string) $cacamba['numero'],
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>
            </h2>

            <p>
                Aberta em
                <?= date(
                    'd/m/Y H:i',
                    strtotime($cacamba['data_abertura'])
                ) ?>
            </p>

        </div>

        <div>

            <strong>
                <?= count($itens) ?>
            </strong>

            <span>
                item(ns)
            </span>

        </div>

    </div>

</section>


<section class="painel">

    <div class="painel-header">

        <div>
            <h2>Adicionar equipamento</h2>

            <p>
                Informe os dados do equipamento que será descartado.
            </p>
        </div>

    </div>


    <form
        method="POST"
        action="/cacambas.php?acao=adicionar"
    >

        <input
            type="hidden"
            name="cacamba_id"
            value="<?= (int) $cacamba['id'] ?>"
        >

        <div class="formulario-item">

            <div>

                <label for="patrimonio">
                    Patrimônio
                </label>

                <input
                    type="text"
                    id="patrimonio"
                    name="patrimonio"
                    required
                    autofocus
                    maxlength="100"
                >

            </div>


            <div>

                <label for="tipo">
                    Tipo
                </label>

                <select
                    id="tipo"
                    name="tipo"
                    required
                >

                    <option value="">
                        Selecione
                    </option>

                    <option value="Monitor">
                        Monitor
                    </option>

                    <option value="Computador">
                        Computador
                    </option>

                    <option value="Notebook">
                        Notebook
                    </option>

                    <option value="Tablet">
                        Tablet
                    </option>

                    <option value="Impressora">
                        Impressora
                    </option>

                    <option value="Celular">
                        Celular
                    </option>

                    <option value="Coletor">
                        Coletor
                    </option>

                    <option value="Outro">
                        Outro
                    </option>

                </select>

            </div>


            <div>

                <label for="descricao">
                    Descrição
                </label>

                <input
                    type="text"
                    id="descricao"
                    name="descricao"
                    maxlength="255"
                    placeholder="Ex.: Dell 22 polegadas"
                >

            </div>


            <div>

                <button
                    type="submit"
                    class="botao botao-primario"
                >
                    Adicionar
                </button>

            </div>

        </div>

    </form>

</section>


<section class="painel">

    <div class="painel-header">

        <div>

            <h2>
                Equipamentos na caçamba
            </h2>

            <p>
                <?= count($itens) ?> item(ns) aguardando descarte.
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
                        <th>Data</th>
                        <th>Ações</th>
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
                                    $item['tipo'] ?: '-'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['descricao'] ?: '-'
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

                            <td>

                                <form
                                    method="POST"
                                    action="/cacambas.php?acao=remover"
                                >

                                    <input
                                        type="hidden"
                                        name="item_id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="cacamba_id"
                                        value="<?= (int) $cacamba['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="botao botao-perigo"
                                        onclick="return confirm('Remover este equipamento da caçamba?')"
                                    >
                                        Remover
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="estado-vazio">
            Nenhum equipamento foi adicionado nesta caçamba.
        </div>

    <?php endif; ?>

</section>


<section class="painel">

    <div class="painel-header">

        <div>

            <h2>
                Finalizar caçamba
            </h2>

            <p>
                Após a finalização, os itens não poderão mais ser alterados.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="/cacambas.php?acao=finalizar"
        class="formulario-finalizacao"
    >

        <input
            type="hidden"
            name="cacamba_id"
            value="<?= (int) $cacamba['id'] ?>"
        >


        <div>

            <label for="data_descarte">
                Data do descarte
            </label>

            <input
                type="date"
                id="data_descarte"
                name="data_descarte"
                value="<?= date('Y-m-d') ?>"
                required
            >

        </div>


        <div>

            <label for="numero_laudo">
                Laudo / comprovante
            </label>

            <input
                type="text"
                id="numero_laudo"
                name="numero_laudo"
                maxlength="100"
            >

        </div>


        <div>

            <label for="observacoes">
                Observações
            </label>

            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
            ></textarea>

        </div>


        <label>

            <input
                type="checkbox"
                name="confirmacao"
                value="1"
                required
            >

            Confirmo que os equipamentos desta caçamba foram descartados.

        </label>


        <div>

            <button
                type="submit"
                class="botao botao-perigo"
                <?= !$itens ? 'disabled' : '' ?>
            >
                Finalizar caçamba
            </button>

        </div>

    </form>

</section>