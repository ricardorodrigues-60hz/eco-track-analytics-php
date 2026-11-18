<?php
/**
 * EcoTrack Analytics — Repositório de Categorias de Materiais
 * Operações PDO para 'categorias_materiais'.
 */

require_once __DIR__ . '/../../config/database.php';

function listar_categorias(): array {
    $pdo = obter_pdo();
    $sql = 'SELECT c.id, c.nome_categoria, c.cor_hex, COUNT(m.id) AS total_materiais
            FROM categorias_materiais c
            LEFT JOIN materiais m ON m.categoria_id = c.id
            GROUP BY c.id, c.nome_categoria, c.cor_hex
            ORDER BY c.nome_categoria ASC';
    return $pdo->query($sql)->fetchAll();
}

function obter_categoria_por_id(int $id): ?array {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('SELECT id, nome_categoria, cor_hex FROM categorias_materiais WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $cat = $stmt->fetch();
    return $cat ?: null;
}

function nome_categoria_existe(string $nome, ?int $ignorar_id = null): bool {
    $pdo = obter_pdo();
    if ($ignorar_id) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categorias_materiais WHERE nome_categoria = :nome AND id != :id');
        $stmt->execute([':nome' => $nome, ':id' => $ignorar_id]);
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categorias_materiais WHERE nome_categoria = :nome');
        $stmt->execute([':nome' => $nome]);
    }
    return (int) $stmt->fetchColumn() > 0;
}

function criar_categoria(string $nome, string $cor_hex): int {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('INSERT INTO categorias_materiais (nome_categoria, cor_hex) VALUES (:nome, :cor)');
    $stmt->execute([
        ':nome' => $nome,
        ':cor'  => strtoupper($cor_hex)
    ]);
    return (int) $pdo->lastInsertId();
}

function atualizar_categoria(int $id, string $nome, string $cor_hex): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('UPDATE categorias_materiais SET nome_categoria = :nome, cor_hex = :cor WHERE id = :id');
    return $stmt->execute([
        ':nome' => $nome,
        ':cor'  => strtoupper($cor_hex),
        ':id'   => $id
    ]);
}

function excluir_categoria(int $id): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('DELETE FROM categorias_materiais WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
