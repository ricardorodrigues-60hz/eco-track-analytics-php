# EcoTrack Analytics — Plano de Design Front-End

**Versão:** 2.0 (Fase 11 — Identidade Visual Refinada)
**Metodologia:** Planejamento baseado em *frontend-design* e ancorado na identidade industrial e funcional de ecopontos e reciclagem.

---

## 1. Briefing e Identidade

- **Contexto Operacional:** Ecopontos municipais e cooperativas de triagem de recicláveis.
- **Público Primário (Coletor/Operador):** Usuários frequentemente utilizando dispositivos móveis em pé, sob luz solar direta, com luvas ou dedos sujos, precisando registrar pesagens rapidamente com confiança.
- **Público Secundário (Administrador/Gestor):** Análise em desktop de volumes consolidados, taxas de ocupação e emissão de relatórios ambientais auditáveis para comprovação de metas públicas e corporativas.
- **Conceito Visual:** **Balança Industrial e Etiqueta de Pesagem**, distanciando-se expressamente do visual genérico de painéis SaaS administrativos (sem dashboards roxos/azuis com degradês, sem kits de cards arredondados idênticos com sombras difusas).

---

## 2. Sistema de Cores e Tokens

A cor de base é intencionalmente sóbria e mineral (concreto, chapas metálicas e tinta de sinalização de piso). **A cor viva pertence estritamente ao material reciclável**, padronizada conforme a Resolução CONAMA 275/2001 (código nacional de coleta seletiva).

### Tokens de Superfície e Estrutura
- `--et-concreto: #E6E8E5` — Fundo geral da aplicação (concreto usinado neutro, reduz reflexo sob luz forte).
- `--et-tinta: #1B2A30` — Texto primário, títulos e elementos de maior hierarquia/ação primária (tinta escura epóxi de piso industrial).
- `--et-cinza: #5C6B70` — Texto secundário, metadados e legendas (cinza chapa de aço).
- `--et-linha: #C9CECB` — Linhas divisórias, marcações e bordas funcionais (linha de giz/marcação).
- `--et-ticket: #FAFAF7` — Superfície contrastante do ticket de pesagem (papel térmico fosco).

### Tokens Cromáticos de Materiais (Resolução CONAMA 275/2001)
- `--et-papel: #005EB8` — Categoria Papel/Papelão (Azul institucional).
- `--et-plastico: #CC0000` — Categoria Plástico/PET (Vermelho sinalização).
- `--et-vidro: #007A33` — Categoria Vidro (Verde garrafa).
- `--et-metal: #FFC000` — Categoria Metal/Alumínio (Amarelo alerta). **Uso restrito ao chip/badge da categoria Metal.**

> **Acessibilidade cromática:** Cores de categorias nunca são usadas isoladamente como único indicador. Sempre acompanhadas de rótulo de texto explicativo em alto contraste (`contrast-ratio` ≥ 4.5:1).

---

## 3. Tipografia

Duas famílias distintas via Google Fonts com fallbacks robustos do sistema:

1. **Display & Números:** `Barlow Condensed` (pesos 600 e 700).
   - Uso: Títulos principais, código de identificação, valores numéricos de pesagem.
   - Propriedade obrigatória: `font-variant-numeric: tabular-nums` para alinhamento uniforme de dígitos.
   - Inspiração: Placas de sinalização e estêncil de balanças rodoviárias/industriais.
2. **Corpo & Formulários:** `Public Sans` (pesos 400 e 600).
   - Uso: Textos corridos, tabelas, rótulos de campos e instruções.
   - Limite de leitura: `max-width: 75ch` para prevenir fadiga visual.

**Fase 11 — Ajuste:** Rótulos de seção passam a usar caixa normal com hierarquia por tamanho e peso, sem `text-transform: uppercase` + `letter-spacing` excessivo. `h1`-`h4` mantêm Barlow Condensed (fonte condensada industrial já é distintiva o suficiente).

---

## 4. Arquitetura de Layout

- **Desktop (≥ 992px):**
  - Navegação lateral fixa à esquerda (260px).
  - Painel principal à direita com alinhamento visual à esquerda e divisores por filetes.
- **Mobile (< 992px):**
  - Barra de navegação inferior fixa (*Bottom Navigation Bar*) com alvos de toque ≥ 44px.
  - Ação central em destaque: **"Registrar Coleta"** acessível com o polegar.
  - Responsividade estrita garantida a partir de 360px de largura de tela.

### Fase 11 — Layout do Dashboard (proporcional, não mais 2×2 idênticos)

```
[ Fita de Pesagem — 4 métricas em cupom horizontal — 12 colunas ]
─── 01 Composição do fardo ──────────────────────────────────────
[ Barra do fardo — 12 colunas ]
─── 02 Evolução mensal ────────── 03 Ranking de CO₂ ────────────
[ LineChart — 8 colunas ]         [ BarChart — 4 colunas ]
─── 04 Distribuição por categoria  05 Volume por ecoponto ───────
[ PieChart — 5 colunas ]           [ ColumnChart — 7 colunas ]
```

Separadores: `<hr class="et-secao-hr">` + `<h2 class="et-secao-titulo">` numerado em vez de caixas com borda idênticas.

---

## 5. Elementos Memoráveis

### 5.1 Ticket de Pesagem Digital (Tela de Registro de Coleta)
- Componente central estilizado como tíquete de balança física.
- Borda picotada inferior implementada puramente em CSS (`radial-gradient` recortado).
- Número de peso com tipografia agigantada (`3.5rem`).
- **Feedback instantâneo:** Conforme o operador digita o peso em kg e seleciona o material, o sistema exibe imediatamente os cálculos estimados de CO₂ evitado e água economizada antes do salvamento.
- **Fase 10:** Cor do ticket e botão de confirmar acompanha `--cor-material` (cor da categoria, via JS).

### 5.2 Barra de Proporção em "Fardo" (Dashboard Analítico)
- No topo do dashboard, uma barra segmentada contínua representando o fardo prensado consolidado do período selecionado.
- Cada segmento tem a largura percentual proporcional e a cor CONAMA correspondente da categoria.
- **Fase 11:** Barra anima da esquerda para a direita ao carregar (uma vez, `prefers-reduced-motion` desativa).
- **Fase 11:** Hover em cada fatia da barra destaca a fatia correspondente na rosca (interação cruzada via `google.visualization.events`).

### 5.3 Fita de Pesagem / KPI Cupom (Dashboard — Fase 11)
- Substitui os 4 cards idênticos por um único painel horizontal em formato de cupom/receipt industrial.
- Linhas com dot-leaders até o valor: `Cargas registradas ........ 974`.
- Números em Barlow Condensed, `tabular-nums`, tamanho destacado.
- Carimbo discreto "provisório" se `FATORES_PROVISORIOS === true` (apenas nos valores de impacto).

### 5.4 Ícones de Traço SVG (Fase 11)
- Conjunto único em `includes/icones.php`: função `icone($nome, $classe)` retorna SVG inline.
- Stroke-based, `currentColor`, espessura 1.5px uniforme.
- Substitui todos os emojis de navegação (sidebar + bottom nav). Zero nova dependência.

---

## 6. Microinterações e Movimento

- **Ticket sendo impresso:** Ao submeter coleta, `.et-ticket--imprimindo` → `clip-path: inset()`.
- **Fase 11 — Barra do fardo:** `@keyframes et-fardo-encher` anima `width` de 0 → valor final, uma vez. `prefers-reduced-motion` desativa.
- **Regra:** No máximo um momento de movimento por tela. Nunca animações em loop.
- **Respeito a Preferências:** `@media (prefers-reduced-motion: reduce)` desativa qualquer transição temporal.

---

## 7. Detalhe de Assinatura (máx. 2 — Fase 11)

1. **Filete picotado** sob o `<h1>` de cada página (`border-bottom: 2px dashed var(--et-linha)`).
2. **Carimbo "provisório"** em KPIs de impacto quando `FATORES_PROVISORIOS = true` (texto em diagonal, opacity baixa, Barlow Condensed, sem bloquear leitura).

---

## 8. Autocrítica — Fase 11

### Comparação com "painel genérico de reciclagem"

| Padrão genérico | EcoTrack Fase 11 |
|---|---|
| 4 cards iguais com sombra, borda colorida lateral e ícone circular | Fita de pesagem/cupom com dot-leaders — remetendo a balança rodoviária |
| Grade 2×2 simétrica de caixas idênticas | Proporções assimétricas por relevância (8+4, 5+7) |
| Rótulos `UPPERCASE ESPAÇADO` em cada seção | Títulos numerados (`01 Composição do fardo`) em caixa normal, Barlow Condensed |
| Ícones emojis inconsistentes | SVGs de traço uniformes, `currentColor`, mesma espessura |
| Nenhum movimento ou movimento excessivo | Um único movimento útil: o fardo "enchendo" ao carregar |
| Verde/azul padrão como cor de acento | Paleta 100% mineral; cor viva reservada à CONAMA |

### O que foi removido
- **Badge "Operador: X" no cabeçalho do ticket** (já aparece no mobile-header e na sidebar — redundante).
- **Borda lateral colorida nos cards de CO₂ e Água** (substituída pelo cupom sem borda colorida; a cor já vem do chip de categoria e do fardo).
- **`text-transform: uppercase + letter-spacing` nos rótulos de seção** do dashboard (substituído por hierarquia de tamanho/peso).

### O que poderia parecer ainda genérico — e por que não é
- A fita de pesagem pode lembrar um "summary card horizontal" genérico. Diferença: dot-leaders, Barlow Condensed em tamanho grande, fundo `--et-ticket` (papel térmico), nenhuma sombra, nenhuma borda colorida lateral.
- Os separadores numerados podem lembrar "section headers" comuns. Diferença: filete `--et-linha` (giz de marcação de piso), numeração em Barlow Condensed muito grande como marcação industrial.
