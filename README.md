# EcoTrack Analytics

Sistema web modular de alta fidelidade para registro, gestão operacional e auditoria de impacto ambiental na triagem de resíduos recicláveis em ecopontos e cooperativas.

> **Trabalho de Conclusão de Curso (TCC) — FATEC Botucatu**  
> Curso Superior de Tecnologia em Análise e Desenvolvimento de Sistemas (ADS).  
> **Arquitetura de Referência:** ADR-001 (Revisão 2).

---

## 1. Visão Geral da Solução

O **EcoTrack Analytics** foi projetado para suprir a carência de digitalização e mensuração de impacto ambiental em unidades municipais de coleta seletiva e cooperativas de reciclagem. O sistema transforma o peso aferido na balança física em indicadores científicos auditáveis de sustentabilidade:
- **Gases de Efeito Estufa (GEE):** Quilogramas de CO₂ equivalente mitigados (evitados).
- **Recursos Hídricos:** Litros de água economizados no processo produtivo.

A interface rejeita a estética genérica de painéis SaaS administrativos e adota uma **identidade visual mineral inspirada em balanças industriais e tickets de pesagem**, com padronização cromática estrita das categorias conforme a **Resolução CONAMA 275/2001**.

---

## 2. Tecnologias Utilizadas

- **Backend:** PHP 8.2+ puro (Monólito modular server-side, sem framework desacoplado)
- **Persistência:** MariaDB / MySQL 8.0+ via PDO com *Prepared Statements*
- **Front-end:** HTML5 semântico, CSS3 com Design System mineral, Bootstrap 5 (`data-bs-*`)
- **Tipografia:** Barlow Condensed (títulos e dígitos tabulares) e Public Sans (corpo de texto)
- **Gráficos Dinâmicos:** Google Charts API (`corechart`)
- **Documentos & Relatórios:** HTML2PDF 5.2.8 (`spipu/html2pdf`)
- **Testes Automatizados:** PHPUnit 10.5+

---

## 3. Fundamentação Científica dos Fatores Ambientais

Todos os cálculos de impacto ambiental baseiam-se em referências bibliográficas acadêmicas e setoriais citáveis:

| Material | Categoria CONAMA | CO₂ Evitado (`fator_co2_kg`) | Água Poupada (`fator_agua_litros`) | Fonte CO₂e | Fonte Hídrica |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PET** | Plástico (`#CC0000`) | 1,1100 kg CO₂e / kg | 2,50 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |
| **Papelão** | Papel (`#005EB8`) | 3,1400 kg CO₂e / kg | 98,00 L / kg | EPA WARM v16 (2023) | Recicla Sampa (2018) |
| **Alumínio** | Metal (`#FFC000`) | 10,0300 kg CO₂e / kg | 7,60 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |
| **Vidro** | Vidro (`#007A33`) | 0,2800 kg CO₂e / kg | 1,50 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |

*Consulte [docs/referencias-fatores.md](docs/referencias-fatores.md) para o detalhamento metodológico e limitações declaradas.*

---

## 4. Guia de Instalação e Execução

### Passo 1: Posicionamento dos Arquivos
Posicione o diretório do projeto no servidor web Apache (por exemplo, em `C:\xampp\htdocs\eco-track-analytics-php`).

### Passo 2: Extensões do PHP
No arquivo `php.ini` do Apache/XAMPP, certifique-se de que as seguintes extensões estão ativas:
```ini
extension=pdo_mysql
extension=mbstring
extension=gd
```

### Passo 3: Dependências do Composer
No terminal da pasta do projeto, execute:
```bash
composer install
```
*(Caso o Composer não esteja no PATH global, use o caminho do binário do PHP do XAMPP):*
```bash
C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### Passo 4: Banco de Dados (Schema & Seed)
No phpMyAdmin ou terminal MySQL, crie o banco e execute os scripts na ordem:
1. `sql/schema.sql` (Criação de tabelas, chaves e restrições)
2. `sql/seed.sql` (Carga de demonstração com 12 meses de dados históricos)

### Passo 5: Configuração de Conexão
Copie o modelo de configuração:
```bash
cp config/config.example.php config/database.php
```
Ajuste `DB_HOST`, `DB_USER` e `DB_PASS` caso necessário.

### Passo 6: Acesso ao Sistema
Abra o navegador e acesse:
```text
http://localhost/eco-track-analytics-php/
```

---

## 5. Credenciais de Demonstração (Seed)

| Perfil | E-mail | Senha Padrão | Permissões no Sistema |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@ecotrack.local` | `admin123` | Acesso irrestrito a todos os cadastros, painel analítico global e emissão dos 4 relatórios PDF |
| **Coletor (Operador)** | `coletor@ecotrack.local` | `coletor123` | Lançamento de pesagens (ticket), visualização estrita do seu próprio histórico e dashboard operacional |

---

## 6. Roteiro Sugerido para Apresentação do TCC

1. **Acesso com Perfil Coletor:**
   - Faça login com `coletor@ecotrack.local` / `coletor123`.
   - Demonstre a barra de navegação inferior mobile no navegador (inspecionar em modo celular de 360px).
   - Clique em **"Coletar"** e mostre o formulário em formato de **Ticket de Pesagem**.
   - Digite um peso em kg e selecione materiais diferentes: mostre o **cálculo de impacto projetado instantaneamente em tempo real**.
   - Submeta a coleta e mostre o efeito visual de impressão do comprovante.
   - Demonstre que na listagem de coletas, o operador enxerga estritamente as suas próprias pesagens.
2. **Tentativa de Acesso Não Autorizado:**
   - Tente acessar `/modules/usuarios/` diretamente na URL e mostre o bloqueio HTTP 403 do servidor.
3. **Acesso com Perfil Administrador:**
   - Efetue logout e faça login como `admin@ecotrack.local` / `admin123`.
   - Acesse o **Painel Analítico** e apresente a **Barra de Fardo** e os **4 Gráficos Google Charts** (Rosca, Linhas, Colunas e Barras horizontais).
   - Demonstre o redimensionamento dinâmico ao ajustar o tamanho da tela.
4. **Cadastros e Integridade Referencial:**
   - Acesse **Categorias** e tente excluir uma categoria que possua materiais vinculados: apresente a mensagem amigável de bloqueio disparada pelo `ON DELETE RESTRICT`.
5. **Emissão de Relatórios em PDF:**
   - Acesse a central de relatórios e gere o **Balanço de Impacto** e a **Produtividade por Ecoponto** (apresentando a taxa percentual de ocupação calculada).
6. **Execução de Testes Automatizados:**
   - No terminal, execute `vendor/bin/phpunit` para comprovar a passagem de 100% dos testes unitários das regras puras de cálculo.
