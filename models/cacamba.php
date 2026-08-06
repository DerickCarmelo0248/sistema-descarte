<?php

require_once __DIR__ . '/../config/database.php';

class Cacamba
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function buscarAtual(): array|false
    {
        $sql = "
            SELECT
                c.id,
                c.numero,
                c.status,
                c.data_abertura,
                c.data_descarte,
                c.observacoes,
                COUNT(ci.id) AS total_itens
            FROM cacambas c
            LEFT JOIN cacamba_itens ci
                ON ci.cacamba_id = c.id
            WHERE c.status = 'aberta'
            GROUP BY c.id
            LIMIT 1
        ";

        return $this->pdo->query($sql)->fetch();
    }

    public function criarProxima(int $usuarioId): array
    {
        $this->pdo->beginTransaction();

        try {
            $aberta = $this->buscarAtual();

            if ($aberta) {
                $this->pdo->commit();
                return $aberta;
            }

            $sqlNumero = "
                SELECT COALESCE(MAX(numero), 0) + 1
                FROM cacambas
            ";

            $numero = (int) $this->pdo
                ->query($sqlNumero)
                ->fetchColumn();

            $sql = "
                INSERT INTO cacambas (
                    numero,
                    status,
                    criada_por
                )
                VALUES (
                    :numero,
                    'aberta',
                    :criada_por
                )
                RETURNING
                    id,
                    numero,
                    status,
                    data_abertura
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':numero' => $numero,
                ':criada_por' => $usuarioId
            ]);

            $cacamba = $stmt->fetch();

            $this->pdo->commit();

            return $cacamba;
        } catch (Throwable $erro) {
            $this->pdo->rollBack();
            throw $erro;
        }
    }

    public function listarItens(int $cacambaId): array
    {
        $sql = "
            SELECT
                ci.id,
                ci.patrimonio,
                ci.descricao,
                ci.data_adicao,
                u.nome AS adicionado_por
            FROM cacamba_itens ci
            INNER JOIN usuarios u
                ON u.id = ci.adicionado_por
            WHERE ci.cacamba_id = :cacamba_id
            ORDER BY ci.data_adicao DESC, ci.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':cacamba_id' => $cacambaId
        ]);

        return $stmt->fetchAll();
    }

    public function adicionarItem(
        int $cacambaId,
        string $patrimonio,
        ?string $descricao,
        int $usuarioId
    ): void {
        $sqlStatus = "
            SELECT status
            FROM cacambas
            WHERE id = :id
        ";

        $stmtStatus = $this->pdo->prepare($sqlStatus);
        $stmtStatus->execute([':id' => $cacambaId]);

        if ($stmtStatus->fetchColumn() !== 'aberta') {
            throw new RuntimeException(
                'Não é possível adicionar itens a uma caçamba finalizada.'
            );
        }

        $sql = "
            INSERT INTO cacamba_itens (
                cacamba_id,
                patrimonio,
                descricao,
                adicionado_por
            )
            VALUES (
                :cacamba_id,
                :patrimonio,
                :descricao,
                :adicionado_por
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':cacamba_id' => $cacambaId,
            ':patrimonio' => $patrimonio,
            ':descricao' => $descricao ?: null,
            ':adicionado_por' => $usuarioId
        ]);
    }

    public function removerItem(int $itemId, int $cacambaId): bool
    {
        $sql = "
            DELETE FROM cacamba_itens
            WHERE id = :id
              AND cacamba_id = :cacamba_id
              AND EXISTS (
                  SELECT 1
                  FROM cacambas
                  WHERE id = :cacamba_id
                    AND status = 'aberta'
              )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $itemId,
            ':cacamba_id' => $cacambaId
        ]);

        return $stmt->rowCount() > 0;
    }

    public function finalizar(
        int $cacambaId,
        string $dataDescarte,
        ?string $numeroLaudo,
        ?string $observacoes,
        int $usuarioId
    ): void {
        $this->pdo->beginTransaction();

        try {
           $sqlBloqueio = "
                SELECT
                    c.id,
                    c.status,
                    (
                        SELECT COUNT(*)
                        FROM cacamba_itens ci
                        WHERE ci.cacamba_id = c.id
                    ) AS total_itens
                FROM cacambas c
                WHERE c.id = :id
                FOR UPDATE
            ";

            $stmtBloqueio = $this->pdo->prepare($sqlBloqueio);
            $stmtBloqueio->execute([':id' => $cacambaId]);

            $cacamba = $stmtBloqueio->fetch();

            if (!$cacamba) {
                throw new RuntimeException('Caçamba não encontrada.');
            }

            if ($cacamba['status'] !== 'aberta') {
                throw new RuntimeException(
                    'Esta caçamba já foi finalizada.'
                );
            }

            if ((int) $cacamba['total_itens'] === 0) {
                throw new RuntimeException(
                    'Não é possível finalizar uma caçamba vazia.'
                );
            }

            $sqlFinalizar = "
                UPDATE cacambas
                SET
                    status = 'descartada',
                    data_descarte = :data_descarte,
                    numero_laudo = :numero_laudo,
                    observacoes = :observacoes,
                    finalizada_por = :finalizada_por,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
                  AND status = 'aberta'
            ";

            $stmtFinalizar = $this->pdo->prepare($sqlFinalizar);

            $stmtFinalizar->execute([
                ':data_descarte' => $dataDescarte,
                ':numero_laudo' => $numeroLaudo ?: null,
                ':observacoes' => $observacoes ?: null,
                ':finalizada_por' => $usuarioId,
                ':id' => $cacambaId
            ]);

            $sqlNumero = "
                SELECT COALESCE(MAX(numero), 0) + 1
                FROM cacambas
            ";

            $proximoNumero = (int) $this->pdo
                ->query($sqlNumero)
                ->fetchColumn();

            $sqlNova = "
                INSERT INTO cacambas (
                    numero,
                    status,
                    criada_por
                )
                VALUES (
                    :numero,
                    'aberta',
                    :criada_por
                )
            ";

            $stmtNova = $this->pdo->prepare($sqlNova);

            $stmtNova->execute([
                ':numero' => $proximoNumero,
                ':criada_por' => $usuarioId
            ]);

            $this->pdo->commit();
        } catch (Throwable $erro) {
            $this->pdo->rollBack();
            throw $erro;
        }
    }

    public function listarHistorico(): array
    {
        $sql = "
            SELECT
                c.id,
                c.numero,
                c.data_abertura,
                c.data_descarte,
                c.numero_laudo,
                c.observacoes,
                COUNT(ci.id) AS total_itens,
                u.nome AS finalizada_por
            FROM cacambas c
            LEFT JOIN cacamba_itens ci
                ON ci.cacamba_id = c.id
            LEFT JOIN usuarios u
                ON u.id = c.finalizada_por
            WHERE c.status = 'descartada'
            GROUP BY c.id, u.nome
            ORDER BY c.data_descarte DESC, c.numero DESC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarFinalizadaPorId(int $id): array|false
    {
        $sql = "
            SELECT
                c.*,
                criador.nome AS criada_por_nome,
                finalizador.nome AS finalizada_por_nome
            FROM cacambas c
            INNER JOIN usuarios criador
                ON criador.id = c.criada_por
            LEFT JOIN usuarios finalizador
                ON finalizador.id = c.finalizada_por
            WHERE c.id = :id
              AND c.status = 'descartada'
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }
}