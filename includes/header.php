<?php
/**
 * EcoTrack Analytics — Cabeçalho e Casca Visual da Aplicação
 * Renderiza: Metas, Fonts (Barlow Condensed e Public Sans), Bootstrap 5 CSS, ecotrack.css,
 * Menu lateral fixo (Desktop) e Barra superior (Mobile).
 */

if (!defined('APP_URL')) {
    if (file_exists(__DIR__ . '/../config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
    } else {
        require_once __DIR__ . '/../config/config.example.php';
    }
}

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/funcoes.php';

$usuario_logado = obter_usuario_logado();
$pagina_atual = $_SERVER['SCRIPT_NAME'] ?? '';
$titulo_pagina = $titulo_pagina ?? 'EcoTrack Analytics';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title><?= e($titulo_pagina) ?> — EcoTrack Analytics</title>
    
    <!-- Google Fonts: Barlow Condensed (600, 700) e Public Sans (400, 500, 600) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Design System Mineral EcoTrack -->
    <link rel="stylesheet" href="/eco-track-analytics-php/assets/css/ecotrack.css">
    
    <!-- Google Charts Loader -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>

<div class="et-app-layout">
    <?php if ($usuario_logado): ?>
        <!-- Menu Lateral Fixo (Desktop) -->
        <aside class="et-sidebar" aria-label="Navegação Principal">
            <a href="/eco-track-analytics-php/dashboard/index.php" class="et-sidebar__brand">
                <div>
                    <div class="et-sidebar__logo-text">EcoTrack</div>
                    <span class="et-sidebar__tag">ANALYTICS</span>
                </div>
            </a>

            <nav class="et-nav-list">
                <a href="/eco-track-analytics-php/dashboard/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/dashboard/') ? 'active' : '' ?>">
                    <span>📊</span> Painel <?= $usuario_logado['perfil'] === 'admin' ? 'Geral' : 'Operacional' ?>
                </a>

                <a href="/eco-track-analytics-php/modules/coletas/form.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/coletas/form.php') ? 'active' : '' ?>">
                    <span>⚖️</span> Registrar Coleta
                </a>

                <a href="/eco-track-analytics-php/modules/coletas/index.php" class="et-nav-item__link <?= (str_contains($pagina_atual, '/coletas/index.php') || (str_contains($pagina_atual, '/coletas/') && !str_contains($pagina_atual, 'form.php'))) ? 'active' : '' ?>">
                    <span>📋</span> <?= $usuario_logado['perfil'] === 'admin' ? 'Todas as Coletas' : 'Minhas Coletas' ?>
                </a>

                <?php if ($usuario_logado['perfil'] === 'admin'): ?>
                    <div style="font-size:0.75rem; text-transform:uppercase; color:var(--et-linha); padding:0.75rem 0.85rem 0.25rem; font-weight:700; letter-spacing:0.05em;">Cadastros Auxiliares</div>
                    
                    <a href="/eco-track-analytics-php/modules/ecopontos/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/ecopontos/') ? 'active' : '' ?>">
                        <span>📍</span> Ecopontos
                    </a>

                    <a href="/eco-track-analytics-php/modules/categorias/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/categorias/') ? 'active' : '' ?>">
                        <span>🏷️</span> Categorias
                    </a>

                    <a href="/eco-track-analytics-php/modules/materiais/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/materiais/') ? 'active' : '' ?>">
                        <span>♻️</span> Materiais & Fatores
                    </a>

                    <a href="/eco-track-analytics-php/modules/usuarios/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/usuarios/') ? 'active' : '' ?>">
                        <span>👥</span> Usuários
                    </a>

                    <div style="font-size:0.75rem; text-transform:uppercase; color:var(--et-linha); padding:0.75rem 0.85rem 0.25rem; font-weight:700; letter-spacing:0.05em;">Auditoria e Emissão</div>

                    <a href="/eco-track-analytics-php/reports/index.php" class="et-nav-item__link <?= str_contains($pagina_atual, '/reports/') ? 'active' : '' ?>">
                        <span>📄</span> Relatórios em PDF
                    </a>
                <?php endif; ?>
            </nav>

            <div class="et-sidebar__user">
                <div class="et-sidebar__user-info">
                    <span class="et-sidebar__user-name"><?= e($usuario_logado['nome']) ?></span>
                    <span class="et-sidebar__user-role"><?= $usuario_logado['perfil'] === 'admin' ? 'Administrador' : 'Coletor' ?></span>
                </div>
                <a href="/eco-track-analytics-php/auth/logout.php" title="Encerrar Sessão" style="color:var(--et-linha); text-decoration:none; padding:0.35rem; font-size:1.1rem;">
                    🚪
                </a>
            </div>
        </aside>

        <!-- Barra Superior (Mobile) -->
        <header class="et-mobile-header">
            <span class="et-sidebar__logo-text" style="font-size:1.25rem;">EcoTrack</span>
            <div style="font-size:0.85rem; color:var(--et-linha);">
                <?= e($usuario_logado['nome']) ?> (<?= $usuario_logado['perfil'] === 'admin' ? 'Admin' : 'Coletor' ?>)
            </div>
        </header>
    <?php endif; ?>

    <!-- Conteúdo Principal -->
    <main class="et-main-content" id="main-content">
