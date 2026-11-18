<?php
/**
 * EcoTrack Analytics — Central de Emissão de Relatórios em PDF (Admin)
 * 
 * Permite filtrar parâmetros (período, ecoponto, operador) e disparar a geração
 * dos 4 relatórios normativos do sistema.
 */

require_once __DIR__ . '/../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/../modules/ecopontos/repository.php';
require_once __DIR__ . '/../modules/usuarios/repository.php';
require_once __DIR__ . '/../includes/funcoes.php';

$titulo_pagina = 'Emissão de Relatórios Oficiais';
$ecopontos = listar_ecopontos();
$usuarios = listar_usuarios();

// Período padrão: 12 meses do seed (01/11/2025 a 31/10/2026)
$data_inicio_padrao = '2025-11-01';
$data_fim_padrao    = '2026-10-31';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Auditoria & Conformidade</span>
    <h1 class="mb-0" style="font-size:2.2rem;">Relatórios Oficiais em PDF</h1>
    <p class="text-muted mt-1 mb-0" style="font-size:0.95rem;">
        Emissão de extratos, balanços de mitigação ambiental e auditoria de pesagens via biblioteca HTML2PDF.
    </p>
</div>

<div class="row g-4">

    <!-- 1. EXTRATO GERAL DE COLETAS -->
    <div class="col-md-6">
        <div class="et-ticket h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h2 style="font-size:1.35rem; margin:0;">1. Extrato Geral de Coletas</h2>
                    <span style="font-size:1.5rem;">📄</span>
                </div>
                <p style="font-size:0.88rem; color:var(--et-cinza); margin-bottom:1.25rem;">
                    Relação detalhada cronológica de todas as pesagens ativas realizadas, agrupando data, tipo de material, ecoponto de origem, peso líquido e responsável pelo lançamento.
                </p>
                <form method="GET" action="/eco-track-analytics-php/reports/pdf_extrato.php" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">INÍCIO</label>
                            <input type="date" name="data_inicio" class="form-control form-control-sm et-tabular" value="<?= $data_inicio_padrao ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">FIM</label>
                            <input type="date" name="data_fim" class="form-control form-control-sm et-tabular" value="<?= $data_fim_padrao ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.75rem; font-weight:700;">ECOPONTO (OPCIONAL)</label>
                        <select name="ecoponto_id" class="form-select form-select-sm">
                            <option value="">Todas as unidades</option>
                            <?php foreach ($ecopontos as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= e($e['nome_unidade']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm">
                        Gerar Extrato em PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. BALANÇO CONSOLIDADO DE IMPACTO AMBIENTAL -->
    <div class="col-md-6">
        <div class="et-ticket h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h2 style="font-size:1.35rem; margin:0;">2. Balanço de Impacto Ambiental</h2>
                    <span style="font-size:1.5rem;">🌱</span>
                </div>
                <p style="font-size:0.88rem; color:var(--et-cinza); margin-bottom:1.25rem;">
                    Relatório consolidado demonstrando o montante de CO₂ evitado e recursos hídricos economizados por tipo de material reciclado, fundamentado nas referências bibliográficas.
                </p>
                <form method="GET" action="/eco-track-analytics-php/reports/pdf_impacto.php" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">INÍCIO</label>
                            <input type="date" name="data_inicio" class="form-control form-control-sm et-tabular" value="<?= $data_inicio_padrao ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">FIM</label>
                            <input type="date" name="data_fim" class="form-control form-control-sm et-tabular" value="<?= $data_fim_padrao ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm" style="margin-top:2.75rem;">
                        Gerar Balanço de Impacto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. PRODUTIVIDADE E OCUPAÇÃO POR ECOPONTO -->
    <div class="col-md-6">
        <div class="et-ticket h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h2 style="font-size:1.35rem; margin:0;">3. Ocupação por Ecoponto</h2>
                    <span style="font-size:1.5rem;">📍</span>
                </div>
                <p style="font-size:0.88rem; color:var(--et-cinza); margin-bottom:1.25rem;">
                    Avaliação operacional de produtividade apurando a <strong>taxa de ocupação (%)</strong> em relação à capacidade nominal (Volume em kg ÷ Capacidade em toneladas × 1000).
                </p>
                <form method="GET" action="/eco-track-analytics-php/reports/pdf_ecopontos.php" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">INÍCIO</label>
                            <input type="date" name="data_inicio" class="form-control form-control-sm et-tabular" value="<?= $data_inicio_padrao ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">FIM</label>
                            <input type="date" name="data_fim" class="form-control form-control-sm et-tabular" value="<?= $data_fim_padrao ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm" style="margin-top:2.75rem;">
                        Gerar Relatório de Ocupação
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. AUDITORIA DE LANÇAMENTOS POR OPERADOR -->
    <div class="col-md-6">
        <div class="et-ticket h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h2 style="font-size:1.35rem; margin:0;">4. Auditoria de Lançamentos</h2>
                    <span style="font-size:1.5rem;">🔍</span>
                </div>
                <p style="font-size:0.88rem; color:var(--et-cinza); margin-bottom:1.25rem;">
                    Rastreabilidade completa de integridade: exibe data original de criação (<code>created_at</code>) e data de última modificação (<code>updated_at</code>) por operador.
                </p>
                <form method="GET" action="/eco-track-analytics-php/reports/pdf_auditoria.php" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">INÍCIO</label>
                            <input type="date" name="data_inicio" class="form-control form-control-sm et-tabular" value="<?= $data_inicio_padrao ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:0.75rem; font-weight:700;">FIM</label>
                            <input type="date" name="data_fim" class="form-control form-control-sm et-tabular" value="<?= $data_fim_padrao ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.75rem; font-weight:700;">OPERADOR (OPCIONAL)</label>
                        <select name="usuario_id" class="form-select form-select-sm">
                            <option value="">Todos os operadores</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= e($u['nome']) ?> (<?= $u['perfil'] === 'admin' ? 'Admin' : 'Coletor' ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm">
                        Gerar Trilha de Auditoria
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
