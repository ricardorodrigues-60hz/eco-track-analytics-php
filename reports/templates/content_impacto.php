<!-- Template HTML2PDF: Balanço Consolidado de Impacto Ambiental -->
<style>
    table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 8.5pt; }
    th { background-color: #1B2A30; color: #FFFFFF; font-weight: bold; text-align: left; padding: 6px 5px; }
    td { padding: 5px; border-bottom: 1px solid #C9CECB; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row { background-color: #E6E8E5; font-weight: bold; }
    .alert-box { background-color: #FFF3CD; border-left: 3px solid #8F6300; padding: 6px 10px; font-size: 8pt; color: #664D03; margin-bottom: 12px; }
</style>

<page backtop="30mm" backbottom="15mm" backleft="10mm" backright="10mm">
    <!-- Cabeçalho Institucional -->
    <page_header>
        <table style="border-bottom: 2px solid #1B2A30; padding-bottom: 8px;">
            <tr>
                <td style="width: 70%; border: none;">
                    <span style="font-size: 15pt; font-weight: bold; color: #1B2A30;">EcoTrack Analytics</span><br>
                    <span style="font-size: 8pt; color: #5C6B70;">FATEC Botucatu — Auditoria de Impacto e Balanço Ambiental</span>
                </td>
                <td style="width: 30%; text-align: right; border: none; font-size: 8pt; color: #5C6B70;">
                    <strong>Relatório:</strong> Balanço de Impacto<br>
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

    <div style="margin-bottom: 12px;">
        <h2 style="font-size: 13pt; color: #1B2A30; margin: 0 0 4px 0; text-transform: uppercase;">
            Balanço Consolidado de Mitigação Ambiental
        </h2>
        <div style="font-size: 8.5pt; color: #5C6B70;">
            Demonstrativo de emissões evitadas de CO₂ equivalente e economia de recursos hídricos.
        </div>
    </div>

    <?php if (defined('FATORES_PROVISORIOS') && FATORES_PROVISORIOS === true): ?>
        <div class="alert-box">
            <strong>Aviso:</strong> Fatores preliminares sem fonte bibliográfica oficial associada.
        </div>
    <?php endif; ?>

    <!-- Tabela de Impacto por Material -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Material</th>
                <th style="width: 15%;">Categoria</th>
                <th style="width: 15%; text-align: right;">Peso Reciclado</th>
                <th style="width: 14%; text-align: right;">Fator CO₂e</th>
                <th style="width: 18%; text-align: right;">CO₂ Evitado</th>
                <th style="width: 18%; text-align: right;">Água Poupada</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($impacto_materiais)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #5C6B70;">
                        Nenhum registro no período especificado.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $total_peso = 0.0;
                $total_co2  = 0.0;
                $total_agua = 0.0;

                foreach ($impacto_materiais as $im): 
                    $total_peso += (float)$im['total_peso_kg'];
                    $total_co2  += (float)$im['total_co2_kg'];
                    $total_agua += (float)$im['total_agua_litros'];
                ?>
                    <tr>
                        <td style="font-weight: bold;"><?= e($im['nome_material']) ?></td>
                        <td><?= e($im['nome_categoria']) ?></td>
                        <td class="text-right"><?= formatar_numero((float)$im['total_peso_kg'], 2) ?> kg</td>
                        <td class="text-right" style="color: #5C6B70;"><?= formatar_numero((float)$im['fator_co2_kg'], 4) ?></td>
                        <td class="text-right" style="font-weight: bold; color: #1B6A45;">
                            <?= formatar_numero((float)$im['total_co2_kg'], 2) ?> kg
                        </td>
                        <td class="text-right" style="font-weight: bold; color: #005EB8;">
                            <?= formatar_numero((float)$im['total_agua_litros'], 0) ?> L
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right; padding: 7px;">TOTAL IMPACTO CONSOLIDADO:</td>
                    <td class="text-right" style="padding: 7px;"><?= formatar_numero($total_peso, 2) ?> kg</td>
                    <td></td>
                    <td class="text-right" style="padding: 7px; color: #1B6A45;"><?= formatar_numero($total_co2, 2) ?> kg</td>
                    <td class="text-right" style="padding: 7px; color: #005EB8;"><?= formatar_numero($total_agua, 0) ?> L</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Quadro de Referências Bibliográficas das Fontes Citáveis -->
    <div style="margin-top: 25px; border-top: 1px dashed #C9CECB; padding-top: 10px;">
        <span style="font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #1B2A30; display: block; margin-bottom: 5px;">
            Fundamentação Metodológica das Fontes Citadas:
        </span>
        <div style="font-size: 7.5pt; color: #5C6B70; line-height: 1.4;">
            • <strong>CO₂ Evitado (Gases de Efeito Estufa):</strong> EPA (2023). <em>Waste Reduction Model (WARM)</em> Version 16, U.S. Environmental Protection Agency. Fatores médios por substituição de matéria virgem.<br>
            • <strong>Economia Hídrica (PET, Vidro, Alumínio):</strong> WWF Brasil (2008). <em>Relatório Planeta Vivo Brasil 2008</em>.<br>
            • <strong>Economia Hídrica (Papelão):</strong> Recicla Sampa (2018). <em>Caderno de Educação Ambiental</em>, Prefeitura de SP / AMLURB.
        </div>
    </div>
</page>
