<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Usuario.php';

class LoginController
{
    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login.php');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $_SESSION['erro_login'] = 'Preencha o e-mail e a senha.';
            header('Location: /login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro_login'] = 'Informe um e-mail válido.';
            header('Location: /login.php');
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
            header('Location: /login.php');
            exit;
        }

        if (!$usuario['ativo']) {
            $_SESSION['erro_login'] = 'Este usuário está desativado.';
            header('Location: /login.php');
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo' => $usuario['tipo']
        ];

        header('Location: /index.php');
        exit;
    }
}