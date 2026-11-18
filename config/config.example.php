<?php
/**
 * EcoTrack Analytics — Arquivo de Configuração de Exemplo
 * 
 * Copie este arquivo para config/database.php (ou config/config.local.php) e ajuste conforme seu ambiente.
 * NUNCA versione o arquivo com senhas reais.
 */

// Configurações do Banco de Dados MariaDB / MySQL
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'ecotrack');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Fatores de Impacto Ambiental
// false = fatores definitivos com fonte citável (EPA WARM 2023 / WWF Brasil 2008 / Recicla Sampa 2018)
// true  = fatores provisórios (exibe aviso discreto no dashboard e no relatório consolidado)
define('FATORES_PROVISORIOS', false);

// Ambiente de Execução: 'dev' ou 'prod'
// Em 'prod', display_errors é desativado e erros são enviados para log
define('AMBIENTE', 'dev');

// URL Base da Aplicação (opcional, para redirecionamentos e caminhos absolutos se necessário)
define('APP_URL', 'http://localhost/eco-track-analytics-php');
