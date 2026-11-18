Fase 9: separar o JavaScript do código PHP.
Siga o mesmo protocolo das fases anteriores: anuncie o plano em tópicos, peça minha
permissão antes de começar, valide, me dê instruções curtas de teste e só então
proponha o commit.

Escopo:
1. Inventarie todo JS embutido nos .php (blocos <script> com lógica e atributos
   onclick/onchange etc.).
2. Mova a lógica para assets/js/, um arquivo por responsabilidade (por exemplo:
   dashboard.js, coletas-ticket.js, confirmar-acao.js), em IIFE com 'use strict',
   comentários em português e sem variáveis globais.
3. O PHP não gera JavaScript. Dados vão num bloco
   <script type="application/json" id="..."> com json_encode e as flags
   JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT. O JS lê com
   JSON.parse(elemento.textContent).
4. Troque handlers inline por addEventListener e atributos data-*.
5. Carregue os scripts com defer, na ordem correta (loader.js do Google Charts antes
   do dashboard.js), usando BASE_URL (ou um helper asset()) para evitar caminhos
   relativos quebrados nas subpastas.
6. Não passe token CSRF nem dados sensíveis pelo bloco JSON.

Aceite: nenhum erro no console; os 4 gráficos e o cálculo ao vivo do ticket funcionam;
os comandos grep (onclick= e <script sem src) não retornam nada.
Commit sugerido: refactor(js): separa scripts JavaScript do código PHP em assets/js