<aside class="sidebar">

    <div class="sidebar-logo">
        <span>Sistema</span>
        <strong>Descarte TI</strong>
    </div>

    <nav class="sidebar-menu">

        <a href="/index.php">Dashboard</a>

        <a href="/views/equipamentos/index.php">
            Equipamentos
        </a>

        <a href="/cacambas.php">
            Caçamba atual
        </a>

        <a href="/views/relatorios/index.php">
            Relatórios
        </a>

        <a href="/historico-cacambas.php">
            Histórico
        </a>

        <?php if (($usuario['tipo'] ?? '') === 'admin'): ?>
            <a href="/views/usuarios/index.php">
                Usuários
            </a>
        <?php endif; ?>

    </nav>

    <div class="sidebar-footer">
        <a href="/logout.php">Sair</a>
    </div>

</aside>