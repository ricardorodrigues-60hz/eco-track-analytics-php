<?php
/**
 * EcoTrack Analytics — Listagem de Usuários (Apenas Admin)
 */

$titulo_pagina = 'Gerenciamento de Usuários';
require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/header.php';

$usuarios = listar_usuarios();
$msg_sucesso = $_GET['msg'] ?? '';
$msg_erro = $_GET['erro'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">Cadastros Auxiliares</span>
        <h1 class="mb-0" style="font-size:2rem;">Usuários do Sistema</h1>
    </div>
    <a href="/eco-track-analytics-php/modules/usuarios/form.php" class="btn btn-primary">
        <span>➕</span> Novo Usuário
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
                <th>Nome Completo</th>
                <th>E-mail de Acesso</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Cadastro</th>
                <th style="width: 170px; text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Nenhum usuário cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td class="et-tabular fw-bold"><?= $u['id'] ?></td>
                        <td class="fw-semibold"><?= e($u['nome']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php if ($u['perfil'] === 'admin'): ?>
                                <span class="badge" style="background-color: var(--et-tinta); color:#fff; font-size:0.75rem;">Administrador</span>
                            <?php else: ?>
                                <span class="badge" style="background-color: var(--et-cinza); color:#fff; font-size:0.75rem;">Coletor</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)$u['ativo'] === 1): ?>
                                <span class="badge bg-success" style="font-size:0.75rem;">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary" style="font-size:0.75rem;">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="et-tabular" style="font-size:0.85rem; color:var(--et-cinza);">
                            <?= formatar_data_br($u['created_at']) ?>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="/eco-track-analytics-php/modules/usuarios/form.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar Usuário" style="padding:0.25rem 0.5rem; min-height:auto;">
                                    ✏️ Editar
                                </a>
                                
                                <form method="POST" action="/eco-track-analytics-php/modules/usuarios/deletar.php" data-confirm="Confirma a alteração do status deste usuário?" style="display:inline;">
                                    <?= campo_csrf() ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="novo_status" value="<?= (int)$u['ativo'] === 1 ? 0 : 1 ?>">
                                    <?php if ((int)$u['ativo'] === 1): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Inativar Usuário" style="padding:0.25rem 0.5rem; min-height:auto;">
                                            ⏸️ Inativar
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Reativar Usuário" style="padding:0.25rem 0.5rem; min-height:auto;">
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
