<?php
/**
 * EcoTrack Analytics — Repositório de Ecopontos
 * Operações PDO para a tabela 'ecopontos'.
 */

require_once __DIR__ . '/../../config/database.php';

function listar_ecopontos(bool $apenas_ativos = false): array {
    $pdo = obter_pdo();
    $sql = 'SELECT id, nome_unidade, endereco, capacidade_toneladas, ativo FROM ecopontos';
    if ($apenas_ativos) {
        $sql .= ' WHERE ativo = 1';
    }
    $sql .= ' ORDER BY nome_unidade ASC';
    return $pdo->query($sql)->fetchAll();
}

function obter_ecoponto_por_id(int $id): ?array {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('SELECT id, nome_unidade, endereco, capacidade_toneladas, ativo FROM ecopontos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $eco = $stmt->fetch();
    return $eco ?: null;
}

function criar_ecoponto(string $nome, string $endereco, float $capacidade): int {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('INSERT INTO ecopontos (nome_unidade, endereco, capacidade_toneladas, ativo) VALUES (:nome, :endereco, :capacidade, 1)');
    $stmt->execute([
        ':nome'       => $nome,
        ':endereco'   => $endereco,
        ':capacidade' => $capacidade
    ]);
    return (int) $pdo->lastInsertId();
}

function atualizar_ecoponto(int $id, string $nome, string $endereco, float $capacidade): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('UPDATE ecopontos SET nome_unidade = :nome, endereco = :endereco, capacidade_toneladas = :capacidade WHERE id = :id');
    return $stmt->execute([
        ':nome'       => $nome,
        ':endereco'   => $endereco,
        ':capacidade' => $capacidade,
        ':id'         => $id
    ]);
}

function alternar_status_ecoponto(int $id, int $novo_status): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('UPDATE ecopontos SET ativo = :ativo WHERE id = :id');
    return $stmt->execute([
        ':ativo' => $novo_status,
        ':id'    => $id
    ]);
}
