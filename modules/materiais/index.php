<?php
/**
 * EcoTrack Analytics — Listagem de Materiais e Fatores Ambientais (Admin)
 */

$titulo_pagina = 'Gerenciamento de Materiais';
require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/header.php';

$materiais = listar_materiais();
$msg_sucesso = $_GET['msg'] ?? '';
$msg_erro = $_GET['erro'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Cadastros Auxiliares</span>
        <h1 class="mb-0" style="font-size:2rem;">Materiais & Fatores Ambientais</h1>
    </div>
    <a href="/eco-track-analytics-php/modules/materiais/form.php" class="btn btn-primary">
        <span>➕</span> Novo Material
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

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Material</th>
                <th>Categoria CONAMA</th>
                <th style="text-align: right;">Fator CO₂ Evitado</th>
                <th style="text-align: right;">Fator Água Poupada</th>
                <th>Fonte Bibliográfica Referenciada</th>
                <th style="width: 170px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($materiais)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Nenhum material cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($materiais as $m): ?>
                    <tr>
                        <td class="et-tabular fw-bold"><?= $m['id'] ?></td>
                        <td class="fw-semibold" style="font-size:1.05rem;"><?= e($m['nome_material']) ?></td>
                        <td>
                            <span class="et-chip" style="background-color: <?= e($m['cor_hex']) ?>; <?= $m['nome_categoria'] === 'Metal' ? 'color:var(--et-tinta);' : '' ?>">
                                <?= e($m['nome_categoria']) ?>
                            </span>
                        </td>
                        <td class="text-end et-tabular fw-bold" style="color:var(--et-sucesso);">
                            <?= formatar_numero((float)$m['fator_co2_kg'], 4) ?> <small class="text-muted fw-normal">kg/kg</small>
                        </td>
                        <td class="text-end et-tabular fw-bold" style="color:var(--et-papel);">
                            <?= formatar_numero((float)$m['fator_agua_litros'], 2) ?> <small class="text-muted fw-normal">L/kg</small>
                        </td>
                        <td style="font-size:0.82rem; color:var(--et-cinza); max-width:280px;" title="<?= e($m['fonte_referencia']) ?>">
                            <?= e(mb_strimwidth($m['fonte_referencia'] ?? 'Não informada', 0, 75, '...')) ?>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="/eco-track-analytics-php/modules/materiais/form.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar Material" style="padding:0.25rem 0.5rem; min-height:auto;">
                                    ✏️ Editar
                                </a>

                                <form method="POST" action="/eco-track-analytics-php/modules/materiais/deletar.php" onsubmit="return confirm('Confirma a exclusão deste material? Se existirem coletas registradas com ele, a operação será impedida.');" style="display:inline;">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Material" style="padding:0.25rem 0.5rem; min-height:auto;">
                                        🗑️ Excluir
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
