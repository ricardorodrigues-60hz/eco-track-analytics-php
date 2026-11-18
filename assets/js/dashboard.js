/**
 * EcoTrack Analytics — Renderização dos Gráficos Analíticos (Google Charts)
 * 
 * Lê os dados estruturados do bloco JSON seguro <script type="application/json" id="dados-dashboard-graficos">
 * e renderiza os 4 gráficos oficiais do sistema com suporte a redimensionamento responsivo.
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var elementoDados = document.getElementById('dados-dashboard-graficos');
        if (!elementoDados) {
            return;
        }

        var dadosGraficos;
        try {
            dadosGraficos = JSON.parse(elementoDados.textContent);
        } catch (erro) {
            console.error('Falha ao processar os dados dos gráficos analíticos:', erro);
            return;
        }

        if (typeof google === 'undefined' || typeof google.charts === 'undefined') {
            console.warn('Google Charts API não carregada.');
            return;
        }

        google.charts.load('current', { 'packages': ['corechart'] });
        google.charts.setOnLoadCallback(desenharTodosGraficos);

        function desenharTodosGraficos() {
            if (dadosGraficos.categorias && dadosGraficos.categorias.length > 0) {
                desenharGraficoCategorias(dadosGraficos.categorias);
            }
            if (dadosGraficos.mensal && dadosGraficos.mensal.length > 0) {
                desenharGraficoEvolucao(dadosGraficos.mensal);
            }
            if (dadosGraficos.ecopontos && dadosGraficos.ecopontos.length > 0) {
                desenharGraficoEcopontos(dadosGraficos.ecopontos);
            }
            if (dadosGraficos.ranking && dadosGraficos.ranking.length > 0) {
                desenharGraficoRankingCO2(dadosGraficos.ranking);
            }
        }

        // 1. Gráfico de Rosca por Categoria
        function desenharGraficoCategorias(itens) {
            var el = document.getElementById('grafico_rosca_categorias');
            if (!el) return;

            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Categoria');
            dataTable.addColumn('number', 'Peso (kg)');

            var cores = [];
            for (var i = 0; i < itens.length; i++) {
                dataTable.addRow([itens[i].nome_categoria, parseFloat(itens[i].total_peso_kg)]);
                cores.push(itens[i].cor_hex);
            }

            var options = {
                pieHole: 0.42,
                colors: cores,
                chartArea: { width: '90%', height: '80%' },
                legend: { position: 'right', textStyle: { fontName: 'Public Sans', fontSize: 12 } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.PieChart(el);
            chart.draw(dataTable, options);
        }

        // 2. Gráfico de Evolução Mensal (Linhas)
        function desenharGraficoEvolucao(itens) {
            var el = document.getElementById('grafico_evolucao_mensal');
            if (!el) return;

            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Mês');
            dataTable.addColumn('number', 'Volume Coletado (kg)');

            for (var i = 0; i < itens.length; i++) {
                dataTable.addRow([itens[i].rotulo, parseFloat(itens[i].total_peso)]);
            }

            var options = {
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

            var chart = new google.visualization.LineChart(el);
            chart.draw(dataTable, options);
        }

        // 3. Gráfico de Volume por Ecoponto (Colunas)
        function desenharGraficoEcopontos(itens) {
            var el = document.getElementById('grafico_volume_ecoponto');
            if (!el) return;

            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Ecoponto');
            dataTable.addColumn('number', 'Volume (kg)');

            for (var i = 0; i < itens.length; i++) {
                dataTable.addRow([itens[i].ecoponto, parseFloat(itens[i].total_peso)]);
            }

            var options = {
                colors: ['#5C6B70'],
                chartArea: { width: '85%', height: '75%' },
                legend: { position: 'none' },
                hAxis: { textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                vAxis: { minValue: 0, textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.ColumnChart(el);
            chart.draw(dataTable, options);
        }

        // 4. Gráfico de Ranking de CO₂ Evitado (Barras Horizontais)
        function desenharGraficoRankingCO2(itens) {
            var el = document.getElementById('grafico_ranking_co2');
            if (!el) return;

            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Material');
            dataTable.addColumn('number', 'CO₂ Evitado (kg)');
            dataTable.addColumn({ type: 'string', role: 'style' });

            for (var i = 0; i < itens.length; i++) {
                dataTable.addRow([
                    itens[i].material,
                    parseFloat(itens[i].total_co2),
                    'color: ' + itens[i].cor_hex
                ]);
            }

            var options = {
                chartArea: { width: '70%', height: '80%' },
                legend: { position: 'none' },
                hAxis: { minValue: 0, textStyle: { fontName: 'Public Sans', fontSize: 11 } },
                vAxis: { textStyle: { fontName: 'Public Sans', fontSize: 12, bold: true } },
                fontName: 'Public Sans',
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.BarChart(el);
            chart.draw(dataTable, options);
        }

        // Redesenho automático de gráficos ao alterar dimensão da janela
        window.addEventListener('resize', function() {
            desenharTodosGraficos();
        });
    });
})();
