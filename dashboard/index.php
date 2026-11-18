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
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 gap-3" style="border-bottom: 2px solid var(--et-linha);">
    <div>
        <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); text-transform:uppercase; letter-spacing:0.05em;">
            <?= $eh_admin ? 'Indicadores Globais de Impacto' : 'Desempenho Individual de Coletas' ?>
        </span>
        <h1 class="mb-0" style="font-size:2.2rem;"><?= e($titulo_pagina) ?></h1>
    </div>

    <!-- Filtro de Período (GET) -->
    <form method="GET" action="" class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center gap-1 bg-white p-1" style="border:2px solid var(--et-linha);">
            <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); padding:0 0.35rem;">DE</span>
            <input type="date" name="data_inicio" class="form-control form-control-sm border-0 et-tabular" value="<?= e($data_inicio) ?>" style="min-height:30px; font-weight:600;">
            <span style="font-size:0.8rem; font-weight:700; color:var(--et-cinza); padding:0 0.35rem;">ATÉ</span>
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

<!-- CARDS DE MÉTRICAS CONSOLIDADAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="p-3" style="background:var(--et-ticket); border:2px solid var(--et-linha);">
            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-cinza); letter-spacing:0.05em;">Cargas Registradas</span>
            <div class="et-num fw-bold" style="font-size:2.4rem; color:var(--et-tinta); line-height:1.1; margin-top:0.25rem;">
                <?= formatar_numero((float)$totais['total_coletas'], 0) ?>
            </div>
            <span style="font-size:0.8rem; color:var(--et-cinza);">tickets validados</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="p-3" style="background:var(--et-ticket); border:2px solid var(--et-linha);">
            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-cinza); letter-spacing:0.05em;">Volume Reciclado</span>
            <div class="et-num fw-bold" style="font-size:2.4rem; color:var(--et-tinta); line-height:1.1; margin-top:0.25rem;">
                <?= formatar_numero($totais['total_peso_kg'], 0) ?> <span style="font-size:1.2rem; font-weight:600;">kg</span>
            </div>
            <span style="font-size:0.8rem; color:var(--et-cinza);"><?= formatar_numero($totais['total_peso_kg'] / 1000.0, 2) ?> toneladas métricas</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="p-3" style="background:var(--et-ticket); border:2px solid var(--et-linha); border-left:4px solid var(--et-sucesso);">
            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-sucesso); letter-spacing:0.05em;">CO₂ Evitado (Mitigado)</span>
            <div class="et-num fw-bold" style="font-size:2.4rem; color:var(--et-sucesso); line-height:1.1; margin-top:0.25rem;">
                <?= formatar_numero($totais['total_co2_kg'], 0) ?> <span style="font-size:1.2rem; font-weight:600;">kg</span>
            </div>
            <span style="font-size:0.8rem; color:var(--et-cinza);"><?= formatar_numero($totais['total_co2_kg'] / 1000.0, 2) ?> t CO₂e (EPA WARM)</span>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="p-3" style="background:var(--et-ticket); border:2px solid var(--et-linha); border-left:4px solid var(--et-papel);">
            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--et-papel); letter-spacing:0.05em;">Recursos Hídricos Poupados</span>
            <div class="et-num fw-bold" style="font-size:2.4rem; color:var(--et-papel); line-height:1.1; margin-top:0.25rem;">
                <?= formatar_numero($totais['total_agua_litros'], 0) ?> <span style="font-size:1.2rem; font-weight:600;">L</span>
            </div>
            <span style="font-size:0.8rem; color:var(--et-cinza);"><?= formatar_numero($totais['total_agua_litros'] / 1000.0, 1) ?> m³ poupados</span>
        </div>
    </div>
</div>

<?php if ($totais['total_coletas'] === 0): ?>
    <!-- ESTADO VAZIO -->
    <div class="et-empty-state my-5">
        <div class="et-empty-state__title">Nenhuma coleta registrada no período selecionado</div>
        <p class="mb-3">Selecione um intervalo de datas maior ou realize o lançamento de pesagens para gerar as análises gráficas.</p>
        <a href="/eco-track-analytics-php/modules/coletas/form.php" class="btn btn-primary">
            <span>⚖️</span> Registrar Primeira Coleta
        </a>
    </div>
<?php else: ?>

    <!-- ELEMENTO MEMORÁVEL: BARRA DE FARDO SEGMENTADA -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:var(--et-cinza); letter-spacing:0.05em;">
                📦 Fardo Consolidado do Período — Proporção por Categoria (CONAMA 275/2001)
            </span>
            <span style="font-size:0.8rem; font-weight:700; color:var(--et-tinta);" class="et-tabular">
                Total: <?= formatar_numero($total_peso_categorias, 1) ?> kg
            </span>
        </div>

        <div class="et-fardo" role="img" aria-label="Proporção percentual de resíduos no período">
            <?php foreach ($dados_categorias as $cat): ?>
                <?php 
                $pct = $total_peso_categorias > 0 ? ($cat['total_peso_kg'] / $total_peso_categorias) * 100.0 : 0.0;
                if ($pct < 1.0) continue;
                ?>
                <div class="et-fardo__fatia"
                     style="width: <?= number_format($pct, 2, '.', '') ?>%; background-color: <?= e($cat['cor_hex']) ?>;"
                     title="<?= e($cat['nome_categoria']) ?>: <?= formatar_numero($pct, 1) ?>% (<?= formatar_numero($cat['total_peso_kg'], 0) ?> kg)">
                    <?= e($cat['nome_categoria']) ?> (<?= formatar_numero($pct, 1) ?>%)
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- OS 4 GRÁFICOS OFICIAIS DO SISTEMA -->
    <div class="row g-4">
        
        <!-- GRÁFICO 1: ROSCA POR CATEGORIA -->
        <div class="col-lg-6">
            <div class="p-3" style="background:#FFFFFF; border:2px solid var(--et-linha);">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px solid var(--et-linha);">
                    <h2 style="font-size:1.15rem; margin:0;">1. Volume por Categoria</h2>
                    <span style="font-size:0.75rem; color:var(--et-cinza); font-weight:600;">Proporção Relativa</span>
                </div>
                <div id="grafico_rosca_categorias" style="width:100%; height:320px;"></div>
            </div>
        </div>

        <!-- GRÁFICO 2: EVOLUÇÃO TEMPORAL MENSAL -->
        <div class="col-lg-6">
            <div class="p-3" style="background:#FFFFFF; border:2px solid var(--et-linha);">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px solid var(--et-linha);">
                    <h2 style="font-size:1.15rem; margin:0;">2. Evolução Mensal de Pesagem</h2>
                    <span style="font-size:0.75rem; color:var(--et-cinza); font-weight:600;">Volume em kg</span>
                </div>
                <div id="grafico_evolucao_mensal" style="width:100%; height:320px;"></div>
            </div>
        </div>

        <!-- GRÁFICO 3: VOLUME POR ECOPONTO -->
        <div class="col-lg-6">
            <div class="p-3" style="background:#FFFFFF; border:2px solid var(--et-linha);">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px solid var(--et-linha);">
                    <h2 style="font-size:1.15rem; margin:0;">3. Volume por Unidade Ecoponto</h2>
                    <span style="font-size:0.75rem; color:var(--et-cinza); font-weight:600;">Total em Quilogramas</span>
                </div>
                <div id="grafico_volume_ecoponto" style="width:100%; height:320px;"></div>
            </div>
        </div>

        <!-- GRÁFICO 4: RANKING DE CO₂ EVITADO POR MATERIAL -->
        <div class="col-lg-6">
            <div class="p-3" style="background:#FFFFFF; border:2px solid var(--et-linha);">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px solid var(--et-linha);">
                    <h2 style="font-size:1.15rem; margin:0;">4. Ranking de CO₂ Evitado</h2>
                    <span style="font-size:0.75rem; color:var(--et-cinza); font-weight:600;">kg CO₂e Mitigado</span>
                </div>
                <div id="grafico_ranking_co2" style="width:100%; height:320px;"></div>
            </div>
        </div>

    </div>

    <!-- SCRIPT DE RENDERIZAÇÃO DOS GRÁFICOS GOOGLE CHARTS -->
    <script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(desenharTodosGraficos);

        function desenharTodosGraficos() {
            desenharGraficoCategorias();
            desenharGraficoEvolucao();
            desenharGraficoEcopontos();
            desenharGraficoRankingCO2();
        }

        // 1. Gráfico de Rosca (PieChart com pieHole)
        function desenharGraficoCategorias() {
            const raw = <?= codificar_json_seguro($dados_categorias) ?>;
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Categoria');
            dataTable.addColumn('number', 'Peso (kg)');

            const cores = [];
            raw.forEach(item => {
                dataTable.addRow([item.nome_categoria, parseFloat(item.total_peso_kg)]);
                cores.push(item.cor_hex);
            });

            const options = {
                pieHole: 0.42,
                colors: cores,
                chartArea: { width: '90%', height: '80%' },
                legend: { position: 'right', textStyle: { fontName: 'Public Sans', fontSize: 12 } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            const chart = new google.visualization.PieChart(document.getElementById('grafico_rosca_categorias'));
            chart.draw(dataTable, options);
        }

        // 2. Gráfico de Evolução Mensal (LineChart)
        function desenharGraficoEvolucao() {
            const raw = <?= codificar_json_seguro($dados_mensal) ?>;
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Mês');
            dataTable.addColumn('number', 'Volume Coletado (kg)');

            raw.forEach(item => {
                dataTable.addRow([item.rotulo, parseFloat(item.total_peso)]);
            });

            const options = {
                colors: ['#1B2A30'],
                lineWidth: 3,
                pointSize: 6,
                chartArea: { width: '85%', height: '75%' },
                legend: { position: 'none' },
                hAxis: { textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                vAxis: { minValue: 0, textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            const chart = new google.visualization.LineChart(document.getElementById('grafico_evolucao_mensal'));
            chart.draw(dataTable, options);
        }

        // 3. Gráfico de Volume por Ecoponto (ColumnChart)
        function desenharGraficoEcopontos() {
            const raw = <?= codificar_json_seguro($dados_ecopontos) ?>;
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Ecoponto');
            dataTable.addColumn('number', 'Volume (kg)');

            raw.forEach(item => {
                dataTable.addRow([item.ecoponto, parseFloat(item.total_peso)]);
            });

            const options = {
                colors: ['#5C6B70'],
                chartArea: { width: '85%', height: '75%' },
                legend: { position: 'none' },
                hAxis: { textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                vAxis: { minValue: 0, textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            const chart = new google.visualization.ColumnChart(document.getElementById('grafico_volume_ecoponto'));
            chart.draw(dataTable, options);
        }

        // 4. Gráfico de Ranking de CO₂ Evitado (BarChart horizontal)
        function desenharGraficoRankingCO2() {
            const raw = <?= codificar_json_seguro($dados_ranking) ?>;
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Material');
            dataTable.addColumn('number', 'CO₂ Evitado (kg)');
            dataTable.addColumn({type: 'string', role: 'style'});

            raw.forEach(item => {
                dataTable.addRow([item.material, parseFloat(item.total_co2), 'color: ' + item.cor_hex]);
            });

            const options = {
                chartArea: { width: '70%', height: '80%' },
                legend: { position: 'none' },
                hAxis: { minValue: 0, textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                vAxis: { textStyle: { fontName: 'Public Sans', fontSize: 12, bold: true } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            const chart = new google.visualization.BarChart(document.getElementById('grafico_ranking_co2'));
            chart.draw(dataTable, options);
        }

        // Redesenho automático ao redimensionar a janela
        window.addEventListener('resize', function() {
            desenharTodosGraficos();
        });
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
