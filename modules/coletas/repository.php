<?php
/**
 * EcoTrack Analytics — Repositório de Coletas
 * Consultas PDO para a tabela fato 'coletas' (exclusão lógica com ativo = 0).
 */

require_once __DIR__ . '/../../config/database.php';

function contar_coletas(array $filtros = [], ?int $usuario_id_restrito = null): int {
    $pdo = obter_pdo();
    $sql = 'SELECT COUNT(*) FROM coletas c WHERE c.ativo = 1';
    $params = [];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :usuario_restrito';
        $params[':usuario_restrito'] = $usuario_id_restrito;
    }

    if (!empty($filtros['ecoponto_id'])) {
        $sql .= ' AND c.ecoponto_id = :ecoponto_id';
        $params[':ecoponto_id'] = (int) $filtros['ecoponto_id'];
    }

    if (!empty($filtros['material_id'])) {
        $sql .= ' AND c.material_id = :material_id';
        $params[':material_id'] = (int) $filtros['material_id'];
    }

    if (!empty($filtros['data_inicio'])) {
        $sql .= ' AND c.data_coleta >= :data_inicio';
        $params[':data_inicio'] = $filtros['data_inicio'];
    }

    if (!empty($filtros['data_fim'])) {
        $sql .= ' AND c.data_coleta <= :data_fim';
        $params[':data_fim'] = $filtros['data_fim'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function listar_coletas(array $filtros = [], ?int $usuario_id_restrito = null, int $limite = 20, int $offset = 0): array {
    $pdo = obter_pdo();
    $sql = 'SELECT c.id, c.peso_kg, c.data_coleta, c.observacoes, c.created_at, c.updated_at,
                   u.id AS usuario_id, u.nome AS usuario_nome,
                   e.id AS ecoponto_id, e.nome_unidade AS ecoponto_nome,
                   m.id AS material_id, m.nome_material, m.fator_co2_kg, m.fator_agua_litros,
                   cat.id AS categoria_id, cat.nome_categoria, cat.cor_hex
            FROM coletas c
            JOIN usuarios u ON u.id = c.usuario_id
            JOIN ecopontos e ON e.id = c.ecoponto_id
            JOIN materiais m ON m.id = c.material_id
            JOIN categorias_materiais cat ON cat.id = m.categoria_id
            WHERE c.ativo = 1';
    $params = [];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :usuario_restrito';
        $params[':usuario_restrito'] = $usuario_id_restrito;
    }

    if (!empty($filtros['ecoponto_id'])) {
        $sql .= ' AND c.ecoponto_id = :ecoponto_id';
        $params[':ecoponto_id'] = (int) $filtros['ecoponto_id'];
    }

    if (!empty($filtros['material_id'])) {
        $sql .= ' AND c.material_id = :material_id';
        $params[':material_id'] = (int) $filtros['material_id'];
    }

    if (!empty($filtros['data_inicio'])) {
        $sql .= ' AND c.data_coleta >= :data_inicio';
        $params[':data_inicio'] = $filtros['data_inicio'];
    }

    if (!empty($filtros['data_fim'])) {
        $sql .= ' AND c.data_coleta <= :data_fim';
        $params[':data_fim'] = $filtros['data_fim'];
    }

    $sql .= ' ORDER BY c.data_coleta DESC, c.id DESC LIMIT ' . (int)$limite . ' OFFSET ' . (int)$offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function obter_coleta_por_id(int $id, ?int $usuario_id_restrito = null): ?array {
    $pdo = obter_pdo();
    $sql = 'SELECT c.id, c.usuario_id, c.ecoponto_id, c.material_id, c.peso_kg, c.data_coleta, c.observacoes, c.ativo, c.created_at, c.updated_at,
                   m.fator_co2_kg, m.fator_agua_litros
            FROM coletas c
            JOIN materiais m ON m.id = c.material_id
            WHERE c.id = :id AND c.ativo = 1';
    $params = [':id' => $id];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :usuario_restrito';
        $params[':usuario_restrito'] = $usuario_id_restrito;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $coleta = $stmt->fetch();
    return $coleta ?: null;
}

function registrar_coleta(int $usuario_id, int $ecoponto_id, int $material_id, float $peso_kg, string $data_coleta, ?string $observacoes): int {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('INSERT INTO coletas (usuario_id, ecoponto_id, material_id, peso_kg, data_coleta, observacoes, ativo)
                           VALUES (:u, :e, :m, :p, :d, :o, 1)');
    $stmt->execute([
        ':u' => $usuario_id,
        ':e' => $ecoponto_id,
        ':m' => $material_id,
        ':p' => $peso_kg,
        ':d' => $data_coleta,
        ':o' => $observacoes ?: null
    ]);
    return (int) $pdo->lastInsertId();
}

function atualizar_coleta(int $id, int $ecoponto_id, int $material_id, float $peso_kg, string $data_coleta, ?string $observacoes, ?int $usuario_id_restrito = null): bool {
    $pdo = obter_pdo();
    $sql = 'UPDATE coletas
            SET ecoponto_id = :e, material_id = :m, peso_kg = :p, data_coleta = :d, observacoes = :o
            WHERE id = :id AND ativo = 1';
    $params = [
        ':e'  => $ecoponto_id,
        ':m'  => $material_id,
        ':p'  => $peso_kg,
        ':d'  => $data_coleta,
        ':o'  => $observacoes ?: null,
        ':id' => $id
    ];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND usuario_id = :usuario_restrito';
        $params[':usuario_restrito'] = $usuario_id_restrito;
    }

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function excluir_logica_coleta(int $id, ?int $usuario_id_restrito = null): bool {
    $pdo = obter_pdo();
    $sql = 'UPDATE coletas SET ativo = 0 WHERE id = :id AND ativo = 1';
    $params = [':id' => $id];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND usuario_id = :usuario_restrito';
        $params[':usuario_restrito'] = $usuario_id_restrito;
    }

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}
