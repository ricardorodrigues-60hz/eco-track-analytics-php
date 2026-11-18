<?php
/**
 * EcoTrack Analytics — Listagem de Categorias (Admin)
 */

$titulo_pagina = 'Gerenciamento de Categorias';
require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/header.php';

$categorias = listar_categorias();
$msg_sucesso = $_GET['msg'] ?? '';
$msg_erro = $_GET['erro'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Cadastros Auxiliares</span>
        <h1 class="mb-0" style="font-size:2rem;">Categorias de Recicláveis</h1>
    </div>
    <a href="/eco-track-analytics-php/modules/categorias/form.php" class="btn btn-primary">
        <span>➕</span> Nova Categoria
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
                <th>Nome da Categoria</th>
                <th style="width: 140px;">Cor Padrão</th>
                <th style="width: 160px; text-align: center;">Materiais Vinculados</th>
                <th style="width: 170px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categorias)): ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Nenhuma categoria cadastrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td class="et-tabular fw-bold"><?= $c['id'] ?></td>
                        <td class="fw-semibold">
                            <span class="d-inline-block rounded-circle me-2" style="width:12px; height:12px; background-color:<?= e($c['cor_hex']) ?>; vertical-align:middle; border:1px solid rgba(0,0,0,0.2);"></span>
                            <?= e($c['nome_categoria']) ?>
                        </td>
                        <td class="et-tabular">
                            <code style="color:var(--et-tinta); font-weight:bold; background:rgba(0,0,0,0.05); padding:2px 6px;"><?= e($c['cor_hex']) ?></code>
                        </td>
                        <td class="text-center et-tabular fw-bold">
                            <?= (int) $c['total_materiais'] ?>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="/eco-track-analytics-php/modules/categorias/form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar Categoria" style="padding:0.25rem 0.5rem; min-height:auto;">
                                    ✏️ Editar
                                </a>

                                <form method="POST" action="/eco-track-analytics-php/modules/categorias/deletar.php" data-confirm="Atenção: A exclusão só será permitida caso nenhum material utilize esta categoria. Deseja prosseguir?" style="display:inline;">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Categoria" style="padding:0.25rem 0.5rem; min-height:auto;">
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
