<?php
/**
 * EcoTrack Analytics — Disparador do Relatório em PDF: Extrato Geral de Coletas
 */

require_once __DIR__ . '/../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../modules/coletas/repository.php';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim    = $_GET['data_fim'] ?? date('Y-m-d');
$ecoponto_id = isset($_GET['ecoponto_id']) && $_GET['ecoponto_id'] !== '' ? (int)$_GET['ecoponto_id'] : null;

if (!validar_data_formato($data_inicio)) $data_inicio = '2025-11-01';
if (!validar_data_formato($data_fim))    $data_fim    = '2026-10-31';

$filtros = [
    'data_inicio' => $data_inicio,
    'data_fim'    => $data_fim,
    'ecoponto_id' => $ecoponto_id
];

$coletas = listar_coletas($filtros, null, 1000, 0);

$ecoponto_nome = null;
if ($ecoponto_id) {
    require_once __DIR__ . '/../modules/ecopontos/repository.php';
    $eco = obter_ecoponto_por_id($ecoponto_id);
    $ecoponto_nome = $eco['nome_unidade'] ?? null;
}

// Carrega o autoload do Composer para carregar o HTML2PDF
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ob_start();
include __DIR__ . '/templates/content_extrato.php';
$html = ob_get_clean();

if (class_exists('Spipu\Html2Pdf\Html2Pdf')) {
    try {
        $pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'pt');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($html);
        $pdf->output('extrato-coletas-' . date('Ymd-His') . '.pdf');
        exit;
    } catch (\Exception $e) {
        die('Erro na geração do PDF: ' . $e->getMessage());
    }
} else {
    // Modo de fallback para visualização em HTML puro caso Composer ainda não tenha baixado
    echo $html;
}
