<?php
/**
 * EcoTrack Analytics — Disparador do Relatório em PDF: Produtividade e Ocupação por Ecoponto
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
$sql = 'SELECT e.id, e.nome_unidade, e.endereco, e.capacidade_toneladas,
               COALESCE(SUM(c.peso_kg), 0) AS volume_kg,
               COUNT(c.id) AS total_coletas
        FROM ecopontos e
        LEFT JOIN coletas c ON c.ecoponto_id = e.id AND c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim
        GROUP BY e.id, e.nome_unidade, e.endereco, e.capacidade_toneladas
        ORDER BY volume_kg DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
$ecopontos_dados = $stmt->fetchAll();

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ob_start();
include __DIR__ . '/templates/content_ecopontos.php';
$html = ob_get_clean();

if (class_exists('Spipu\Html2Pdf\Html2Pdf')) {
    try {
        $pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'pt');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($html);
        $pdf->output('ocupacao-ecopontos-' . date('Ymd-His') . '.pdf');
        exit;
    } catch (\Exception $e) {
        die('Erro na geração do PDF: ' . $e->getMessage());
    }
} else {
    echo $html;
}
