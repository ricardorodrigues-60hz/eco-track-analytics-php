<?php
/**
 * EcoTrack Analytics — Formulário de Criação e Edição de Ecoponto (Admin)
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$ecoponto_edicao = null;
$titulo_pagina = $id ? 'Editar Ecoponto' : 'Novo Ecoponto';

if ($id) {
    $ecoponto_edicao = obter_ecoponto_por_id($id);
    if (!$ecoponto_edicao) {
        header('Location: /eco-track-analytics-php/modules/ecopontos/index.php?erro=' . urlencode('Ecoponto não encontrado.'));
        exit;
    }
}

$erro = '';
$nome       = $ecoponto_edicao['nome_unidade'] ?? '';
$endereco   = $ecoponto_edicao['endereco'] ?? '';
$capacidade = isset($ecoponto_edicao['capacidade_toneladas']) ? (float)$ecoponto_edicao['capacidade_toneladas'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $nome       = trim($_POST['nome_unidade'] ?? '');
    $endereco   = trim($_POST['endereco'] ?? '');
    $cap_raw    = str_replace(',', '.', trim($_POST['capacidade_toneladas'] ?? ''));
    $capacidade = is_numeric($cap_raw) ? (float) $cap_raw : 0.0;

    if ($nome === '' || $endereco === '') {
        $erro = 'O nome da unidade e o endereço são obrigatórios.';
    } elseif ($capacidade <= 0.0) {
        $erro = 'A capacidade nominal deve ser um valor positivo em toneladas.';
    } else {
        try {
            if ($id) {
                atualizar_ecoponto($id, $nome, $endereco, $capacidade);
                header('Location: /eco-track-analytics-php/modules/ecopontos/index.php?msg=' . urlencode('Ecoponto atualizado com sucesso.'));
            } else {
                criar_ecoponto($nome, $endereco, $capacidade);
                header('Location: /eco-track-analytics-php/modules/ecopontos/index.php?msg=' . urlencode('Ecoponto cadastrado com sucesso.'));
            }
            exit;
        } catch (Exception $e) {
            $erro = 'Erro ao salvar os dados do ecoponto no banco de dados.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <a href="/eco-track-analytics-php/modules/ecopontos/index.php" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:var(--et-cinza);">
        ← Voltar para Lista de Ecopontos
    </a>
    <h1 class="mt-2 mb-0" style="font-size:2rem;"><?= $id ? 'Editar Ecoponto' : 'Novo Ecoponto' ?></h1>
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

                <div class="mb-3">
                    <label for="nome_unidade" class="form-label fw-semibold">Nome da Unidade / Ponto de Coleta *</label>
                    <input type="text" class="form-control" id="nome_unidade" name="nome_unidade" value="<?= e($nome) ?>" required maxlength="120" placeholder="Ex: Ecoponto Central">
                </div>

                <div class="mb-3">
                    <label for="endereco" class="form-label fw-semibold">Endereço Completo *</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?= e($endereco) ?>" required maxlength="255" placeholder="Rua, número, bairro e referência">
                </div>

                <div class="mb-4">
                    <label for="capacidade_toneladas" class="form-label fw-semibold">Capacidade Operacional Máxima (em Toneladas) *</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0.1" class="form-control et-tabular" id="capacidade_toneladas" name="capacidade_toneladas" value="<?= e((string)$capacidade) ?>" required placeholder="Ex: 5.00">
                        <span class="input-group-text" style="background:var(--et-linha); font-weight:600;">toneladas</span>
                    </div>
                    <div class="form-text" style="color:var(--et-cinza);">Utilizado para apurar a taxa percentual de ocupação nos relatórios gerenciais.</div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top:1px dashed var(--et-linha);">
                    <a href="/eco-track-analytics-php/modules/ecopontos/index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $id ? 'Salvar Alterações' : 'Cadastrar Ecoponto' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
