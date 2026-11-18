<?php
/**
 * EcoTrack Analytics — Formulário de Criação e Edição de Categoria (Admin)
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$categoria_edicao = null;
$titulo_pagina = $id ? 'Editar Categoria' : 'Nova Categoria';

if ($id) {
    $categoria_edicao = obter_categoria_por_id($id);
    if (!$categoria_edicao) {
        header('Location: /eco-track-analytics-php/modules/categorias/index.php?erro=' . urlencode('Categoria não encontrada.'));
        exit;
    }
}

$erro = '';
$nome    = $categoria_edicao['nome_categoria'] ?? '';
$cor_hex = $categoria_edicao['cor_hex'] ?? '#005EB8';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $nome    = trim($_POST['nome_categoria'] ?? '');
    $cor_hex = trim($_POST['cor_hex'] ?? '');

    if ($nome === '') {
        $erro = 'O nome da categoria é obrigatório.';
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $cor_hex)) {
        $erro = 'A cor deve estar no formato hexadecimal válido de 6 dígitos (ex: #005EB8).';
    } elseif (nome_categoria_existe($nome, $id)) {
        $erro = 'Já existe uma categoria cadastrada com este nome.';
    } else {
        try {
            if ($id) {
                atualizar_categoria($id, $nome, $cor_hex);
                header('Location: /eco-track-analytics-php/modules/categorias/index.php?msg=' . urlencode('Categoria atualizada com sucesso.'));
            } else {
                criar_categoria($nome, $cor_hex);
                header('Location: /eco-track-analytics-php/modules/categorias/index.php?msg=' . urlencode('Categoria cadastrada com sucesso.'));
            }
            exit;
        } catch (Exception $e) {
            $erro = 'Erro ao salvar categoria no banco de dados.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <a href="/eco-track-analytics-php/modules/categorias/index.php" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:var(--et-cinza);">
        ← Voltar para Lista de Categorias
    </a>
    <h1 class="mt-2 mb-0" style="font-size:2rem;"><?= $id ? 'Editar Categoria' : 'Nova Categoria' ?></h1>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="et-ticket">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger mb-4" role="alert">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= campo_csrf() ?>

                <div class="mb-3">
                    <label for="nome_categoria" class="form-label fw-semibold">Nome da Categoria *</label>
                    <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" value="<?= e($nome) ?>" required maxlength="80" placeholder="Ex: Plástico, Papel, Vidro, Metal">
                </div>

                <div class="mb-4">
                    <label for="cor_hex" class="form-label fw-semibold">Cor Padrão CONAMA (Gráficos e Indicadores) *</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" class="form-control form-control-color" id="cor_hex" name="cor_hex" value="<?= e($cor_hex) ?>" required style="width: 60px; height: 46px; cursor: pointer; padding: 4px;">
                        <input type="text" class="form-control et-tabular" id="cor_hex_text" value="<?= e($cor_hex) ?>" maxlength="7" style="max-width: 140px; text-transform: uppercase;" placeholder="#000000">
                        <div id="cor_preview_chip" class="et-chip" style="background-color: <?= e($cor_hex) ?>; font-size: 0.9rem; padding: 0.4rem 0.85rem;">
                            Visualização da Etiqueta
                        </div>
                    </div>
                    <div class="form-text" style="color:var(--et-cinza);">Cores padrão CONAMA 275/2001: Azul (#005EB8) Papel, Vermelho (#CC0000) Plástico, Verde (#007A33) Vidro, Amarelo (#FFC000) Metal.</div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top:1px dashed var(--et-linha);">
                    <a href="/eco-track-analytics-php/modules/categorias/index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $id ? 'Salvar Alterações' : 'Criar Categoria' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
$scripts_especificos = ['assets/js/categoria-cor.js'];
require_once __DIR__ . '/../../includes/footer.php'; 
?>
