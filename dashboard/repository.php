<?php
/**
 * EcoTrack Analytics — Repositório de Consultas Agregadas do Dashboard
 * 
 * Todas as consultas filtram estritamente 'coletas.ativo = 1'.
 * Se $usuario_id_restrito for informado, filtra as coletas do operador especificado.
 */

require_once __DIR__ . '/../config/database.php';

function obter_totais_dashboard(string $data_inicio, string $data_fim, ?int $usuario_id_restrito = null): array {
    $pdo = obter_pdo();
    $sql = 'SELECT COUNT(c.id) AS total_coletas,
                   COALESCE(SUM(c.peso_kg), 0) AS total_peso_kg,
                   COALESCE(SUM(c.peso_kg * m.fator_co2_kg), 0) AS total_co2_kg,
                   COALESCE(SUM(c.peso_kg * m.fator_agua_litros), 0) AS total_agua_litros
            FROM coletas c
            JOIN materiais m ON m.id = c.material_id
            WHERE c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim';
    $params = [':inicio' => $data_inicio, ':fim' => $data_fim];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :u_id';
        $params[':u_id'] = $usuario_id_restrito;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $r = $stmt->fetch();

    return [
        'total_coletas'     => (int) ($r['total_coletas'] ?? 0),
        'total_peso_kg'     => (float) ($r['total_peso_kg'] ?? 0.0),
        'total_co2_kg'      => (float) ($r['total_co2_kg'] ?? 0.0),
        'total_agua_litros' => (float) ($r['total_agua_litros'] ?? 0.0),
    ];
}

/**
 * 1. Proporção por Categoria (Gráfico de Rosca & Barra de Fardo)
 */
function obter_dados_grafico_categorias(string $data_inicio, string $data_fim, ?int $usuario_id_restrito = null): array {
    $pdo = obter_pdo();
    $sql = 'SELECT cat.id, cat.nome_categoria, cat.cor_hex,
                   COALESCE(SUM(c.peso_kg), 0) AS total_peso_kg
            FROM categorias_materiais cat
            JOIN materiais m ON m.categoria_id = cat.id
            JOIN coletas c ON c.material_id = m.id AND c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim';
    $params = [':inicio' => $data_inicio, ':fim' => $data_fim];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :u_id';
        $params[':u_id'] = $usuario_id_restrito;
    }

    $sql .= ' GROUP BY cat.id, cat.nome_categoria, cat.cor_hex
              HAVING total_peso_kg > 0
              ORDER BY total_peso_kg DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $linhas = $stmt->fetchAll();

    $resultado = [];
    foreach ($linhas as $linha) {
        $resultado[] = [
            'id'             => (int) $linha['id'],
            'nome_categoria' => (string) $linha['nome_categoria'],
            'cor_hex'        => (string) $linha['cor_hex'],
            'total_peso_kg'  => (float) $linha['total_peso_kg'],
        ];
    }
    return $resultado;
}

/**
 * 2. Evolução Mensal de Peso em kg (Gráfico de Linha)
 */
function obter_dados_grafico_evolucao_mensal(string $data_inicio, string $data_fim, ?int $usuario_id_restrito = null): array {
    $pdo = obter_pdo();
    $sql = "SELECT DATE_FORMAT(c.data_coleta, '%Y-%m') AS mes_ano,
                   COALESCE(SUM(c.peso_kg), 0) AS total_peso_kg
            FROM coletas c
            WHERE c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim";
    $params = [':inicio' => $data_inicio, ':fim' => $data_fim];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :u_id';
        $params[':u_id'] = $usuario_id_restrito;
    }

    $sql .= " GROUP BY DATE_FORMAT(c.data_coleta, '%Y-%m')
              ORDER BY mes_ano ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $linhas = $stmt->fetchAll();

    $meses_pt = [
        '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr',
        '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
        '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'
    ];

    $resultado = [];
    foreach ($linhas as $l) {
        $partes = explode('-', $l['mes_ano']);
        $rotulo = ($meses_pt[$partes[1]] ?? $partes[1]) . '/' . substr($partes[0], 2);
        $resultado[] = [
            'periodo_iso' => $l['mes_ano'],
            'rotulo'      => $rotulo,
            'total_peso'  => (float) $l['total_peso_kg']
        ];
    }
    return $resultado;
}

/**
 * 3. Volume Consolidado por Ecoponto (Gráfico de Coluna)
 */
function obter_dados_grafico_ecopontos(string $data_inicio, string $data_fim, ?int $usuario_id_restrito = null): array {
    $pdo = obter_pdo();
    $sql = 'SELECT e.id, e.nome_unidade,
                   COALESCE(SUM(c.peso_kg), 0) AS total_peso_kg
            FROM ecopontos e
            LEFT JOIN coletas c ON c.ecoponto_id = e.id AND c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim';
    $params = [':inicio' => $data_inicio, ':fim' => $data_fim];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :u_id';
        $params[':u_id'] = $usuario_id_restrito;
    }

    $sql .= ' GROUP BY e.id, e.nome_unidade
              ORDER BY total_peso_kg DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $linhas = $stmt->fetchAll();

    $resultado = [];
    foreach ($linhas as $l) {
        $resultado[] = [
            'ecoponto'   => (string) $l['nome_unidade'],
            'total_peso' => (float) $l['total_peso_kg']
        ];
    }
    return $resultado;
}

/**
 * 4. Ranking de CO₂ Evitado por Tipo de Material (Gráfico de Barra Horizontal)
 */
function obter_dados_grafico_ranking_co2(string $data_inicio, string $data_fim, ?int $usuario_id_restrito = null): array {
    $pdo = obter_pdo();
    $sql = 'SELECT m.nome_material, cat.cor_hex,
                   COALESCE(SUM(c.peso_kg * m.fator_co2_kg), 0) AS total_co2_kg
            FROM materiais m
            JOIN categorias_materiais cat ON cat.id = m.categoria_id
            JOIN coletas c ON c.material_id = m.id AND c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim';
    $params = [':inicio' => $data_inicio, ':fim' => $data_fim];

    if ($usuario_id_restrito !== null) {
        $sql .= ' AND c.usuario_id = :u_id';
        $params[':u_id'] = $usuario_id_restrito;
    }

    $sql .= ' GROUP BY m.id, m.nome_material, cat.cor_hex
              HAVING total_co2_kg > 0
              ORDER BY total_co2_kg DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $linhas = $stmt->fetchAll();

    $resultado = [];
    foreach ($linhas as $l) {
        $resultado[] = [
            'material' => (string) $l['nome_material'],
            'cor_hex'  => (string) $l['cor_hex'],
            'total_co2'=> (float) $l['total_co2_kg']
        ];
    }
    return $resultado;
}
