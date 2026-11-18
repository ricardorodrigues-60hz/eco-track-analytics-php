<?php
/**
 * EcoTrack Analytics — Listagem de Ecopontos (Admin)
 */

$titulo_pagina = 'Gerenciamento de Ecopontos';
require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/header.php';

$ecopontos = listar_ecopontos();
$msg_sucesso = $_GET['msg'] ?? '';
$msg_erro = $_GET['erro'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Cadastros Auxiliares</span>
        <h1 class="mb-0" style="font-size:2rem;">Unidades de Ecoponto</h1>
    </div>
    <a href="/eco-track-analytics-php/modules/ecopontos/form.php" class="btn btn-primary">
        <span>➕</span> Novo Ecoponto
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
                <th style="width: 70px;">ID</th>
                <th>Nome da Unidade</th>
                <th>Endereço Completo</th>
                <th style="text-align: right;">Capacidade Nominal</th>
                <th style="width: 100px; text-align: center;">Status</th>
                <th style="width: 170px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ecopontos)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Nenhum ecoponto cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ecopontos as $e): ?>
                    <tr>
                        <td class="et-tabular fw-bold"><?= $e['id'] ?></td>
                        <td class="fw-semibold"><?= e($e['nome_unidade']) ?></td>
                        <td><?= e($e['endereco']) ?></td>
                        <td class="et-tabular text-end fw-bold">
                            <?= formatar_numero((float)$e['capacidade_toneladas'], 2) ?> t
                            <span style="font-size:0.75rem; color:var(--et-cinza); font-weight:normal;">(<?= formatar_numero((float)$e['capacidade_toneladas'] * 1000, 0) ?> kg)</span>
                        </td>
                        <td class="text-center">
                            <?php if ((int)$e['ativo'] === 1): ?>
                                <span class="badge bg-success" style="font-size:0.75rem;">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary" style="font-size:0.75rem;">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="/eco-track-analytics-php/modules/ecopontos/form.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar Ecoponto" style="padding:0.25rem 0.5rem; min-height:auto;">
                                    ✏️ Editar
                                </a>

                                <form method="POST" action="/eco-track-analytics-php/modules/ecopontos/deletar.php" data-confirm="Confirma a alteração do status operacional deste ecoponto?" style="display:inline;">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                    <input type="hidden" name="novo_status" value="<?= (int)$e['ativo'] === 1 ? 0 : 1 ?>">
                                    <?php if ((int)$e['ativo'] === 1): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Inativar Unidade" style="padding:0.25rem 0.5rem; min-height:auto;">
                                            ⏸️ Inativar
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Reativar Unidade" style="padding:0.25rem 0.5rem; min-height:auto;">
                                            ▶️ Ativar
                                        </button>
                                    <?php endif; ?>
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
