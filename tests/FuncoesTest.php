<?php
/**
 * EcoTrack Analytics — Suíte de Testes Unitários das Funções Puras (PHPUnit)
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/funcoes.php';

class FuncoesTest extends TestCase {

    /**
     * Testa o cálculo de impacto ambiental (CO2 evitado e economia hídrica).
     */
    public function test_calcular_impacto_aluminio(): void {
        // Alumínio: fator_co2 = 10.03, fator_agua = 7.60
        $impacto = calcular_impacto(10.0, 10.0300, 7.60);
        $this->assertSame(100.3, $impacto['co2_kg']);
        $this->assertSame(76.0, $impacto['agua_litros']);
    }

    public function test_calcular_impacto_papelao(): void {
        // Papelão: fator_co2 = 3.14, fator_agua = 98.00
        $impacto = calcular_impacto(2.5, 3.1400, 98.00);
        $this->assertSame(7.85, $impacto['co2_kg']);
        $this->assertSame(245.0, $impacto['agua_litros']);
    }

    public function test_calcular_impacto_peso_zero(): void {
        $impacto = calcular_impacto(0.0, 1.1100, 2.50);
        $this->assertSame(0.0, $impacto['co2_kg']);
        $this->assertSame(0.0, $impacto['agua_litros']);
    }

    public function test_calcular_impacto_peso_negativo_tratado(): void {
        $impacto = calcular_impacto(-15.0, 1.1100, 2.50);
        $this->assertSame(0.0, $impacto['co2_kg']);
        $this->assertSame(0.0, $impacto['agua_litros']);
    }

    /**
     * Testa o cálculo da taxa percentual de ocupação conforme fórmula do ADR-001:
     * Taxa (%) = volume em kg ÷ (capacidade_toneladas * 1000) * 100.
     */
    public function test_calcular_taxa_ocupacao_normal(): void {
        // Ecoponto com 5 toneladas = 5000 kg. Volume recebido = 2500 kg. Taxa = 50%
        $taxa = calcular_taxa_ocupacao(2500.0, 5.0);
        $this->assertSame(50.0, $taxa);
    }

    public function test_calcular_taxa_ocupacao_ciclo_superior_a_100(): void {
        // Volume recebido = 7500 kg em unidade de 5t = 150%
        $taxa = calcular_taxa_ocupacao(7500.0, 5.0);
        $this->assertSame(150.0, $taxa);
    }

    public function test_calcular_taxa_ocupacao_capacidade_zero(): void {
        $taxa = calcular_taxa_ocupacao(100.0, 0.0);
        $this->assertSame(0.0, $taxa);
    }

    /**
     * Testa validação de formatos de data.
     */
    public function test_validar_data_valida_iso(): void {
        $this->assertTrue(validar_data_formato('2026-05-18'));
        $this->assertTrue(validar_data_formato('2025-12-31'));
    }

    public function test_validar_data_invalida_mes(): void {
        $this->assertFalse(validar_data_formato('2026-13-01'));
    }

    public function test_validar_data_invalida_dia(): void {
        $this->assertFalse(validar_data_formato('2026-02-30'));
    }

    public function test_validar_data_formato_incorreto(): void {
        $this->assertFalse(validar_data_formato('18/05/2026'));
        $this->assertFalse(validar_data_formato('texto'));
        $this->assertFalse(validar_data_formato(''));
    }

    /**
     * Testa formatação de números no padrão brasileiro.
     */
    public function test_formatar_numero_padrao_br(): void {
        $this->assertSame('1.250,50', formatar_numero(1250.50, 2));
        $this->assertSame('10.000,000', formatar_numero(10000.0, 3));
        $this->assertSame('0,00', formatar_numero(0.0, 2));
    }

    /**
     * Testa formatação de data para o padrão brasileiro.
     */
    public function test_formatar_data_br(): void {
        $this->assertSame('18/05/2026', formatar_data_br('2026-05-18'));
        $this->assertSame('01/11/2025', formatar_data_br('2025-11-01 14:30:00'));
        $this->assertSame('—', formatar_data_br(null));
    }

    /**
     * Testa escape seguro contra XSS.
     */
    public function test_escape_html_xss(): void {
        $sujo = '<script>alert("xss")</script>';
        $limpo = e($sujo);
        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $limpo);
    }

    /**
     * Testa codificação de JSON com flags de segurança.
     */
    public function test_codificar_json_seguro_caracteres_perigosos(): void {
        $dados = ['tag' => '<script>', 'amp' => '&', 'aspas' => '"'];
        $json = codificar_json_seguro($dados);
        $this->assertStringNotContainsString('<', $json);
        $this->assertStringNotContainsString('>', $json);
        $this->assertStringNotContainsString('&', $json);
    }
}
