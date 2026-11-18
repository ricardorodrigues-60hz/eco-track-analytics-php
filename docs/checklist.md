# EcoTrack Analytics — Checklist de Homologação e Integração (Fase 8)

Este documento orienta a validação manual e automatizada completa de todos os fluxos e requisitos de conformidade do sistema **EcoTrack Analytics** (TCC FATEC Botucatu — ADS).

---

## 1. Testes Automatizados (PHPUnit)

- [x] **Suíte de Testes Puros:** `tests/FuncoesTest.php` cobrindo 100% das funções utilitárias:
  - Cálculo de impacto de CO₂ evitado e economia hídrica com múltiplos materiais
  - Tratamento de pesos nulos e limiares
  - Cálculo da taxa percentual de ocupação de ecopontos segundo fórmula do ADR-001
  - Validação estrita de datas no padrão ISO e rejeição de entradas inválidas
  - Formatação numérica brasileira (vírgula como decimal, ponto como milhar)
  - Sanitização de entidades HTML contra Cross-Site Scripting (XSS)
  - Serialização JSON com flags de segurança para gráficos

---

## 2. Autenticação e Controle de Acesso (RBAC)

- [x] **Acesso como Administrador:**
  - Login com `admin@ecotrack.local` / `admin123`.
  - Acesso liberado ao painel completo, cadastros auxiliares (ecopontos, categorias, materiais, usuários) e relatórios PDF.
- [x] **Acesso como Coletor (Operador):**
  - Login com `coletor@ecotrack.local` / `coletor123`.
  - Acesso restrito ao terminal de pesagens e ao seu próprio histórico.
  - Tentativa de acesso direto por URL a `/modules/usuarios/` ou `/reports/` resulta em bloqueio HTTP 403 no servidor.
- [x] **Proteção contra Força Bruta:**
  - Tentativas com credenciais incorretas incrementam `tentativas_login`.
  - Após 5 falhas consecutivas, a conta é temporariamente bloqueada por 15 minutos (`bloqueado_ate`).
- [x] **Proteção CSRF:**
  - Todo formulário POST inclui token de sessão em campo oculto validado por `validar_csrf()`.
- [x] **Encerramento de Sessão (Logout):**
  - Revoga cookies de sessão e invalida os dados em memória.

---

## 3. Cadastros Auxiliares (Admin)

- [x] **Usuários:** Criação, edição de perfil/senha e alternância de status ativo/inativo. Proteção contra auto-inativação da própria conta.
- [x] **Ecopontos:** Criação e edição com capacidade nominal em toneladas métricas.
- [x] **Categorias de Materiais:**
  - Código de cores CONAMA 275/2001 com seletor `<input type="color">` e preview dinâmico.
  - Tentativa de exclusão de categoria com materiais associados é bloqueada por `ON DELETE RESTRICT` com mensagem amigável.
- [x] **Materiais & Fatores:**
  - Registro de fatores de CO₂ e água com área de citação da fonte bibliográfica citável (EPA WARM 2023 / WWF Brasil 2008 / Recicla Sampa 2018).
  - Tentativa de exclusão de material com coletas associadas é bloqueada por integridade referencial.

---

## 4. Registro e Operação de Coletas (Ticket de Pesagem)

- [x] **Interface em Formato de Ticket:** Borda picotada em CSS puro e visual de balança física.
- [x] **Cálculo Reativo ao Vivo:**
  - Conforme o operador digita o peso e seleciona o material, o total de CO₂ evitado e água economizada é projetado instantaneamente no ticket.
- [x] **Privacidade e Filtros por Perfil:**
  - Coletor visualiza estritamente as suas próprias coletas.
  - Administrador visualiza todas as coletas da rede.
- [x] **Exclusão Lógica:** Registros excluídos recebem `ativo = 0`, sumindo das listagens e relatórios, mas preservando o log de auditoria.

---

## 5. Painel Analítico (Google Charts)

- [x] **Fardo Segmentado:** Barra contínua no topo demonstrando a proporção percentual de cada categoria no período.
- [x] **Gráfico 1 (Rosca):** Distribuição por categoria com cores CONAMA via `cor_hex`.
- [x] **Gráfico 2 (Linha):** Evolução temporal mensal de volume em kg.
- [x] **Gráfico 3 (Colunas):** Volume recebido por unidade ecoponto.
- [x] **Gráfico 4 (Barras Horizontais):** Ranking de materiais por CO₂ evitado.
- [x] **Redimensionamento:** Gráficos adaptam-se automaticamente a `window.resize`.
- [x] **Estado Vazio:** Mensagem amigável convidando à ação quando não houver dados no período.

---

## 6. Emissão de Relatórios em PDF (HTML2PDF)

- [x] **1. Extrato Geral de Coletas:** Listagem de lançamentos com totalizador consolidado de peso.
- [x] **2. Balanço de Impacto Ambiental:** Demonstrativo científico de emissões evitadas e água poupada por material, acompanhado das fontes citáveis.
- [x] **3. Produtividade e Ocupação por Ecoponto:** Cálculo da taxa de ocupação nominal segundo fórmula do ADR-001.
- [x] **4. Trilha de Auditoria:** Rastreabilidade com `created_at` e `updated_at`, destacando registros modificados após a pesagem original.

---

## 7. Acessibilidade e Responsividade

- [x] **Responsividade Mobile:** Testado para visualização em telas de 360px de largura com barra de navegação inferior (*Bottom Bar*).
- [x] **Alvos de Toque:** Botões e campos com altura mínima de 44px (ou 48px na navegação inferior).
- [x] **Contraste e Legibilidade:** Relação de contraste $\ge 4.5:1$ em todos os textos.
- [x] **Sensibilidade ao Movimento:** Diretiva `@media (prefers-reduced-motion: reduce)` respeitada na animação do ticket.
