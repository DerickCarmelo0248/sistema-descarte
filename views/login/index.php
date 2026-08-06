<?php

require_once __DIR__ . '/../../config/session.php';

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Sistema de Descarte</title>

    <link
        rel="stylesheet"
        href="/assets/css/login.css"
    >
</head>

<body>

    <main class="login-container">

        <section class="login-card">

            <header class="login-header">
                <h1>Sistema de Descarte</h1>
                <p>Controle de ativos e equipamentos de TI</p>
            </header>

            <?php if ($erro): ?>
                <div class="alerta-erro">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login.php">

                <div class="campo">
                    <label for="email">E-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit">
                    Entrar
                </button>

            </form>

        </section>

    </main>

</body>

</html>