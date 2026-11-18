<?php
/**
 * EcoTrack Analytics — Encerramento Seguro de Sessão (Logout)
 * Invalida todos os dados em memória e revoga os cookies de sessão do cliente.
 */

require_once __DIR__ . '/../includes/auth_check.php';

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Se cookies de sessão foram emitidos, força a expiração no cliente
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona para o login
header('Location: /eco-track-analytics-php/auth/login.php');
exit;
