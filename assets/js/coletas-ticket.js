/**
 * EcoTrack Analytics — Ticket de Pesagem e Projeção Reativa de Impacto
 * 
 * Calcula instantaneamente no front-end os valores de CO₂ evitado e economia hídrica
 * conforme o operador digita o peso em kg e seleciona o material reciclável.
 * Aciona animação de emissão do ticket ao submeter a pesagem.
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var selectMaterial = document.getElementById('material_id');
        var inputPeso = document.getElementById('peso_kg');
        var displayCo2 = document.getElementById('impacto-co2');
        var displayAgua = document.getElementById('impacto-agua');
        var formColeta = document.getElementById('form-coleta');
        var ticketBox = document.getElementById('ticket-coleta');

        if (!selectMaterial || !inputPeso || !displayCo2 || !displayAgua) {
            return;
        }

        function atualizarCalculoImpacto() {
            var opt = selectMaterial.options[selectMaterial.selectedIndex];
            var peso = parseFloat(inputPeso.value) || 0;

            if (opt && opt.value && peso > 0) {
                var fatorCo2 = parseFloat(opt.getAttribute('data-co2')) || 0;
                var fatorAgua = parseFloat(opt.getAttribute('data-agua')) || 0;

                var totalCo2 = peso * fatorCo2;
                var totalAgua = peso * fatorAgua;

                displayCo2.textContent = totalCo2.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' kg';

                displayAgua.textContent = totalAgua.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' L';
            } else {
                displayCo2.textContent = '0,00 kg';
                displayAgua.textContent = '0,00 L';
            }
        }

        selectMaterial.addEventListener('change', atualizarCalculoImpacto);
        inputPeso.addEventListener('input', atualizarCalculoImpacto);

        if (formColeta && ticketBox) {
            formColeta.addEventListener('submit', function() {
                ticketBox.classList.add('et-ticket--imprimindo');
            });
        }

        // Execução inicial para preenchimento em tela de edição
        atualizarCalculoImpacto();
    });
})();
