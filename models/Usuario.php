<?php

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function buscarPorEmail(string $email): array|false
    {
        $sql = '
            SELECT id, nome, email, senha, tipo, ativo
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch();
    }
}