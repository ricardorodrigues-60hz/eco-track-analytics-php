<!-- Template HTML2PDF: Extrato Geral de Coletas -->
<style>
    table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 9pt; }
    th { background-color: #1B2A30; color: #FFFFFF; font-weight: bold; text-align: left; padding: 6px 5px; }
    td { padding: 5px; border-bottom: 1px solid #C9CECB; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row { background-color: #E6E8E5; font-weight: bold; }
</style>

<page backtop="30mm" backbottom="15mm" backleft="10mm" backright="10mm">
    <!-- Cabeçalho Institucional -->
    <page_header>
        <table style="border-bottom: 2px solid #1B2A30; padding-bottom: 8px;">
            <tr>
                <td style="width: 70%; border: none;">
                    <span style="font-size: 15pt; font-weight: bold; color: #1B2A30;">EcoTrack Analytics</span><br>
                    <span style="font-size: 8pt; color: #5C6B70;">FATEC Botucatu — Gestão e Triagem de Resíduos Recicláveis</span>
                </td>
                <td style="width: 30%; text-align: right; border: none; font-size: 8pt; color: #5C6B70;">
                    <strong>Relatório:</strong> Extrato Geral<br>
                    <strong>Emissão:</strong> <?= date('d/m/Y H:i') ?>
                </td>
            </tr>
        </table>
    </page_header>

    <!-- Rodapé com Paginação -->
    <page_footer>
        <table style="border-top: 1px solid #C9CECB; padding-top: 5px;">
            <tr>
                <td style="width: 50%; border: none; font-size: 8pt; color: #5C6B70;">
                    Período: <?= formatar_data_br($data_inicio) ?> a <?= formatar_data_br($data_fim) ?>
                </td>
                <td style="width: 50%; border: none; text-align: right; font-size: 8pt; color: #5C6B70;">
                    Página [[page_cu]] de [[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>

    <div style="margin-bottom: 15px;">
        <h2 style="font-size: 13pt; color: #1B2A30; margin: 0 0 4px 0; text-transform: uppercase;">
            Extrato Geral de Lançamentos de Coleta
        </h2>
        <div style="font-size: 8.5pt; color: #5C6B70;">
            Filtro: <?= $ecoponto_nome ? 'Unidade ' . e($ecoponto_nome) : 'Todas as Unidades' ?> |
            Registros encontrados: <?= count($coletas) ?>
        </div>
    </div>

    <!-- Tabela de Lançamentos -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 12%;">Data</th>
                <th style="width: 24%;">Ecoponto</th>
                <th style="width: 20%;">Material</th>
                <th style="width: 14%; text-align: right;">Peso Líquido</th>
                <th style="width: 22%;">Operador</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coletas)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #5C6B70;">
                        Nenhuma pesagem registrada no período especificado.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $acumulador_peso = 0.0;
                foreach ($coletas as $c): 
                    $acumulador_peso += (float) $c['peso_kg'];
                ?>
                    <tr>
                        <td class="text-center"><?= $c['id'] ?></td>
                        <td><?= formatar_data_br($c['data_coleta']) ?></td>
                        <td><?= e($c['ecoponto_nome']) ?></td>
                        <td><?= e($c['nome_material']) ?> (<?= e($c['nome_categoria']) ?>)</td>
                        <td class="text-right"><?= formatar_numero((float)$c['peso_kg'], 3) ?> kg</td>
                        <td style="font-size: 8pt;"><?= e($c['usuario_nome']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding: 7px;">TOTAL GERAL CONSOLIDADO:</td>
                    <td class="text-right" style="padding: 7px;"><?= formatar_numero($acumulador_peso, 3) ?> kg</td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</page>
