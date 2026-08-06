<?php

$numeroFormatado = str_pad(
    (string) $cacamba['numero'],
    3,
    '0',
    STR_PAD_LEFT
);
?>

<?php if ($mensagem): ?>
    <div class="alerta alerta-<?= htmlspecialchars($mensagem['tipo']) ?>">
        <?= htmlspecialchars($mensagem['texto']) ?>
    </div>
<?php endif; ?>

<section class="cabecalho-cacamba">

    <div>
        <span class="rotulo">Lote atual</span>

        <h2>Caçamba <?= $numeroFormatado ?></h2>

        <p>
            Aberta em
            <?= date(
                'd/m/Y H:i',
                strtotime($cacamba['data_abertura'])
            ) ?>
        </p>
    </div>

    <div class="resumo-cacamba">
        <strong><?= count($itens) ?></strong>
        <span>itens registrados</span>
    </div>

</section>

<section class="painel formulario-painel">

    <div class="painel-header">
        <div>
            <h2>Adicionar equipamento</h2>
            <p>Informe o patrimônio do item colocado na caçamba.</p>
        </div>
    </div>

    <form
        class="formulario-item"
        method="POST"
        action="/cacambas.php?acao=adicionar"
    >
        <input
            type="hidden"
            name="cacamba_id"
            value="<?= (int) $cacamba['id'] ?>"
        >

        <div class="campo-formulario">
            <label for="patrimonio">Patrimônio</label>

            <input
                type="text"
                id="patrimonio"
                name="patrimonio"
                maxlength="100"
                required
                autofocus
                autocomplete="off"
                placeholder="Digite ou leia o patrimônio"
            >
        </div>

        <div class="campo-formulario campo-descricao">
            <label for="descricao">Descrição opcional</label>

            <input
                type="text"
                id="descricao"
                name="descricao"
                maxlength="255"
                placeholder="Ex.: Monitor Dell 24 polegadas"
            >
        </div>

        <button class="botao botao-primario" type="submit">
            Adicionar
        </button>
    </form>

</section>

<section class="painel">

    <div class="painel-header">
        <div>
            <h2>Itens na caçamba</h2>

            <p>
                Os itens podem ser removidos enquanto o lote estiver aberto.
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
                        <th>Adicionado por</th>
                        <th>Data</th>
                        <th class="coluna-acoes">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= htmlspecialchars($item['patrimonio']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item['descricao'] ?: 'Não informada'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item['adicionado_por']) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($item['data_adicao'])
                                ) ?>
                            </td>

                            <td class="coluna-acoes">
                                <form
                                    method="POST"
                                    action="/cacambas.php?acao=remover"
                                    onsubmit="
                                        return confirm(
                                            'Remover este item da caçamba?'
                                        );
                                    "
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
                                        class="botao-remover"
                                        type="submit"
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
            Nenhum equipamento foi adicionado à caçamba atual.
        </div>

    <?php endif; ?>

</section>

<section class="painel finalizar-painel">

    <div class="painel-header">
        <div>
            <h2>Finalizar caçamba</h2>

            <p>
                Após a finalização, os registros não poderão ser alterados.
            </p>
        </div>
    </div>

    <form
        class="formulario-finalizacao"
        method="POST"
        action="/cacambas.php?acao=finalizar"
        onsubmit="
            return confirm(
                'Confirma a finalização definitiva desta caçamba?'
            );
        "
    >
        <input
            type="hidden"
            name="cacamba_id"
            value="<?= (int) $cacamba['id'] ?>"
        >

        <div class="campo-formulario">
            <label for="data_descarte">Data do descarte</label>

            <input
                type="date"
                id="data_descarte"
                name="data_descarte"
                value="<?= date('Y-m-d') ?>"
                required
            >
        </div>

        <div class="campo-formulario">
            <label for="numero_laudo">Laudo ou comprovante</label>

            <input
                type="text"
                id="numero_laudo"
                name="numero_laudo"
                maxlength="100"
            >
        </div>

        <div class="campo-formulario campo-largo">
            <label for="observacoes">Observações</label>

            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
            ></textarea>
        </div>

        <label class="confirmacao-finalizacao">
            <input
                type="checkbox"
                name="confirmacao"
                value="1"
                required
            >

            Confirmo que os itens foram descartados e que este lote deve ser
            bloqueado definitivamente.
        </label>

        <button
            class="botao botao-perigo"
            type="submit"
            <?= !$itens ? 'disabled' : '' ?>
        >
            Finalizar caçamba <?= $numeroFormatado ?>
        </button>
    </form>

</section>