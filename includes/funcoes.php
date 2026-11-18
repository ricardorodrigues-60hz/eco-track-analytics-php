<?php
/**
 * EcoTrack Analytics — Funções Utilitárias Puras
 * 
 * Regras: Funções pequenas, puras (sem efeitos colaterais), nomes claros,
 * comentários e documentação em português do Brasil.
 */

/**
 * Escapa uma string para saída segura em contextos HTML contra XSS.
 * 
 * @param string|null $valor
 * @return string
 */
function e(?string $valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/**
 * Calcula o impacto ambiental de uma pesagem em kg com base nos fatores unitários.
 * 
 * @param float $peso_kg Quantidade coletada em quilogramas.
 * @param float $fator_co2 Fator de emissão evitada em kg CO2e por kg de material reciclado.
 * @param float $fator_agua Fator de economia hídrica em litros por kg de material reciclado.
 * @return array{co2_kg: float, agua_litros: float}
 */
function calcular_impacto(float $peso_kg, float $fator_co2, float $fator_agua): array {
    $peso_valido = max(0.0, $peso_kg);
    return [
        'co2_kg'      => round($peso_valido * $fator_co2, 4),
        'agua_litros' => round($peso_valido * $fator_agua, 2)
    ];
}

/**
 * Calcula a taxa percentual de ocupação de um ecoponto no período.
 * Fórmula do ADR-001: volume em kg no período ÷ (capacidade_toneladas × 1000) × 100.
 * 
 * @param float $volume_kg Volume total recebido em quilogramas.
 * @param float $capacidade_toneladas Capacidade nominal da unidade em toneladas métricas.
 * @return float Taxa percentual (ex: 78.45).
 */
function calcular_taxa_ocupacao(float $volume_kg, float $capacidade_toneladas): float {
    if ($capacidade_toneladas <= 0.0) {
        return 0.0;
    }
    $capacidade_kg = $capacidade_toneladas * 1000.0;
    return round(($volume_kg / $capacidade_kg) * 100.0, 2);
}

/**
 * Valida se uma string representa uma data válida em um formato específico (padrão 'Y-m-d').
 * 
 * @param string $data Data a validar.
 * @param string $formato Formato esperado compatível com DateTime::createFromFormat.
 * @return bool
 */
function validar_data_formato(string $data, string $formato = 'Y-m-d'): bool {
    if (trim($data) === '') {
        return false;
    }
    $dt = DateTime::createFromFormat($formato, $data);
    return $dt !== false && $dt->format($formato) === $data;
}

/**
 * Formata um valor numérico decimal no padrão brasileiro (ex: 1.250,50).
 * 
 * @param float $valor
 * @param int $casas_decimais
 * @return string
 */
function formatar_numero(float $valor, int $casas_decimais = 2): string {
    return number_format($valor, $casas_decimais, ',', '.');
}

/**
 * Converte data de formato ISO ('YYYY-MM-DD') para exibição brasileira ('DD/MM/YYYY').
 * 
 * @param string|null $data_iso
 * @return string
 */
function formatar_data_br(?string $data_iso): string {
    if (empty($data_iso)) {
        return '—';
    }
    $dt = DateTime::createFromFormat('Y-m-d', substr($data_iso, 0, 10));
    return $dt ? $dt->format('d/m/Y') : (string) $data_iso;
}

/**
 * Converte timestamp completo ISO ('YYYY-MM-DD HH:MM:SS') para exibição brasileira com hora.
 * 
 * @param string|null $data_hora_iso
 * @return string
 */
function formatar_data_hora_br(?string $data_hora_iso): string {
    if (empty($data_hora_iso)) {
        return '—';
    }
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $data_hora_iso);
    return $dt ? $dt->format('d/m/Y H:i') : (string) $data_hora_iso;
}

/**
 * Converte uma estrutura de dados PHP para JSON seguro com flags recomendadas para injeção HTML/JS.
 * Converte recursivamente strings numéricas para float quando indicado.
 * 
 * @param mixed $dados
 * @return string
 */
function codificar_json_seguro($dados): string {
    return (string) json_encode(
        $dados,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    );
}
