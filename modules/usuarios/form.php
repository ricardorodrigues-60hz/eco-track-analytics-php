<?php
/**
 * EcoTrack Analytics — Formulário de Criação e Edição de Usuário (Admin)
 */

require_once __DIR__ . '/../../includes/auth_check.php';
exigir_perfil(['admin']);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/funcoes.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$usuario_edicao = null;
$titulo_pagina = $id ? 'Editar Usuário' : 'Novo Usuário';

if ($id) {
    $usuario_edicao = obter_usuario_por_id($id);
    if (!$usuario_edicao) {
        header('Location: /eco-track-analytics-php/modules/usuarios/index.php?erro=' . urlencode('Usuário não encontrado.'));
        exit;
    }
}

$erro = '';
$nome  = $usuario_edicao['nome'] ?? '';
$email = $usuario_edicao['email'] ?? '';
$perfil = $usuario_edicao['perfil'] ?? 'coletor';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $perfil = trim($_POST['perfil'] ?? 'coletor');
    $senha  = $_POST['senha'] ?? '';
    $senha_confirma = $_POST['senha_confirma'] ?? '';

    // Validações
    if ($nome === '' || $email === '') {
        $erro = 'Nome e e-mail são campos de preenchimento obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um endereço de e-mail válido.';
    } elseif (!in_array($perfil, ['admin', 'coletor'], true)) {
        $erro = 'Perfil de acesso inválido.';
    } elseif (email_usuario_existe($email, $id)) {
        $erro = 'Já existe outro usuário cadastrado com este e-mail.';
    } elseif (!$id && empty($senha)) {
        $erro = 'A senha é obrigatória para o cadastro de novos usuários.';
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $erro = 'A senha deve conter no mínimo 6 caracteres.';
    } elseif (!empty($senha) && $senha !== $senha_confirma) {
        $erro = 'A confirmação de senha não confere.';
    } else {
        try {
            if ($id) {
                atualizar_usuario($id, $nome, $email, $perfil, !empty($senha) ? $senha : null);
                header('Location: /eco-track-analytics-php/modules/usuarios/index.php?msg=' . urlencode('Usuário atualizado com sucesso.'));
            } else {
                criar_usuario($nome, $email, $senha, $perfil);
                header('Location: /eco-track-analytics-php/modules/usuarios/index.php?msg=' . urlencode('Usuário cadastrado com sucesso.'));
            }
            exit;
        } catch (Exception $e) {
            $erro = 'Erro ao salvar informações do usuário no banco de dados.';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="mb-4 pb-2" style="border-bottom: 2px solid var(--et-linha);">
    <a href="/eco-track-analytics-php/modules/usuarios/index.php" class="text-decoration-none" style="font-size:0.85rem; font-weight:600; color:var(--et-cinza);">
        ← Voltar para Lista de Usuários
    </a>
    <h1 class="mt-2 mb-0" style="font-size:2rem;"><?= $id ? 'Editar Usuário' : 'Novo Usuário' ?></h1>
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
                    <label for="nome" class="form-label fw-semibold">Nome Completo *</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= e($nome) ?>" required maxlength="120" placeholder="Ex: Maria Silva">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail de Acesso *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" required maxlength="180" placeholder="usuario@ecotrack.local">
                </div>

                <div class="mb-3">
                    <label for="perfil" class="form-label fw-semibold">Perfil de Acesso *</label>
                    <select class="form-select" id="perfil" name="perfil" required>
                        <option value="coletor" <?= $perfil === 'coletor' ? 'selected' : '' ?>>Coletor (Lança pesagens e acessa histórico próprio)</option>
                        <option value="admin" <?= $perfil === 'admin' ? 'selected' : '' ?>>Administrador (Acesso irrestrito ao sistema e relatórios)</option>
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="senha" class="form-label fw-semibold">
                            <?= $id ? 'Nova Senha (deixe em branco para manter)' : 'Senha de Acesso *' ?>
                        </label>
                        <input type="password" class="form-control" id="senha" name="senha" <?= $id ? '' : 'required' ?> minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div class="col-md-6">
                        <label for="senha_confirma" class="form-label fw-semibold">Confirme a Senha</label>
                        <input type="password" class="form-control" id="senha_confirma" name="senha_confirma" <?= $id ? '' : 'required' ?> minlength="6" placeholder="Repita a senha">
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3" style="border-top:1px dashed var(--et-linha);">
                    <a href="/eco-track-analytics-php/modules/usuarios/index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <?= $id ? 'Salvar Alterações' : 'Criar Usuário' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
