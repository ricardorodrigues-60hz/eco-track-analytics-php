<!-- Template HTML2PDF: Produtividade e Ocupação por Ecoponto -->
<style>
    table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 8.5pt; }
    th { background-color: #1B2A30; color: #FFFFFF; font-weight: bold; text-align: left; padding: 6px 5px; }
    td { padding: 6px 5px; border-bottom: 1px solid #C9CECB; }
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
                    <span style="font-size: 8pt; color: #5C6B70;">FATEC Botucatu — Auditoria de Capacidade e Logística de Ecopontos</span>
                </td>
                <td style="width: 30%; text-align: right; border: none; font-size: 8pt; color: #5C6B70;">
                    <strong>Relatório:</strong> Produtividade & Ocupação<br>
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
            Produtividade e Taxa de Ocupação Operacional
        </h2>
        <div style="font-size: 8.5pt; color: #5C6B70;">
            Fórmula ADR-001: Taxa de Ocupação (%) = Volume em kg ÷ (Capacidade em toneladas × 1000) × 100.
        </div>
    </div>

    <!-- Tabela de Ecopontos -->
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 28%;">Unidade Ecoponto</th>
                <th style="width: 26%;">Endereço</th>
                <th style="width: 14%; text-align: right;">Capacidade</th>
                <th style="width: 14%; text-align: right;">Volume Período</th>
                <th style="width: 12%; text-align: right;">Ocupação (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ecopontos_dados)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #5C6B70;">
                        Nenhuma informação encontrada para o período.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $acum_cap_ton = 0.0;
                $acum_vol_kg  = 0.0;

                foreach ($ecopontos_dados as $ed): 
                    $acum_cap_ton += (float)$ed['capacidade_toneladas'];
                    $acum_vol_kg  += (float)$ed['volume_kg'];
                    $taxa = calcular_taxa_ocupacao((float)$ed['volume_kg'], (float)$ed['capacidade_toneladas']);
                ?>
                    <tr>
                        <td class="text-center"><?= $ed['id'] ?></td>
                        <td style="font-weight: bold;"><?= e($ed['nome_unidade']) ?></td>
                        <td style="font-size: 7.5pt; color: #5C6B70;"><?= e($ed['endereco']) ?></td>
                        <td class="text-right"><?= formatar_numero((float)$ed['capacidade_toneladas'], 2) ?> t</td>
                        <td class="text-right" style="font-weight: bold;"><?= formatar_numero((float)$ed['volume_kg'], 2) ?> kg</td>
                        <td class="text-right" style="font-weight: bold; color: <?= $taxa > 100.0 ? '#CC0000' : '#1B2A30' ?>;">
                            <?= formatar_numero($taxa, 2) ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; padding: 7px;">TOTAIS DA REDE DE ECOPONTOS:</td>
                    <td class="text-right" style="padding: 7px;"><?= formatar_numero($acum_cap_ton, 2) ?> t</td>
                    <td class="text-right" style="padding: 7px;"><?= formatar_numero($acum_vol_kg, 2) ?> kg</td>
                    <td class="text-right" style="padding: 7px;">
                        <?= formatar_numero(calcular_taxa_ocupacao($acum_vol_kg, $acum_cap_ton), 2) ?>%
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 7.5pt; color: #5C6B70; line-height: 1.4;">
        * <strong>Interpretação da Taxa:</strong> Valores superiores a 100% no período demonstram múltiplos ciclos de esvaziamento e destinação do ecoponto para centros de triagem e prensagem.
    </div>
</page>
