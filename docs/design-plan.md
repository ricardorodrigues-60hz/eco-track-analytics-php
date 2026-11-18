# EcoTrack Analytics — Plano de Design Front-End

**Versão:** 1.0 (Fase 0)  
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
- `--et-metal: #FFC000` — Categoria Metal/Alumínio (Amarelo alerta).

> **Acessibilidade cromática:** Cores de categorias nunca são usadas isoladamente como único indicador. Sempre acompanhadas de rótulo de texto explicativo em alto contraste (`contrast-ratio` $\ge 4.5:1$).

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

---

## 4. Arquitetura de Layout

- **Desktop ($\ge 992\text{px}$):**
  - Navegação lateral fixa e recolhível à esquerda (240px de largura).
  - Painel principal à direita com alinhamento visual à esquerda e divisores por filetes rígidos.
- **Mobile ($< 992\text{px}$):**
  - Barra de navegação inferior fixa (*Bottom Navigation Bar*) com alvos de toque generosos ($\ge 48\text{px}$).
  - Ação central em destaque: **"Registrar Coleta"** acessível com o polegar.
  - Responsividade estrita garantida a partir de $360\text{px}$ de largura de tela.

---

## 5. Elementos Memoráveis

### 5.1 Ticket de Pesagem Digital (Tela de Registro de Coleta)
- Componente central estilizado como tíquete de balança física.
- Borda picotada inferior implementada puramente em CSS (`radial-gradient` recortado).
- Número de peso com tipografia agigantada (`3.5rem`).
- **Feedback instantâneo:** Conforme o operador digita o peso em kg e seleciona o material, o sistema exibe imediatamente os cálculos estimados de CO₂ evitado e água economizada antes do salvamento.

### 5.2 Barra de Proporção em "Fardo" (Dashboard Analítico)
- No topo do dashboard, uma barra segmentada contínua representando o fardo prensado consolidado do período selecionado.
- Cada segmento tem a largura percentual proporcional e a cor CONAMA correspondente da categoria.

---

## 6. Microinterações e Movimento

- **Um único momento orquestrado:** Ao submeter o formulário de coleta, o ticket recebe a classe `.et-ticket--imprimindo`, simulando o desenrolar de impressão do comprovante (`clip-path: inset()`).
- **Respeito a Preferências:** Diretiva `@media (prefers-reduced-motion: reduce)` desativa qualquer transição temporal para usuários com sensibilidade vestibular.

---

## 7. Wireframes Textuais de Referência

### 7.1 Desktop: Painel Analítico
```text
┌──────────────┬────────────────────────────────────────────────────────┐
│ EcoTrack     │ Período: [ 01/10/2026 ] até [ 31/10/2026 ] [ Filtrar ] │
│ ──────────── │ ────────────────────────────────────────────────────── │
│ [Dashboard]  │ FARDO DO PERÍODO (Total: 18.420 kg)                    │
│ [Coletas]    │ [Plástico 35%][ Papel 40% ][Alumínio 15%][Vidro 10%]   │
│ [Ecopontos]  │ ────────────────────────────────────────────────────── │
│ [Materiais]  │ [ Gráfico Rosca Categorias ] [ Evolução Mensal Linha ] │
│ [Relatórios] │                                                        │
│              │ [ Volume por Ecoponto Col ]  [ Ranking CO₂ Barras ]    │
└──────────────┴────────────────────────────────────────────────────────┘
```

### 7.2 Mobile: Registro de Coleta (Operador)
```text
┌────────────────────────────────────────┐
│ EcoTrack ── Nova Coleta                │
│                                        │
│ ┌── TICKET DE PESAGEM ───────────────┐ │
│ │ Ecoponto: [ Ecoponto Central   ▼ ] │ │
│ │ Material: [ PET (Plástico)     ▼ ] │ │
│ │                                    │ │
│ │ PESO AFERIDO                       │ │
│ │ 250,500 kg                         │ │
│ │                                    │ │
│ │ IMPACTO ESTIMADO                   │ │
│ │ • CO₂ Evitado:  278,05 kg          │ │
│ │ • Água Poupada: 626,25 L           │ │
│ └- - - - - - - - - - - - - - - - - - ┘ │
│                                        │
│ [   CONFIRMAR E REGISTRAR COLETA   ]   │
│                                        │
├────────────────────────────────────────┤
│ [Início]       [+ COLETAR]    [Histórico]│
└────────────────────────────────────────┘
```

---

## 8. Autocrítica e Eliminação de Acessórios (Fase 0)

1. **O que um modelo genérico teria feito:**
   - Cards com cantos muito arredondados (`rounded-3` ou `rounded-4`), fundos brancos puros com sombras flutuantes azuis ou roxas, ícones decorativos dentro de círculos coloridos ao lado de cada número e cabeçalhos em gradientes.
2. **O que mudamos e por quê:**
   - Substituímos todos os cards arredondados por estrutura modular ancorada em linhas de giz (`--et-linha`), fundo mineral de concreto (`--et-concreto`) e superfícies que remetem a comprovantes de balança e fardos de triagem. A tipografia condensada confere ar utilitário e imediato.
3. **Acessório eliminado:**
   - **Eliminamos:** Ícones genéricos dentro de círculos coloridos antes de cada rótulo de KPI. O número com tipografia tabular expressiva e a legenda clara comunicam com maior densidade de informação e zero ruído decorativo.
