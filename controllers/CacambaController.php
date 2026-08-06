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

        $itens = $this->model->listarItens((int) $cacamba['id']);

        return [
            'cacamba' => $cacamba,
            'itens' => $itens
        ];
    }

    public function adicionar(): void
    {
        $this->validarPost();

        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);
        $patrimonio = trim($_POST['patrimonio'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $usuarioId = (int) $_SESSION['usuario']['id'];

        if ($cacambaId <= 0 || $patrimonio === '') {
            $this->redirecionarComMensagem(
                'erro',
                'Informe um patrimônio válido.'
            );
        }

        if (mb_strlen($patrimonio) > 100) {
            $this->redirecionarComMensagem(
                'erro',
                'O patrimônio deve ter no máximo 100 caracteres.'
            );
        }

        try {
            $this->model->adicionarItem(
                $cacambaId,
                $patrimonio,
                $descricao,
                $usuarioId
            );

            $this->redirecionarComMensagem(
                'sucesso',
                'Equipamento adicionado à caçamba.'
            );
        } catch (PDOException $erro) {
            if ($erro->getCode() === '23505') {
                $this->redirecionarComMensagem(
                    'erro',
                    'Este patrimônio já está nesta caçamba.'
                );
            }

            throw $erro;
        } catch (Throwable $erro) {
            $this->redirecionarComMensagem(
                'erro',
                $erro->getMessage()
            );
        }
    }

    public function remover(): void
    {
        $this->validarPost();

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);

        try {
            $removido = $this->model->removerItem(
                $itemId,
                $cacambaId
            );

            $this->redirecionarComMensagem(
                $removido ? 'sucesso' : 'erro',
                $removido
                    ? 'Item removido da caçamba.'
                    : 'O item não pôde ser removido.'
            );
        } catch (Throwable $erro) {
            $this->redirecionarComMensagem(
                'erro',
                $erro->getMessage()
            );
        }
    }

    public function finalizar(): void
    {
        $this->validarPost();

        $cacambaId = (int) ($_POST['cacamba_id'] ?? 0);
        $dataDescarte = $_POST['data_descarte'] ?? '';
        $numeroLaudo = trim($_POST['numero_laudo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $confirmacao = $_POST['confirmacao'] ?? '';
        $usuarioId = (int) $_SESSION['usuario']['id'];

        if (
            $cacambaId <= 0 ||
            $dataDescarte === '' ||
            $confirmacao !== '1'
        ) {
            $this->redirecionarComMensagem(
                'erro',
                'Confirme os dados antes de finalizar.'
            );
        }

        try {
            $this->model->finalizar(
                $cacambaId,
                $dataDescarte,
                $numeroLaudo,
                $observacoes,
                $usuarioId
            );

            $this->redirecionarComMensagem(
                'sucesso',
                'Caçamba finalizada e novo lote iniciado.'
            );
        } catch (Throwable $erro) {
            $this->redirecionarComMensagem(
                'erro',
                $erro->getMessage()
            );
        }
    }

    private function validarPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cacambas.php');
            exit;
        }
    }

    private function redirecionarComMensagem(
        string $tipo,
        string $mensagem
    ): never {
        $_SESSION['mensagem'] = [
            'tipo' => $tipo,
            'texto' => $mensagem
        ];

        header('Location: /cacambas.php');
        exit;
    }
}