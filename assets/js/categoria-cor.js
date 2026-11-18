/**
 * EcoTrack Analytics — Sincronização do Seletor de Cores CONAMA
 * 
 * Sincroniza o seletor nativo de cor com o campo de texto hexadecimal
 * e atualiza em tempo real o preview da etiqueta da categoria.
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var colorInput = document.getElementById('cor_hex');
        var colorText = document.getElementById('cor_hex_text');
        var colorChip = document.getElementById('cor_preview_chip');

        if (!colorInput || !colorText || !colorChip) {
            return;
        }

        colorInput.addEventListener('input', function() {
            colorText.value = colorInput.value.toUpperCase();
            colorChip.style.backgroundColor = colorInput.value;
        });

        colorText.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(colorText.value)) {
                colorInput.value = colorText.value;
                colorChip.style.backgroundColor = colorText.value;
            }
        });
    });
})();
