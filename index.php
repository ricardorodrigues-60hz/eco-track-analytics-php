<?php
/**
 * EcoTrack Analytics — Ponto de Entrada Principal
 * Redireciona o visitante para a tela de autenticação ou para o painel se já autenticado.
 */

session_start();

if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard/index.php');
} else {
    header('Location: auth/login.php');
}
exit;
