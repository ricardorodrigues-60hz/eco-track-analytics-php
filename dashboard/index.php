<?php
/**
 * EcoTrack Analytics — Painel Analítico Geral e Operacional (Dashboard)
 * 
 * Quatro Gráficos Oficiais (Google Charts API):
 * 1. Rosca de Volume por Categoria (PieChart com pieHole)
 * 2. Evolução Temporal Mensal de Pesagem (LineChart)
 * 3. Volume Consolidado por Ecoponto (ColumnChart)
 * 4. Ranking de CO₂ Evitado por Tipo de Material (BarChart horizontal)
 */

require_once __DIR__ . '/../includes/auth_check.php';
exigir_login();

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/icones.php';

$usuario_logado = obter_usuario_logado();
$eh_admin = ($usuario_logado['perfil'] === 'admin');
$usuario_restrito = $eh_admin ? null : $usuario_logado['id'];

$titulo_pagina = $eh_admin ? 'Painel Analítico de Impacto' : 'Painel Operacional do Coletor';

// Definição do Período de Análise (Padrão: 12 meses do seed — 01/11/2025 até 31/10/2026)
$data_inicio = $_GET['data_inicio'] ?? '2025-11-01';
$data_fim    = $_GET['data_fim'] ?? '2026-10-31';

if (!validar_data_formato($data_inicio)) {
    $data_inicio = '2025-11-01';
}
if (!validar_data_formato($data_fim)) {
    $data_fim = '2026-10-31';
}

// Consultas agregadas isoladas no repositório
$totais = obter_totais_dashboard($data_inicio, $data_fim, $usuario_restrito);
$dados_categorias = obter_dados_grafico_categorias($data_inicio, $data_fim, $usuario_restrito);
$dados_mensal     = obter_dados_grafico_evolucao_mensal($data_inicio, $data_fim, $usuario_restrito);
$dados_ecopontos  = obter_dados_grafico_ecopontos($data_inicio, $data_fim, $usuario_restrito);
$dados_ranking    = obter_dados_grafico_ranking_co2($data_inicio, $data_fim, $usuario_restrito);

// Cálculo percentual para a barra de fardo do período
$total_peso_categorias = array_sum(array_column($dados_categorias, 'total_peso_kg'));

require_once __DIR__ . '/../includes/header.php';
?>

<!-- CABEÇALHO DO PAINEL COM FILTRO DE PERÍODO -->
<div class="d-flex flex-wrap justify-content-between align-items-end mb-4 pb-3 gap-3 et-cabecalho-secao">
    <div>
        <p class="et-secao-supertitulo"><?= $eh_admin ? 'Indicadores globais de impacto' : 'Desempenho individual de coletas' ?></p>
        <h1 class="mb-0"><?= e($titulo_pagina) ?></h1>
    </div>

    <!-- Filtro de Período (GET) -->
    <form method="GET" action="" class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center gap-1 bg-white p-1" style="border:2px solid var(--et-linha);">
            <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); padding:0 0.35rem;">De</span>
            <input type="date" name="data_inicio" class="form-control form-control-sm border-0 et-tabular" value="<?= e($data_inicio) ?>" style="min-height:30px; font-weight:600;">
            <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); padding:0 0.35rem;">até</span>
            <input type="date" name="data_fim" class="form-control form-control-sm border-0 et-tabular" value="<?= e($data_fim) ?>" style="min-height:30px; font-weight:600;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm px-3" style="min-height:38px;">Aplicar</button>
    </form>
</div>

<?php if (defined('FATORES_PROVISORIOS') && FATORES_PROVISORIOS === true): ?>
    <div class="alert alert-warning mb-4" role="alert" style="background-color:#FFF3CD; border-color:var(--et-aviso); color:#664D03;">
        <strong>Nota Metodológica:</strong> Os fatores de emissão de CO₂ e economia hídrica atuais são preliminares e pendentes de atualização citável antes da homologação final do artigo.
    </div>
<?php endif; ?>

<!-- FITA DE PESAGEM (substitui os 4 cards idênticos) -->
<div class="et-fita-pesagem mb-4" role="region" aria-label="Resumo de indicadores do período">
    <?php
    $provisorio = defined('FATORES_PROVISORIOS') && FATORES_PROVISORIOS === true;
    $linhas = [
        [
            'rotulo' => 'Cargas registradas',
            'valor'  => formatar_numero((float)$totais['total_coletas'], 0),
            'unidade' => 'tickets',
            'provisorio' => false,
        ],
        [
            'rotulo' => 'Volume reciclado',
            'valor'  => formatar_numero($totais['total_peso_kg'], 0),
            'unidade' => 'kg',
            'provisorio' => false,
        ],
        [
            'rotulo' => 'CO₂ evitado',
            'valor'  => formatar_numero($totais['total_co2_kg'], 0),
            'unidade' => 'kg CO₂e',
            'provisorio' => $provisorio,
        ],
        [
            'rotulo' => 'Recursos hídricos poupados',
            'valor'  => formatar_numero($totais['total_agua_litros'], 0),
            'unidade' => 'litros',
            'provisorio' => $provisorio,
        ],
    ];
    foreach ($linhas as $linha):
    ?>
        <div class="et-fita-pesagem__linha">
            <span class="et-fita-pesagem__rotulo"><?= $linha['rotulo'] ?></span>
            <span class="et-fita-pesagem__guia" aria-hidden="true"></span>
            <span class="et-fita-pesagem__valor et-num">
                <?= $linha['valor'] ?>
                <span class="et-fita-pesagem__unidade"><?= $linha['unidade'] ?></span>
                <?php if ($linha['provisorio']): ?>
                    <span class="et-carimbo-provisorio" aria-label="valor provisório">prov.</span>
                <?php endif; ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($totais['total_coletas'] === 0): ?>
    <!-- ESTADO VAZIO -->
    <div class="et-empty-state my-5">
        <div class="et-empty-state__title">Nenhuma coleta registrada no período selecionado</div>
        <p class="mb-3">Selecione um intervalo de datas maior ou realize o lançamento de pesagens para gerar as análises gráficas.</p>
        <a href="/eco-track-analytics-php/modules/coletas/form.php" class="btn btn-primary">
            <?= icone('coletar') ?> Registrar Primeira Coleta
        </a>
    </div>
<?php else: ?>

    <!-- SEPARADOR 01: COMPOSIÇÃO DO FARDO -->
    <hr class="et-secao-hr">
    <h2 class="et-secao-titulo"><span class="et-secao-titulo__num">01</span> Composição do fardo
        <span class="et-secao-titulo__meta et-tabular">
            Total: <?= formatar_numero($total_peso_categorias, 1) ?> kg
        </span>
    </h2>

    <!-- BARRA DE FARDO SEGMENTADA (com animação de enchimento) -->
    <div class="et-fardo mb-5" role="img" aria-label="Proporção percentual de resíduos no período" id="et-fardo-principal">
        <?php foreach ($dados_categorias as $cat):
            $pct = $total_peso_categorias > 0 ? ($cat['total_peso_kg'] / $total_peso_categorias) * 100.0 : 0.0;
            if ($pct < 1.0) continue;
        ?>
            <div class="et-fardo__fatia"
                 data-pct="<?= number_format($pct, 2, '.', '') ?>"
                 data-categoria="<?= e($cat['nome_categoria']) ?>"
                 style="width:0%; background-color:<?= e($cat['cor_hex']) ?>;"
                 title="<?= e($cat['nome_categoria']) ?>: <?= formatar_numero($pct, 1) ?>% (<?= formatar_numero($cat['total_peso_kg'], 0) ?> kg)">
                <?= e($cat['nome_categoria']) ?> (<?= formatar_numero($pct, 1) ?>%)
            </div>
        <?php endforeach; ?>
    </div>

    <!-- LINHA: EVOLUÇÃO MENSAL (8 col) + RANKING CO₂ (4 col) -->
    <hr class="et-secao-hr">
    <div class="row g-0 mb-1">
        <div class="col-lg-8">
            <h2 class="et-secao-titulo"><span class="et-secao-titulo__num">02</span> Evolução mensal de pesagem</h2>
        </div>
        <div class="col-lg-4">
            <h2 class="et-secao-titulo"><span class="et-secao-titulo__num">03</span> Ranking de CO₂ evitado</h2>
        </div>
    </div>
    <div class="row g-3 mb-5">
        <div class="col-lg-8">
            <div class="et-grafico-bloco">
                <span class="et-grafico-bloco__meta">Volume em kg / mês</span>
                <div id="grafico_evolucao_mensal" style="width:100%; height:300px;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="et-grafico-bloco">
                <span class="et-grafico-bloco__meta">kg CO₂e mitigado por material</span>
                <div id="grafico_ranking_co2" style="width:100%; height:300px;"></div>
            </div>
        </div>
    </div>

    <!-- LINHA: ROSCA CATEGORIAS (5 col) + VOLUME ECOPONTO (7 col) -->
    <hr class="et-secao-hr">
    <div class="row g-0 mb-1">
        <div class="col-lg-5">
            <h2 class="et-secao-titulo"><span class="et-secao-titulo__num">04</span> Distribuição por categoria</h2>
        </div>
        <div class="col-lg-7">
            <h2 class="et-secao-titulo"><span class="et-secao-titulo__num">05</span> Volume por unidade ecoponto</h2>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="et-grafico-bloco">
                <span class="et-grafico-bloco__meta">Proporção relativa no período</span>
                <div id="grafico_rosca_categorias" style="width:100%; height:300px;"></div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="et-grafico-bloco">
                <span class="et-grafico-bloco__meta">Total em quilogramas</span>
                <div id="grafico_volume_ecoponto" style="width:100%; height:300px;"></div>
            </div>
        </div>
    </div>

    <!-- DADOS DOS GRÁFICOS ANALÍTICOS (JSON SEGURO) -->
    <script type="application/json" id="dados-dashboard-graficos"><?= codificar_json_seguro([
        'categorias' => $dados_categorias,
        'mensal'     => $dados_mensal,
        'ecopontos'  => $dados_ecopontos,
        'ranking'    => $dados_ranking,
    ]) ?></script>

<?php endif; ?>

<?php
$scripts_especificos = ['assets/js/dashboard.js', 'assets/js/fardo-animar.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
