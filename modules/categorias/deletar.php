<?php
/**
 * EcoTrack Analytics — Exclusão de Categoria com Tratamento de Integridade Referencial
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    try {
        excluir_categoria($id);
        header('Location: /eco-track-analytics-php/modules/categorias/index.php?msg=' . urlencode('Categoria excluída com sucesso.'));
    } catch (PDOException $e) {
        // Código 23000 = Violação de integridade referencial (FK RESTRICT)
        if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1451')) {
            $erro = 'Esta categoria não pode ser excluída porque já existem materiais cadastrados vinculados a ela. Remova ou altere os materiais primeiro.';
        } else {
            $erro = 'Erro interno ao tentar excluir a categoria: ' . $e->getMessage();
        }
        header('Location: /eco-track-analytics-php/modules/categorias/index.php?erro=' . urlencode($erro));
    } catch (Exception $e) {
        header('Location: /eco-track-analytics-php/modules/categorias/index.php?erro=' . urlencode('Erro ao processar exclusão da categoria.'));
    }
    exit;
}

header('Location: /eco-track-analytics-php/modules/categorias/index.php');
exit;
