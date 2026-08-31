<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Cacamba.php';

class CacambaController
{
    private Cacamba $model;

    public function __construct()
    {
        $this->model = new Cacamba();
    }

    public function paginaAtual(): array
    {
        $usuarioId = (int) $_SESSION['usuario']['id'];

        $cacamba = $this->model->buscarAtual();

        if (!$cacamba) {
            $cacamba = $this->model->criarProxima($usuarioId);
        }

        $itens = $this->model->listarItens(
            (int) $cacamba['id']
        );

        return [
            'cacamba' => $cacamba,
            'itens' => $itens
        ];
    }

    public function adicionar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar();
        }

        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);

        $patrimonio = trim(
            $_POST['patrimonio'] ?? ''
        );

        $tipo = trim(
            $_POST['tipo'] ?? ''
        );

        $descricao = trim(
            $_POST['descricao'] ?? ''
        );

        $usuarioId = (int) $_SESSION['usuario']['id'];

        if ($cacambaId <= 0) {
            $this->mensagem(
                'erro',
                'Caçamba inválida.'
            );

            $this->redirecionar();
        }

        if ($patrimonio === '') {
            $this->mensagem(
                'erro',
                'Informe o patrimônio.'
            );

            $this->redirecionar();
        }

        if (strlen($patrimonio) > 100) {
            $this->mensagem(
                'erro',
                'O patrimônio é muito longo.'
            );

            $this->redirecionar();
        }

        if ($tipo === '') {
            $this->mensagem(
                'erro',
                'Informe o tipo do equipamento.'
            );

            $this->redirecionar();
        }

        if (strlen($tipo) > 100) {
            $this->mensagem(
                'erro',
                'O tipo do equipamento é muito longo.'
            );

            $this->redirecionar();
        }

        if (strlen($descricao) > 255) {
            $this->mensagem(
                'erro',
                'A descrição é muito longa.'
            );

            $this->redirecionar();
        }

        try {

            $this->model->adicionarItem(
                $cacambaId,
                $patrimonio,
                $tipo,
                $descricao ?: null,
                $usuarioId
            );

            $this->mensagem(
                'sucesso',
                'Equipamento adicionado com sucesso.'
            );

        } catch (PDOException $erro) {

            if ($erro->getCode() === '23505') {

                $this->mensagem(
                    'erro',
                    'Este patrimônio já está registrado nesta caçamba.'
                );

            } else {

                $this->mensagem(
                    'erro',
                    'Erro ao adicionar equipamento.'
                );
            }

        } catch (Throwable $erro) {

            $this->mensagem(
                'erro',
                $erro->getMessage()
            );
        }

        $this->redirecionar();
    }

    public function remover(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar();
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);

        if ($itemId <= 0 || $cacambaId <= 0) {

            $this->mensagem(
                'erro',
                'Registro inválido.'
            );

            $this->redirecionar();
        }

        try {

            $this->model->removerItem(
                $itemId,
                $cacambaId
            );

            $this->mensagem(
                'sucesso',
                'Equipamento removido da caçamba.'
            );

        } catch (Throwable $erro) {

            $this->mensagem(
                'erro',
                $erro->getMessage()
            );
        }

        $this->redirecionar();
    }

    public function finalizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar();
        }

        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);

        $dataDescarte = trim(
            $_POST['data_descarte'] ?? ''
        );

        $numeroLaudo = trim(
            $_POST['numero_laudo'] ?? ''
        );

        $observacoes = trim(
            $_POST['observacoes'] ?? ''
        );

        $confirmacao = isset(
            $_POST['confirmacao']
        );

        $usuarioId = (int) $_SESSION['usuario']['id'];

        if ($cacambaId <= 0) {

            $this->mensagem(
                'erro',
                'Caçamba inválida.'
            );

            $this->redirecionar();
        }

        if ($dataDescarte === '') {

            $this->mensagem(
                'erro',
                'Informe a data do descarte.'
            );

            $this->redirecionar();
        }

        if (!$confirmacao) {

            $this->mensagem(
                'erro',
                'Confirme a finalização da caçamba.'
            );

            $this->redirecionar();
        }

        try {

            $this->model->finalizar(
                $cacambaId,
                $dataDescarte,
                $numeroLaudo ?: null,
                $observacoes ?: null,
                $usuarioId
            );

            $this->mensagem(
                'sucesso',
                'Caçamba finalizada. Uma nova caçamba foi criada.'
            );

        } catch (Throwable $erro) {

            $this->mensagem(
                'erro',
                $erro->getMessage()
            );
        }

        $this->redirecionar();
    }

    private function mensagem(
        string $tipo,
        string $texto
    ): void {

        $_SESSION['mensagem'] = [
            'tipo' => $tipo,
            'texto' => $texto
        ];
    }

    private function redirecionar(): never
    {
        header('Location: /cacambas.php');
        exit;
    }
}