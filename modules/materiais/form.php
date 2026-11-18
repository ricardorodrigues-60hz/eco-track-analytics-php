<?php
/**
 * EcoTrack Analytics — Formulário de Criação e Edição de Material (Admin)
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../categorias/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$material_edicao = null;
$titulo_pagina = $id ? 'Editar Material' : 'Novo Material';

if ($id) {
    $material_edicao = obter_material_por_id($id);
    if (!$material_edicao) {
        header('Location: /eco-track-analytics-php/modules/materiais/index.php?erro=' . urlencode('Material não encontrado.'));
        exit;
    }
}

$categorias = listar_categorias();
$erro = '';
$nome          = $material_edicao['nome_material'] ?? '';
$categoria_id  = isset($material_edicao['categoria_id']) ? (int)$material_edicao['categoria_id'] : 0;
$fator_co2     = isset($material_edicao['fator_co2_kg']) ? (float)$material_edicao['fator_co2_kg'] : '';
$fator_agua    = isset($material_edicao['fator_agua_litros']) ? (float)$material_edicao['fator_agua_litros'] : '';
$fonte         = $material_edicao['fonte_referencia'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $nome         = trim($_POST['nome_material'] ?? '');
    $categoria_id = (int) ($_POST['categoria_id'] ?? 0);
    $co2_raw      = str_replace(',', '.', trim($_POST['fator_co2_kg'] ?? ''));
    $agua_raw     = str_replace(',', '.', trim($_POST['fator_agua_litros'] ?? ''));
    $fonte        = trim($_POST['fonte_referencia'] ?? '');

    $fator_co2  = is_numeric($co2_raw) ? (float) $co2_raw : -1.0;
    $fator_agua = is_numeric($agua_raw) ? (float) $agua_raw : -1.0;

    if ($nome === '') {
        $erro = 'O nome do material é obrigatório.';
    } elseif ($categoria_id <= 0) {
        $erro = 'Selecione uma categoria válida para o material.';
    } elseif ($fator_co2 < 0.0) {
        $erro = 'O fator de CO₂ evitado deve ser um número maior ou igual a zero.';
    } elseif ($fator_agua < 0.0) {
        $erro = 'O fator de água economizada deve ser um número maior ou igual a zero.';
    } else {
        try {
            if ($id) {
                atualizar_material($id, $categoria_id, $nome, $fator_co2, $fator_agua, $fonte ?: null);
                header('Location: /eco-track-analytics-php/modules/materiais/index.php?msg=' . urlencode('Material atualizado com sucesso.'));
            } else {
                criar_material($categoria_id, $nome, $fator_co2, $fator_agua, $fonte ?: null);
                header('Location: /eco-track-analytics-php/modules/materiais/index.php?msg=' . urlencode('Material cadastrado com sucesso.'));
            }
            exit;
        } catch (Exception $e) {
            $erro = 'Erro ao salvar material no banco de dados.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <a href="/eco-track-analytics-php/modules/materiais/index.php" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:var(--et-cinza);">
        ← Voltar para Lista de Materiais
    </a>
    <h1 class="mt-2 mb-0" style="font-size:2rem;"><?= $id ? 'Editar Material' : 'Novo Material' ?></h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="et-ticket">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger mb-4" role="alert">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= campo_csrf() ?>

                <div class="row mb-3">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <label for="nome_material" class="form-label fw-semibold">Nome do Material *</label>
                        <input type="text" class="form-control" id="nome_material" name="nome_material" value="<?= e($nome) ?>" required maxlength="120" placeholder="Ex: PET, Papelão, Vidro, Alumínio">
                    </div>
                    <div class="col-md-5">
                        <label for="categoria_id" class="form-label fw-semibold">Categoria CONAMA *</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $categoria_id === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['nome_categoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="fator_co2_kg" class="form-label fw-semibold">CO₂ Evitado (kg por 1 kg reciclado) *</label>
                        <div class="input-group">
                            <input type="number" step="0.0001" min="0" class="form-control et-tabular" id="fator_co2_kg" name="fator_co2_kg" value="<?= e((string)$fator_co2) ?>" required placeholder="Ex: 1.1100">
                            <span class="input-group-text" style="background:var(--et-linha); font-size:0.85rem;">kg CO₂e/kg</span>
                        </div>
                        <div class="form-text" style="color:var(--et-cinza);">Base de cálculo para os rankings e balanços de emissão.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="fator_agua_litros" class="form-label fw-semibold">Água Poupada (litros por 1 kg reciclado) *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control et-tabular" id="fator_agua_litros" name="fator_agua_litros" value="<?= e((string)$fator_agua) ?>" required placeholder="Ex: 2.50">
                            <span class="input-group-text" style="background:var(--et-linha); font-size:0.85rem;">litros/kg</span>
                        </div>
                        <div class="form-text" style="color:var(--et-cinza);">Base de cálculo para a economia de recursos hídricos.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="fonte_referencia" class="form-label fw-semibold">Fonte Bibliográfica Citável (Referência Acadêmica) *</label>
                    <textarea class="form-control" id="fonte_referencia" name="fonte_referencia" rows="3" placeholder="Ex: EPA (2023). Waste Reduction Model (WARM) v16; WWF Brasil (2008)..."><?= e($fonte) ?></textarea>
                    <div class="form-text" style="color:var(--et-cinza);">Indique o autor, ano, relatório e metodologia de conversão utilizada.</div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top:1px dashed var(--et-linha);">
                    <a href="/eco-track-analytics-php/modules/materiais/index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $id ? 'Salvar Alterações' : 'Cadastrar Material' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
