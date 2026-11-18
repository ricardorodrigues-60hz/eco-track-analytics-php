<?php
/**
 * EcoTrack Analytics — Proteção contra Cross-Site Request Forgery (CSRF)
 * Gera e valida tokens criptográficos vinculados à sessão ativa do usuário.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Obtém ou gera o token CSRF atual da sessão.
 * 
 * @return string
 */
function obter_token_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Retorna o elemento HTML <input type="hidden"> pronto para inserção em formulários POST.
 * 
 * @return string
 */
function campo_csrf(): string {
    $token = htmlspecialchars(obter_token_csrf(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Valida o token recebido no POST contra o token armazenado na sessão.
 * Caso seja inválido ou ausente, interrompe a requisição com status HTTP 403.
 * 
 * @param string|null $token_recebido
 * @return void
 */
function validar_csrf(?string $token_recebido = null): void {
    if ($token_recebido === null) {
        $token_recebido = $_POST['csrf_token'] ?? '';
    }

    $token_sessao = $_SESSION['csrf_token'] ?? '';

    if (empty($token_sessao) || empty($token_recebido) || !hash_equals($token_sessao, $token_recebido)) {
        http_response_code(403);
        die('Erro de segurança: Token CSRF inválido ou expirado. Recarregue a página e tente novamente.');
    }
}
