<?php
/**
 * EcoTrack Analytics — Formulário de Registro de Coleta (Ticket de Pesagem Digital)
 * 
 * Elemento Memorável do Sistema:
 * - Aparência de tíquete de balança rodoviária/industrial com borda picotada CSS
 * - Tipografia de peso em destaque imediato (3.5rem Barlow Condensed)
 * - Cálculo instantâneo em JavaScript do impacto (CO₂ evitado e água economizada)
 * - Animação de impressão ao submeter o formulário
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_login();

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../materiais/repository.php';
require_once __DIR__ . '/../ecopontos/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$usuario_logado = obter_usuario_logado();
$eh_admin = ($usuario_logado['perfil'] === 'admin');
$usuario_restrito = $eh_admin ? null : $usuario_logado['id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$coleta_edicao = null;
$titulo_pagina = $id ? 'Editar Coleta' : 'Registrar Coleta';

if ($id) {
    $coleta_edicao = obter_coleta_por_id($id, $usuario_restrito);
    if (!$coleta_edicao) {
        header('Location: /eco-track-analytics-php/modules/coletas/index.php?erro=' . urlencode('Registro de coleta não encontrado ou sem permissão de alteração.'));
        exit;
    }
}

$materiais = listar_materiais();
$ecopontos = listar_ecopontos(true); // Apenas ecopontos ativos

$erro = '';
$ecoponto_id = isset($coleta_edicao['ecoponto_id']) ? (int)$coleta_edicao['ecoponto_id'] : 0;
$material_id = isset($coleta_edicao['material_id']) ? (int)$coleta_edicao['material_id'] : 0;
$peso_kg     = isset($coleta_edicao['peso_kg']) ? (float)$coleta_edicao['peso_kg'] : '';
$data_coleta = $coleta_edicao['data_coleta'] ?? date('Y-m-d');
$observacoes = $coleta_edicao['observacoes'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $ecoponto_id = (int) ($_POST['ecoponto_id'] ?? 0);
    $material_id = (int) ($_POST['material_id'] ?? 0);
    $peso_raw    = str_replace(',', '.', trim($_POST['peso_kg'] ?? ''));
    $peso_kg     = is_numeric($peso_raw) ? (float) $peso_raw : 0.0;
    $data_coleta = trim($_POST['data_coleta'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($ecoponto_id <= 0) {
        $erro = 'Selecione a unidade de ecoponto onde foi realizada a pesagem.';
    } elseif ($material_id <= 0) {
        $erro = 'Selecione o tipo de material reciclável coletado.';
    } elseif ($peso_kg <= 0.0) {
        $erro = 'O peso aferido deve ser estritamente maior que zero quilogramas.';
    } elseif (!validar_data_formato($data_coleta, 'Y-m-d')) {
        $erro = 'Informe uma data de pesagem válida no formato AAAA-MM-DD.';
    } else {
        try {
            if ($id) {
                atualizar_coleta($id, $ecoponto_id, $material_id, $peso_kg, $data_coleta, $observacoes, $usuario_restrito);
                header('Location: /eco-track-analytics-php/modules/coletas/index.php?msg=' . urlencode('Pesagem atualizada com sucesso.'));
            } else {
                registrar_coleta($usuario_logado['id'], $ecoponto_id, $material_id, $peso_kg, $data_coleta, $observacoes);
                header('Location: /eco-track-analytics-php/modules/coletas/index.php?msg=' . urlencode('Coleta registrada e ticket emitido com sucesso!'));
            }
            exit;
        } catch (Exception $e) {
            $erro = 'Falha ao salvar a pesagem no banco de dados.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <a href="/eco-track-analytics-php/modules/coletas/index.php" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:var(--et-cinza);">
        ← Voltar para Histórico de Coletas
    </a>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <h1 class="mb-0" style="font-size:2rem;"><?= $id ? 'Editar Comprovante de Coleta' : 'Terminal de Pesagem & Registro' ?></h1>
        <span class="badge" style="background-color:var(--et-tinta); color:#fff; font-size:0.8rem; padding:0.4rem 0.75rem;">
            Operador: <?= e($usuario_logado['nome']) ?>
        </span>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger mb-4" role="alert">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <!-- TICKET DE PESAGEM DIGITAL -->
        <div class="et-ticket" id="ticket-coleta">
            <div class="et-ticket__header">
                <div>
                    <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-cinza); letter-spacing:0.08em;">Comprovante de Recepção e Triagem</span>
                    <h2 style="font-size:1.5rem; margin:0; color:var(--et-tinta);">Ticket de Pesagem #<?= $id ? $id : 'NOVO' ?></h2>
                </div>
                <div class="text-end" style="font-size:0.8rem; color:var(--et-cinza);">
                    <div>FATEC Botucatu</div>
                    <div class="et-tabular fw-bold"><?= date('d/m/Y H:i') ?></div>
                </div>
            </div>

            <form method="POST" action="" id="form-coleta">
                <?= campo_csrf() ?>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="ecoponto_id" class="form-label fw-semibold" style="font-size:0.85rem; text-transform:uppercase;">Unidade Ecoponto *</label>
                        <select class="form-select" id="ecoponto_id" name="ecoponto_id" required>
                            <option value="">Selecione o Ecoponto...</option>
                            <?php foreach ($ecopontos as $eco): ?>
                                <option value="<?= $eco['id'] ?>" <?= $ecoponto_id === (int)$eco['id'] ? 'selected' : '' ?>>
                                    <?= e($eco['nome_unidade']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="data_coleta" class="form-label fw-semibold" style="font-size:0.85rem; text-transform:uppercase;">Data da Operação *</label>
                        <input type="date" class="form-control et-tabular" id="data_coleta" name="data_coleta" value="<?= e($data_coleta) ?>" required max="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="material_id" class="form-label fw-semibold" style="font-size:0.85rem; text-transform:uppercase;">Material Reciclável Triado *</label>
                    <select class="form-select" id="material_id" name="material_id" required style="font-size:1.05rem;">
                        <option value="">Selecione o Material...</option>
                        <?php foreach ($materiais as $mat): ?>
                            <option value="<?= $mat['id'] ?>"
                                    data-categoria="<?= e($mat['nome_categoria']) ?>"
                                    data-cor="<?= e($mat['cor_hex']) ?>"
                                    data-co2="<?= (float)$mat['fator_co2_kg'] ?>"
                                    data-agua="<?= (float)$mat['fator_agua_litros'] ?>"
                                    <?= $material_id === (int)$mat['id'] ? 'selected' : '' ?>>
                                <?= e($mat['nome_material']) ?> — Categoria <?= e($mat['nome_categoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- CAMPO DE PESO COM DESTAQUE DE BALANÇA -->
                <div class="p-3 mb-3" style="background-color:rgba(27,42,48,0.04); border:2px solid var(--et-tinta);">
                    <label for="peso_kg" class="form-label fw-bold mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--et-tinta);">
                        ⚖️ Peso Líquido Apurado na Balança (em Quilogramas) *
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" step="0.001" min="0.001" class="form-control et-ticket__num-peso" id="peso_kg" name="peso_kg" value="<?= e((string)$peso_kg) ?>" required placeholder="0.000" style="height:70px;">
                        <span style="font-family:'Barlow Condensed', sans-serif; font-size:2.2rem; font-weight:700; color:var(--et-cinza);">KG</span>
                    </div>
                </div>

                <!-- PAINEL DE IMPACTO ESTIMADO AO VIVO -->
                <div class="et-ticket__impacto-box" id="painel-impacto">
                    <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-cinza); letter-spacing:0.05em; margin-bottom:0.5rem;">
                        Impacto Ambiental Mitigado por Esta Carga (Projeção Instantânea)
                    </div>
                    <div class="row text-center text-md-start">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <span style="font-size:0.82rem; color:var(--et-cinza); display:block;">🌱 CO₂ Equivalente Evitado</span>
                            <span class="et-num fw-bold" id="impacto-co2" style="font-size:1.75rem; color:var(--et-sucesso); line-height:1.1;">0,00 kg</span>
                        </div>
                        <div class="col-sm-6">
                            <span style="font-size:0.82rem; color:var(--et-cinza); display:block;">💧 Recursos Hídricos Economizados</span>
                            <span class="et-num fw-bold" id="impacto-agua" style="font-size:1.75rem; color:var(--et-papel); line-height:1.1;">0,00 L</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 mb-4">
                    <label for="observacoes" class="form-label fw-semibold" style="font-size:0.85rem; text-transform:uppercase;">Observações Operacionais (Opcional)</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Ex: Lote enfardado; entrega por cooperativa parceira; conferido no turno matutino."><?= e($observacoes) ?></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top:1px dashed var(--et-linha);">
                    <a href="/eco-track-analytics-php/modules/coletas/index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="font-size:1.05rem; letter-spacing:0.04em;">
                        <?= $id ? 'Salvar Alterações' : 'Emitir e Registrar Coleta' ?>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php 
$scripts_especificos = ['assets/js/coletas-ticket.js'];
require_once __DIR__ . '/../../includes/footer.php'; 
?>
