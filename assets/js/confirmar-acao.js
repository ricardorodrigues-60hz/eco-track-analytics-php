/**
 * EcoTrack Analytics — Confirmação de Ações e Exclusões
 * 
 * Intercepta submissões de formulários que possuam o atributo data-confirm
 * e solicita confirmação nativa do usuário antes de enviar a requisição.
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(evento) {
            var form = evento.target;
            if (!form || form.nodeName !== 'FORM') {
                return;
            }

            var mensagem = form.getAttribute('data-confirm');
            if (!mensagem) {
                // Verifica se o botão disparador possuía data-confirm
                var botaoAtivo = document.activeElement;
                if (botaoAtivo && botaoAtivo.hasAttribute('data-confirm')) {
                    mensagem = botaoAtivo.getAttribute('data-confirm');
                }
            }

            if (mensagem) {
                var confirmado = window.confirm(mensagem);
                if (!confirmado) {
                    evento.preventDefault();
                    evento.stopPropagation();
                }
            }
        });
    });
})();
