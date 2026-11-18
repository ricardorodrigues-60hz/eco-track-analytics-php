<?php
/**
 * EcoTrack Analytics — Biblioteca de Ícones SVG de Traço
 *
 * Conjunto único de ícones inline, stroke-based, currentColor, espessura 1.5px.
 * Uso: <?= icone('painel') ?> ou <?= icone('coletar', 'et-icone--lg') ?>
 * Sem dependência externa. Zero nova biblioteca.
 */

function icone(string $nome, string $classe = ''): string
{
    $cls = 'et-icone' . ($classe ? ' ' . $classe : '');
    $attr = sprintf(
        'width="20" height="20" viewBox="0 0 24 24" fill="none" '
        . 'stroke="currentColor" stroke-width="1.5" stroke-linecap="round" '
        . 'stroke-linejoin="round" aria-hidden="true" class="%s"',
        htmlspecialchars($cls, ENT_QUOTES)
    );

    $paths = [
        // Painel / Dashboard — grade de 4 quadrantes
        'painel' =>
            '<rect x="3" y="3" width="7" height="7"/>'
            . '<rect x="14" y="3" width="7" height="7"/>'
            . '<rect x="14" y="14" width="7" height="7"/>'
            . '<rect x="3" y="14" width="7" height="7"/>',

        // Coletar / Registrar coleta — balança de braços
        'coletar' =>
            '<path d="M12 3v18"/>'
            . '<path d="M5 7l7-4 7 4"/>'
            . '<path d="M5 7c0 3.314 3.134 6 7 6s7-2.686 7-6"/>'
            . '<path d="M2 21h20"/>',

        // Histórico / Coletas — prancheta com lista
        'historico' =>
            '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>'
            . '<rect x="9" y="3" width="6" height="4" rx="1"/>'
            . '<path d="M9 12h6"/>'
            . '<path d="M9 16h4"/>',

        // Relatórios / PDF — documento com seta para baixo
        'relatorios' =>
            '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>'
            . '<polyline points="14 2 14 8 20 8"/>'
            . '<path d="M12 12v6"/>'
            . '<path d="M9 15l3 3 3-3"/>',

        // Ecopontos — alfinete de localização
        'ecopontos' =>
            '<path d="M12 2C8.134 2 5 5.134 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7z"/>'
            . '<circle cx="12" cy="9" r="2.5"/>',

        // Categorias — etiqueta/tag
        'categorias' =>
            '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>'
            . '<line x1="7" y1="7" x2="7.01" y2="7"/>',

        // Materiais / Reciclagem — setas em triângulo
        'materiais' =>
            '<polyline points="1 4 1 10 7 10"/>'
            . '<polyline points="23 20 23 14 17 14"/>'
            . '<path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15"/>',

        // Usuários — pessoa com ombros
        'usuarios' =>
            '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>'
            . '<circle cx="12" cy="7" r="4"/>',

        // Sair / Logout — porta com seta
        'sair' =>
            '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>'
            . '<polyline points="16 17 21 12 16 7"/>'
            . '<line x1="21" y1="12" x2="9" y2="12"/>',

        // Conta / Usuário logado — círculo com silhueta
        'conta' =>
            '<circle cx="12" cy="8" r="4"/>'
            . '<path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>',

        // Balança (ícone alternativo para coleta)
        'balanca' =>
            '<path d="M12 3v18"/>'
            . '<path d="M5 7c0 3.314 3.134 6 7 6s7-2.686 7-6"/>'
            . '<path d="M5 7l3.5-2m8 2-3.5-2"/>'
            . '<path d="M2 21h20"/>',
    ];

    if (!isset($paths[$nome])) {
        // Ícone de fallback: interrogação
        $corpo = '<circle cx="12" cy="12" r="10"/>'
               . '<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>'
               . '<line x1="12" y1="17" x2="12.01" y2="17"/>';
    } else {
        $corpo = $paths[$nome];
    }

    return "<svg {$attr}>{$corpo}</svg>";
}
