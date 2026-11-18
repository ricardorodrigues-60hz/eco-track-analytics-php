<?php
/**
 * EcoTrack Analytics — Disparador do Relatório em PDF: Balanço de Impacto Ambiental
 */

require_once __DIR__ . '/../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim    = $_GET['data_fim'] ?? date('Y-m-d');

if (!validar_data_formato($data_inicio)) $data_inicio = '2025-11-01';
if (!validar_data_formato($data_fim))    $data_fim    = '2026-10-31';

$pdo = obter_pdo();
$sql = 'SELECT m.id, m.nome_material, m.fator_co2_kg, m.fator_agua_litros, m.fonte_referencia,
               cat.nome_categoria, cat.cor_hex,
               COALESCE(SUM(c.peso_kg), 0) AS total_peso_kg,
               COALESCE(SUM(c.peso_kg * m.fator_co2_kg), 0) AS total_co2_kg,
               COALESCE(SUM(c.peso_kg * m.fator_agua_litros), 0) AS total_agua_litros
        FROM materiais m
        JOIN categorias_materiais cat ON cat.id = m.categoria_id
        LEFT JOIN coletas c ON c.material_id = m.id AND c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim
        GROUP BY m.id, m.nome_material, m.fator_co2_kg, m.fator_agua_litros, m.fonte_referencia, cat.nome_categoria, cat.cor_hex
        ORDER BY total_peso_kg DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
$impacto_materiais = $stmt->fetchAll();

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ob_start();
include __DIR__ . '/templates/content_impacto.php';
$html = ob_get_clean();

if (class_exists('Spipu\Html2Pdf\Html2Pdf')) {
    try {
        $pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'pt');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($html);
        $pdf->output('balanco-impacto-ambiental-' . date('Ymd-His') . '.pdf');
        exit;
    } catch (\Exception $e) {
        die('Erro na geração do PDF: ' . $e->getMessage());
    }
} else {
    echo $html;
}
