<?php

require_once __DIR__ . '/../config/database.php';

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function obterDados(): array
    {
        return [
            'cacambaAtual' => $this->buscarCacambaAtual(),
            'ultimaCacamba' => $this->buscarUltimaCacamba(),
            'totalDescartados' => $this->contarEquipamentosDescartados(),
            'totalCacambas' => $this->contarCacambasFinalizadas(),
            'ultimosItens' => $this->buscarUltimosItens()
        ];
    }

    private function buscarCacambaAtual(): array|false
    {
        $sql = "
            SELECT
                c.id,
                c.numero,
                c.status,
                c.data_abertura,
                COUNT(ci.id) AS total_itens
            FROM cacambas c
            LEFT JOIN cacamba_itens ci
                ON ci.cacamba_id = c.id
            WHERE c.status = 'aberta'
            GROUP BY
                c.id,
                c.numero,
                c.status,
                c.data_abertura
            LIMIT 1
        ";

        return $this->pdo->query($sql)->fetch();
    }

    private function buscarUltimaCacamba(): array|false
    {
        $sql = "
            SELECT
                c.id,
                c.numero,
                c.data_descarte,
                COUNT(ci.id) AS total_itens
            FROM cacambas c
            LEFT JOIN cacamba_itens ci
                ON ci.cacamba_id = c.id
            WHERE c.status = 'descartada'
            GROUP BY
                c.id,
                c.numero,
                c.data_descarte
            ORDER BY c.data_descarte DESC, c.id DESC
            LIMIT 1
        ";

        return $this->pdo->query($sql)->fetch();
    }

    private function contarEquipamentosDescartados(): int
    {
        $sql = "
            SELECT COUNT(ci.id)
            FROM cacamba_itens ci
            INNER JOIN cacambas c
                ON c.id = ci.cacamba_id
            WHERE c.status = 'descartada'
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    private function contarCacambasFinalizadas(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM cacambas
            WHERE status = 'descartada'
        ";

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    private function buscarUltimosItens(): array
{
    $sql = "
        SELECT
            ci.patrimonio,
            ci.descricao,
            ci.data_adicao,
            c.numero AS cacamba_numero
        FROM cacamba_itens ci
        INNER JOIN cacambas c
            ON c.id = ci.cacamba_id
        ORDER BY ci.data_adicao DESC
        LIMIT 5
    ";

    $stmt = $this->pdo->query($sql);

    return $stmt->fetchAll();
}
}