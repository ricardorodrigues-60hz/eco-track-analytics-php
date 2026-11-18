<?php
/**
 * EcoTrack Analytics — Inativação / Ativação de Usuário
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $novo_status = isset($_POST['novo_status']) ? (int) $_POST['novo_status'] : 0;

    // Regra de segurança: não inativar a si próprio
    if ($id === (int) $_SESSION['usuario_id'] && $novo_status === 0) {
        header('Location: /eco-track-analytics-php/modules/usuarios/index.php?erro=' . urlencode('Operação negada: você não pode inativar sua própria conta ativa.'));
        exit;
    }

    try {
        alternar_status_usuario($id, $novo_status);
        $texto = $novo_status === 1 ? 'Usuário ativado com sucesso.' : 'Usuário inativado com sucesso.';
        header('Location: /eco-track-analytics-php/modules/usuarios/index.php?msg=' . urlencode($texto));
    } catch (Exception $e) {
        header('Location: /eco-track-analytics-php/modules/usuarios/index.php?erro=' . urlencode('Erro ao atualizar o status do usuário.'));
    }
    exit;
}

header('Location: /eco-track-analytics-php/modules/usuarios/index.php');
exit;
