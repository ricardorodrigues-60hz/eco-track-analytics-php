<?php
/**
 * EcoTrack Analytics — Listagem Geral e Histórico de Coletas
 * 
 * Regra de Perfil:
 * - Administrador: Visualiza todas as coletas de todos os operadores e unidades.
 * - Coletor: Visualiza estritamente as suas próprias pesagens (WHERE usuario_id = :sessao).
 * Filtro permanente: ativo = 1 (exclusão lógica).
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

$titulo_pagina = $eh_admin ? 'Todas as Coletas Registradas' : 'Minhas Coletas Realizadas';

// Filtros GET
$filtros = [
    'ecoponto_id' => isset($_GET['ecoponto_id']) && $_GET['ecoponto_id'] !== '' ? (int)$_GET['ecoponto_id'] : null,
    'material_id' => isset($_GET['material_id']) && $_GET['material_id'] !== '' ? (int)$_GET['material_id'] : null,
    'data_inicio' => isset($_GET['data_inicio']) && trim($_GET['data_inicio']) !== '' ? trim($_GET['data_inicio']) : null,
    'data_fim'    => isset($_GET['data_fim']) && trim($_GET['data_fim']) !== '' ? trim($_GET['data_fim']) : null,
];

// Validação simples de datas de filtro
if ($filtros['data_inicio'] && !validar_data_formato($filtros['data_inicio'])) {
    $filtros['data_inicio'] = null;
}
if ($filtros['data_fim'] && !validar_data_formato($filtros['data_fim'])) {
    $filtros['data_fim'] = null;
}

// Paginação
$itens_por_pagina = 15;
$pagina_atual_num = max(1, (int)($_GET['p'] ?? 1));
$total_registros = contar_coletas($filtros, $usuario_restrito);
$total_paginas = max(1, (int)ceil($total_registros / $itens_por_pagina));
if ($pagina_atual_num > $total_paginas) {
    $pagina_atual_num = $total_paginas;
}
$offset = ($pagina_atual_num - 1) * $itens_por_pagina;

$coletas = listar_coletas($filtros, $usuario_restrito, $itens_por_pagina, $offset);
$ecopontos = listar_ecopontos();
$materiais = listar_materiais();

$msg_sucesso = $_GET['msg'] ?? '';
$msg_erro = $_GET['erro'] ?? '';

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Operações & Histórico</span>
        <h1 class="mb-0" style="font-size:2rem;"><?= $eh_admin ? 'Todas as Coletas Registradas' : 'Minhas Coletas Realizadas' ?></h1>
    </div>
    <a href="/eco-track-analytics-php/modules/coletas/form.php" class="btn btn-primary">
        <span>⚖️</span> Registrar Nova Pesagem
    </a>
</div>

<?php if ($msg_sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($msg_sucesso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if ($msg_erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($msg_erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<!-- FILTROS DE CONSULTA -->
<div class="card p-3 mb-4" style="background-color:var(--et-ticket); border:2px solid var(--et-linha); border-radius:0;">
    <form method="GET" action="" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="f_ecoponto" class="form-label mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Ecoponto</label>
            <select class="form-select form-select-sm" id="f_ecoponto" name="ecoponto_id">
                <option value="">Todos os Ecopontos</option>
                <?php foreach ($ecopontos as $eco): ?>
                    <option value="<?= $eco['id'] ?>" <?= $filtros['ecoponto_id'] === (int)$eco['id'] ? 'selected' : '' ?>>
                        <?= e($eco['nome_unidade']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="f_material" class="form-label mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Material</label>
            <select class="form-select form-select-sm" id="f_material" name="material_id">
                <option value="">Todos os Materiais</option>
                <?php foreach ($materiais as $mat): ?>
                    <option value="<?= $mat['id'] ?>" <?= $filtros['material_id'] === (int)$mat['id'] ? 'selected' : '' ?>>
                        <?= e($mat['nome_material']) ?> (<?= e($mat['nome_categoria']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="f_inicio" class="form-label mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Data Início</label>
            <input type="date" class="form-control form-control-sm et-tabular" id="f_inicio" name="data_inicio" value="<?= e($filtros['data_inicio'] ?? '') ?>">
        </div>

        <div class="col-md-2">
            <label for="f_fim" class="form-label mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Data Fim</label>
            <input type="date" class="form-control form-control-sm et-tabular" id="f_fim" name="data_fim" value="<?= e($filtros['data_fim'] ?? '') ?>">
        </div>

        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="min-height:31px;">Filtrar</button>
            <a href="/eco-track-analytics-php/modules/coletas/index.php" class="btn btn-sm btn-outline-secondary" title="Limpar Filtros" style="min-height:31px;">Limpar</a>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 70px;">ID</th>
                <th style="width: 110px;">Data</th>
                <th>Material</th>
                <th>Unidade Ecoponto</th>
                <th style="text-align: right;">Peso Aferido</th>
                <th style="text-align: right;">CO₂ Evitado</th>
                <?php if ($eh_admin): ?>
                    <th>Operador</th>
                <?php endif; ?>
                <th style="width: 160px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coletas)): ?>
                <tr>
                    <td colspan="<?= $eh_admin ? '8' : '7' ?>" class="text-center py-5">
                        <div class="et-empty-state">
                            <div class="et-empty-state__title">Nenhuma coleta localizada neste período</div>
                            <p class="mb-3">Ajuste os filtros acima ou registre a primeira pesagem agora mesmo.</p>
                            <a href="/eco-track-analytics-php/modules/coletas/form.php" class="btn btn-primary btn-sm">
                                Registrar Coleta
                            </a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($coletas as $c): ?>
                    <?php
                    $impacto = calcular_impacto((float)$c['peso_kg'], (float)$c['fator_co2_kg'], (float)$c['fator_agua_litros']);
                    ?>
                    <tr>
                        <td class="et-tabular fw-bold"><?= $c['id'] ?></td>
                        <td class="et-tabular"><?= formatar_data_br($c['data_coleta']) ?></td>
                        <td>
                            <span class="et-chip me-1" style="background-color: <?= e($c['cor_hex']) ?>; <?= $c['nome_categoria'] === 'Metal' ? 'color:var(--et-tinta);' : '' ?> font-size:0.75rem;">
                                <?= e($c['nome_categoria']) ?>
                            </span>
                            <span class="fw-semibold"><?= e($c['nome_material']) ?></span>
                        </td>
                        <td><?= e($c['ecoponto_nome']) ?></td>
                        <td class="text-end et-tabular fw-bold" style="font-size:1.05rem;">
                            <?= formatar_numero((float)$c['peso_kg'], 3) ?> kg
                        </td>
                        <td class="text-end et-tabular fw-bold" style="color:var(--et-sucesso);">
                            <?= formatar_numero($impacto['co2_kg'], 2) ?> kg
                        </td>
                        <?php if ($eh_admin): ?>
                            <td style="font-size:0.85rem; color:var(--et-cinza);"><?= e($c['usuario_nome']) ?></td>
                        <?php endif; ?>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="/eco-track-analytics-php/modules/coletas/form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar Pesagem" style="padding:0.25rem 0.5rem; min-height:auto;">
                                    ✏️
                                </a>

                                <form method="POST" action="/eco-track-analytics-php/modules/coletas/deletar.php" onsubmit="return confirm('Confirma o cancelamento e exclusão lógica deste ticket de coleta?');" style="display:inline;">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Coleta" style="padding:0.25rem 0.5rem; min-height:auto;">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINAÇÃO -->
<?php if ($total_paginas > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-3 pt-2" style="border-top:1px solid var(--et-linha);">
        <div style="font-size:0.85rem; color:var(--et-cinza);">
            Mostrando <strong><?= count($coletas) ?></strong> de <strong><?= $total_registros ?></strong> registros (Página <?= $pagina_atual_num ?> de <?= $total_paginas ?>)
        </div>
        <nav aria-label="Navegação de Páginas">
            <ul class="pagination pagination-sm mb-0">
                <?php
                // Constrói query string sem o parâmetro 'p'
                $query_params = $_GET;
                unset($query_params['p']);
                $qs = http_build_query($query_params);
                $qs_prefix = $qs ? '?' . $qs . '&p=' : '?p=';
                ?>
                <li class="page-item <?= $pagina_atual_num <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $qs_prefix . ($pagina_atual_num - 1) ?>">Anterior</a>
                </li>
                <?php for ($i = max(1, $pagina_atual_num - 2); $i <= min($total_paginas, $pagina_atual_num + 2); $i++): ?>
                    <li class="page-item <?= $i === $pagina_atual_num ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $qs_prefix . $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagina_atual_num >= $total_paginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $qs_prefix . ($pagina_atual_num + 1) ?>">Próxima</a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
