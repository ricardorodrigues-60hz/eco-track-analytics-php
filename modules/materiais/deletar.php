<?php
/**
 * EcoTrack Analytics — Exclusão de Material com Tratamento de Integridade Referencial
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    try {
        excluir_material($id);
        header('Location: /eco-track-analytics-php/modules/materiais/index.php?msg=' . urlencode('Material excluído com sucesso.'));
    } catch (PDOException $e) {
        // Código 23000 = Violação de integridade referencial (FK RESTRICT em coletas)
        if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1451')) {
            $erro = 'Este material não pode ser excluído porque já existem pesagens e coletas vinculadas a ele no histórico do sistema.';
        } else {
            $erro = 'Erro ao tentar excluir o material: ' . $e->getMessage();
        }
        header('Location: /eco-track-analytics-php/modules/materiais/index.php?erro=' . urlencode($erro));
    } catch (Exception $e) {
        header('Location: /eco-track-analytics-php/modules/materiais/index.php?erro=' . urlencode('Erro ao processar exclusão do material.'));
    }
    exit;
}

header('Location: /eco-track-analytics-php/modules/materiais/index.php');
exit;
