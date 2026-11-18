# EcoTrack Analytics

Sistema web modular para registrar, gerenciar e auditar o impacto ambiental (CO₂ evitado e economia hídrica) na triagem de resíduos recicláveis em ecopontos e cooperativas.

> Projeto desenvolvido como Trabalho de Conclusão de Curso (TCC) — FATEC Botucatu  
> Curso Superior de Tecnologia em Análise e Desenvolvimento de Sistemas (ADS).

---

## 1. Tecnologias Utilizadas

- **Linguagem & Ambiente:** PHP 8.2+ puro (Server-side, monólito modular, sem framework)
- **Banco de Dados:** MariaDB / MySQL via PDO com *Prepared Statements*
- **Front-end:** HTML5 semântico, CSS3 customizado com Design System mineral, Bootstrap 5 (`data-bs-*`)
- **Visualização de Dados:** Google Charts API (`loader.js` + pacote `corechart`)
- **Geração de Documentos:** HTML2PDF 5.2.8 (`spipu/html2pdf`)
- **Testes Automatizados:** PHPUnit 10.5+

---

## 2. Requisitos de Sistema

- Servidor Apache com PHP 8.2 (ex: pacote XAMPP)
- MariaDB ou MySQL 8.0+
- Extensões PHP obrigatórias habilitadas no `php.ini`:
  - `pdo_mysql`
  - `mbstring`
  - `gd` (necessária para processamento de imagens e renderização nos relatórios PDF)
- Gerenciador de dependências **Composer**

---

## 3. Guia de Instalação e Configuração

### Passo 1: Obter o projeto e configurar o ambiente
Clone o repositório ou posicione a pasta no diretório público do servidor web (por exemplo, `C:\xampp\htdocs\eco-track-analytics-php`).

### Passo 2: Instalar as dependências do Composer
No terminal, na raiz do projeto, execute:
```bash
composer install
```
*(Caso esteja utilizando o XAMPP no Windows e o PHP não esteja no PATH global, execute apontando para o binário do XAMPP):*
```bash
C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
```

### Passo 3: Configurar o banco de dados
1. Abra o gerenciador do banco (phpMyAdmin ou terminal MySQL).
2. Execute o script de criação de tabelas: `sql/schema.sql`.
3. Em seguida, execute a carga de dados iniciais e coletas: `sql/seed.sql`.

### Passo 4: Configurar a conexão da aplicação
1. Copie o arquivo de exemplo de configuração:
   ```bash
   cp config/config.example.php config/database.php
   ```
2. Abra `config/database.php` e ajuste as constantes `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS` de acordo com suas credenciais locais.

### Passo 5: Executar a aplicação
Inicie o Apache e MySQL no painel do XAMPP e acesse em seu navegador:
```text
http://localhost/eco-track-analytics-php/
```

---

## 4. Credenciais de Demonstração (Seed)

| Perfil de Acesso | E-mail de Login | Senha Padrão | Permissões |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@ecotrack.local` | `admin123` | Acesso integral: CRUD de cadastros, painel analítico global e todos os relatórios PDF |
| **Coletor (Operador)** | `coletor@ecotrack.local` | `coletor123` | Lançamento de pesagens (ticket), histórico restrito às suas coletas e dashboard operacional |

---

## 5. Estrutura do Projeto

```text
eco-track-analytics-php/
├── assets/         # Folhas de estilo (ecotrack.css), scripts e imagens institucionais
├── auth/           # Scripts de autenticação, encerramento de sessão e verificação de bloqueio
├── config/         # Arquivos de conexão PDO e credenciais locais
├── dashboard/      # Painel analítico e consultas agregadas para os gráficos Google Charts
├── docs/           # Arquitetura (ADR-001), plano de design e referências metodológicas
├── includes/       # Componentes compartilhados, cabeçalho, rodapé, segurança e CSRF
├── modules/        # CRUDs modulares (usuários, ecopontos, categorias, materiais, coletas)
├── reports/        # Controladores e templates de relatórios PDF (HTML2PDF)
├── sql/            # Scripts DDL (schema) e DML (seed com dados de teste)
└── tests/          # Suíte de testes unitários PHPUnit
```

---

## 6. Licença e Autoria

Desenvolvido para fins acadêmicos sob licença MIT. Consulte o arquivo [LICENSE](LICENSE) para mais detalhes.
