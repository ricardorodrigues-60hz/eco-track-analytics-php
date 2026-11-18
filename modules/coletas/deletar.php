<?php
/**
 * EcoTrack Analytics — Exclusão Lógica de Registro de Coleta (ativo = 0)
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_login();

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $usuario_logado = obter_usuario_logado();
    $eh_admin = ($usuario_logado['perfil'] === 'admin');
    $usuario_restrito = $eh_admin ? null : $usuario_logado['id'];

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    try {
        $sucesso = excluir_logica_coleta($id, $usuario_restrito);
        if ($sucesso) {
            header('Location: /eco-track-analytics-php/modules/coletas/index.php?msg=' . urlencode('Coleta cancelada e inativada com sucesso.'));
        } else {
            header('Location: /eco-track-analytics-php/modules/coletas/index.php?erro=' . urlencode('Não foi possível excluir a coleta (registro inexistente ou permissão insuficiente).'));
        }
    } catch (Exception $e) {
        header('Location: /eco-track-analytics-php/modules/coletas/index.php?erro=' . urlencode('Erro ao processar exclusão da coleta.'));
    }
    exit;
}

header('Location: /eco-track-analytics-php/modules/coletas/index.php');
exit;
