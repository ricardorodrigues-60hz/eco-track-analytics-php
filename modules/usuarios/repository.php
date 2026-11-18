<?php
/**
 * EcoTrack Analytics — Repositório de Usuários
 * Operações PDO isoladas para a tabela 'usuarios'.
 */

require_once __DIR__ . '/../../config/database.php';

function listar_usuarios(): array {
    $pdo = obter_pdo();
    $stmt = $pdo->query('SELECT id, nome, email, perfil, ativo, created_at FROM usuarios ORDER BY nome ASC');
    return $stmt->fetchAll();
}

function obter_usuario_por_id(int $id): ?array {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('SELECT id, nome, email, perfil, ativo FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $u = $stmt->fetch();
    return $u ?: null;
}

function email_usuario_existe(string $email, ?int $ignorar_id = null): bool {
    $pdo = obter_pdo();
    if ($ignorar_id) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id');
        $stmt->execute([':email' => $email, ':id' => $ignorar_id]);
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $email]);
    }
    return (int) $stmt->fetchColumn() > 0;
}

function criar_usuario(string $nome, string $email, string $senha, string $perfil): int {
    $pdo = obter_pdo();
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, :perfil, 1)');
    $stmt->execute([
        ':nome'   => $nome,
        ':email'  => $email,
        ':senha'  => $hash,
        ':perfil' => $perfil
    ]);
    return (int) $pdo->lastInsertId();
}

function atualizar_usuario(int $id, string $nome, string $email, string $perfil, ?string $nova_senha = null): bool {
    $pdo = obter_pdo();
    if ($nova_senha !== null && trim($nova_senha) !== '') {
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, senha = :senha WHERE id = :id');
        return $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':perfil' => $perfil,
            ':senha'  => $hash,
            ':id'     => $id
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id');
        return $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':perfil' => $perfil,
            ':id'     => $id
        ]);
    }
}

function alternar_status_usuario(int $id, int $novo_status): bool {
    $pdo = obter_pdo();
    $stmt = $pdo->prepare('UPDATE usuarios SET ativo = :ativo WHERE id = :id');
    return $stmt->execute([
        ':ativo' => $novo_status,
        ':id'    => $id
    ]);
}
