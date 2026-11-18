<?php
/**
 * EcoTrack Analytics — Disparador do Relatório em PDF: Auditoria de Lançamentos por Operador
 */

require_once __DIR__ . '/../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funcoes.php';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim    = $_GET['data_fim'] ?? date('Y-m-d');
$usuario_id  = isset($_GET['usuario_id']) && $_GET['usuario_id'] !== '' ? (int)$_GET['usuario_id'] : null;

if (!validar_data_formato($data_inicio)) $data_inicio = '2025-11-01';
if (!validar_data_formato($data_fim))    $data_fim    = '2026-10-31';

$pdo = obter_pdo();
$sql = 'SELECT c.id, c.peso_kg, c.data_coleta, c.created_at, c.updated_at,
               u.nome AS usuario_nome,
               e.nome_unidade AS ecoponto_nome,
               m.nome_material
        FROM coletas c
        JOIN usuarios u ON u.id = c.usuario_id
        JOIN ecopontos e ON e.id = c.ecoponto_id
        JOIN materiais m ON m.id = c.material_id
        WHERE c.ativo = 1 AND c.data_coleta BETWEEN :inicio AND :fim';
$params = [':inicio' => $data_inicio, ':fim' => $data_fim];

if ($usuario_id !== null) {
    $sql .= ' AND c.usuario_id = :u_id';
    $params[':u_id'] = $usuario_id;
}

$sql .= ' ORDER BY c.updated_at DESC, c.id DESC LIMIT 500';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$coletas_auditoria = $stmt->fetchAll();

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ob_start();
include __DIR__ . '/templates/content_auditoria.php';
$html = ob_get_clean();

if (class_exists('Spipu\Html2Pdf\Html2Pdf')) {
    try {
        $pdf = new \Spipu\Html2Pdf\Html2Pdf('L', 'A4', 'pt');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($html);
        $pdf->output('auditoria-coletas-' . date('Ymd-His') . '.pdf');
        exit;
    } catch (\Exception $e) {
        die('Erro na geração do PDF: ' . $e->getMessage());
    }
} else {
    echo $html;
}
