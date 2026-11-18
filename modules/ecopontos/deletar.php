<?php
/**
 * EcoTrack Analytics — Inativação / Ativação de Ecoponto
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $novo_status = isset($_POST['novo_status']) ? (int) $_POST['novo_status'] : 0;

    try {
        alternar_status_ecoponto($id, $novo_status);
        $texto = $novo_status === 1 ? 'Ecoponto ativado com sucesso.' : 'Ecoponto inativado com sucesso.';
        header('Location: /eco-track-analytics-php/modules/ecopontos/index.php?msg=' . urlencode($texto));
    } catch (Exception $e) {
        header('Location: /eco-track-analytics-php/modules/ecopontos/index.php?erro=' . urlencode('Erro ao atualizar o status do ecoponto.'));
    }
    exit;
}

header('Location: /eco-track-analytics-php/modules/ecopontos/index.php');
exit;
