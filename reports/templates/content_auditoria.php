<!-- Template HTML2PDF: Auditoria de Lançamentos por Operador -->
<style>
    table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 8pt; }
    th { background-color: #1B2A30; color: #FFFFFF; font-weight: bold; text-align: left; padding: 6px 4px; }
    td { padding: 5px 4px; border-bottom: 1px solid #C9CECB; }
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
                    <span style="font-size: 8pt; color: #5C6B70;">FATEC Botucatu — Trilha de Auditoria e Conformidade de Dados</span>
                </td>
                <td style="width: 30%; text-align: right; border: none; font-size: 8pt; color: #5C6B70;">
                    <strong>Relatório:</strong> Trilha de Auditoria<br>
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
            Auditoria de Lançamentos e Retificações de Pesagem
        </h2>
        <div style="font-size: 8.5pt; color: #5C6B70;">
            Rastreamento de operadores, carimbo de data/hora de criação (created_at) e última modificação (updated_at).
        </div>
    </div>

    <!-- Tabela de Auditoria -->
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 10%;">Data Coleta</th>
                <th style="width: 18%;">Operador</th>
                <th style="width: 20%;">Ecoponto</th>
                <th style="width: 16%;">Material</th>
                <th style="width: 10%; text-align: right;">Peso</th>
                <th style="width: 10%;">Criado em</th>
                <th style="width: 10%;">Atualizado em</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($coletas_auditoria)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #5C6B70;">
                        Nenhum registro localizado no período e operador selecionados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($coletas_auditoria as $ca): ?>
                    <?php 
                    $foi_modificado = ($ca['created_at'] !== $ca['updated_at']);
                    ?>
                    <tr style="<?= $foi_modificado ? 'background-color: #FFF9E6;' : '' ?>">
                        <td class="text-center"><?= $ca['id'] ?></td>
                        <td><?= formatar_data_br($ca['data_coleta']) ?></td>
                        <td style="font-weight: bold;"><?= e($ca['usuario_nome']) ?></td>
                        <td><?= e($ca['ecoponto_nome']) ?></td>
                        <td><?= e($ca['nome_material']) ?></td>
                        <td class="text-right"><?= formatar_numero((float)$ca['peso_kg'], 3) ?> kg</td>
                        <td style="font-size: 7pt; color: #5C6B70;"><?= formatar_data_hora_br($ca['created_at']) ?></td>
                        <td style="font-size: 7pt; font-weight: <?= $foi_modificado ? 'bold' : 'normal' ?>; color: <?= $foi_modificado ? '#8F6300' : '#5C6B70' ?>;">
                            <?= formatar_data_hora_br($ca['updated_at']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 7.5pt; color: #5C6B70;">
        * Linhas destacadas em amarelo indicam registros retificados após a pesagem original (updated_at posterior ao created_at).
    </div>
</page>
