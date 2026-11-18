<?php
/**
 * EcoTrack Analytics — Repositório de Materiais
 * Operações PDO para a tabela 'materiais' e seus fatores ambientais.
 */

require_once __DIR__ . '/../../config/database.php';

function listar_materiais(): array {
    $pdo = obter_pdo();
    $sql = 'SELECT m.id, m.nome_material, m.fator_co2_kg, m.fator_agua_litros, m.fonte_referencia,
                   c.id AS categoria_id, c.nome_categoria, c.cor_hex,
                   (SELECT COUNT(*) FROM coletas col WHERE col.material_id = m.id AND col.ativo = 1) AS total_coletas
            FROM materiais m
            JOIN categorias_materiais c ON c.id = m.categoria_id
            ORDER BY c.nome_categoria ASC, m.nome_material ASC';
    return $pdo->query($sql)->fetchAll();
}

function obter_material_por_id(int $id): ?array {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('SELECT id, categoria_id, nome_material, fator_co2_kg, fator_agua_litros, fonte_referencia FROM materiais WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $mat = $stmt->fetch();
    return $mat ?: null;
}

function criar_material(int $categoria_id, string $nome, float $fator_co2, float $fator_agua, ?string $fonte): int {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('INSERT INTO materiais (categoria_id, nome_material, fator_co2_kg, fator_agua_litros, fonte_referencia)
                           VALUES (:cat_id, :nome, :co2, :agua, :fonte)');
    $stmt->execute([
        ':cat_id' => $categoria_id,
        ':nome'   => $nome,
        ':co2'    => $fator_co2,
        ':agua'   => $fator_agua,
        ':fonte'  => $fonte
    ]);
    return (int) $pdo->lastInsertId();
}

function atualizar_material(int $id, int $categoria_id, string $nome, float $fator_co2, float $fator_agua, ?string $fonte): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('UPDATE materiais
                           SET categoria_id = :cat_id, nome_material = :nome, fator_co2_kg = :co2, fator_agua_litros = :agua, fonte_referencia = :fonte
                           WHERE id = :id');
    return $stmt->execute([
        ':cat_id' => $categoria_id,
        ':nome'   => $nome,
        ':co2'    => $fator_co2,
        ':agua'   => $fator_agua,
        ':fonte'  => $fonte,
        ':id'     => $id
    ]);
}

function excluir_material(int $id): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('DELETE FROM materiais WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
