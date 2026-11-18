/**
 * EcoTrack Analytics — Animação de Enchimento da Barra de Fardo
 *
 * Anima cada fatia da barra de fardo da largura 0 até o valor final,
 * da esquerda para a direita, uma única vez ao carregar a página.
 * Respeita prefers-reduced-motion: se ativo, aplica as larguras imediatamente.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var fardo = document.getElementById('et-fardo-principal');
        if (!fardo) return;

        var fatias = fardo.querySelectorAll('.et-fardo__fatia');
        if (!fatias.length) return;

        // Verifica preferência do usuário por movimento reduzido
        var semMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (semMovimento) {
            // Aplica larguras diretamente sem animação
            fatias.forEach(function (fatia) {
                fatia.style.width = fatia.getAttribute('data-pct') + '%';
            });
            return;
        }

        // Anima cada fatia com atraso escalonado
        fatias.forEach(function (fatia, indice) {
            var pct = fatia.getAttribute('data-pct');
            setTimeout(function () {
                fatia.style.transition = 'width 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                fatia.style.width = pct + '%';
            }, indice * 80); // 80ms de atraso entre cada fatia
        });
    });
})();
