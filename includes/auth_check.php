<?php
/**
 * EcoTrack Analytics — Controle de Acesso e Autenticação (RBAC)
 * Garante a verificação estrita de privilégios e integridade de sessão no servidor.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configurações rígidas de segurança para cookies de sessão
    $seguro = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $seguro,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

/**
 * Verifica se há um usuário autenticado na sessão.
 * 
 * @return bool
 */
function esta_logado(): bool {
    return !empty($_SESSION['usuario_id']) && !empty($_SESSION['usuario_perfil']);
}

/**
 * Retorna os dados resumidos do usuário conectado.
 * 
 * @return array{id: int, nome: string, email: string, perfil: string}|null
 */
function obter_usuario_logado(): ?array {
    if (!esta_logado()) {
        return null;
    }

    return [
        'id'     => (int) $_SESSION['usuario_id'],
        'nome'   => (string) $_SESSION['usuario_nome'],
        'email'  => (string) $_SESSION['usuario_email'],
        'perfil' => (string) $_SESSION['usuario_perfil'],
    ];
}

/**
 * Exige que o visitante esteja autenticado. Se não estiver, redireciona para o login.
 * 
 * @return void
 */
function exigir_login(): void {
    if (!esta_logado()) {
        $origem = $_SERVER['REQUEST_URI'] ?? '';
        $redirect = !empty($origem) ? '?redir=' . urlencode($origem) : '';
        header('Location: /eco-track-analytics-php/auth/login.php' . $redirect);
        exit;
    }
}

/**
 * Exige que o usuário autenticado possua um dos perfis autorizados.
 * Se não possuir, interrompe com HTTP 403 (Proibido).
 * 
 * @param array<string> $perfis_permitidos Lista de perfis ('admin', 'coletor')
 * @return void
 */
function exigir_perfil(array $perfis_permitidos): void {
    exigir_login();

    $perfil_atual = $_SESSION['usuario_perfil'] ?? '';

    if (!in_array($perfil_atual, $perfis_permitidos, true)) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 — Acesso Restrito | EcoTrack Analytics</title>
            <link rel="stylesheet" href="/eco-track-analytics-php/assets/css/ecotrack.css">
        </head>
        <body style="display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--et-concreto); margin:0; font-family:'Public Sans', sans-serif;">
            <div style="background:var(--et-ticket); padding:2.5rem; border:2px solid var(--et-linha); max-width:480px; text-align:center;">
                <h1 style="font-family:'Barlow Condensed', sans-serif; font-size:2.5rem; color:var(--et-tinta); margin:0 0 1rem 0;">Acesso Não Autorizado</h1>
                <p style="color:var(--et-cinza); line-height:1.5; margin-bottom:2rem;">Seu perfil de usuário (<strong><?= htmlspecialchars($perfil_atual === 'admin' ? 'Administrador' : 'Coletor', ENT_QUOTES, 'UTF-8') ?></strong>) não possui permissão para acessar este recurso administrativo.</p>
                <a href="/eco-track-analytics-php/dashboard/index.php" style="display:inline-block; background:var(--et-tinta); color:#fff; text-decoration:none; padding:0.75rem 1.5rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; font-size:0.875rem;">Voltar ao Painel</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
