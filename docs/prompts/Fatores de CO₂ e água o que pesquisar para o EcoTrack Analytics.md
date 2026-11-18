# Fatores de CO₂ e água: o que pesquisar para o EcoTrack Analytics

Guia para definir a fonte bibliográfica dos fatores ambientais da tabela `materiais`.

## O que são esses fatores

São dois números por material, guardados na tabela `materiais`. O sistema os multiplica pelo peso de cada coleta para mostrar o impacto ambiental.

- **`fator_co2_kg`:** quantos kg de CO₂ equivalente (CO₂e) deixam de ser emitidos por **cada 1 kg reciclado** daquele material, em vez de produzi-lo a partir de matéria-prima virgem. No sistema: `peso_kg × fator_co2_kg` dá o CO₂ evitado da coleta.
- **`fator_agua_litros`:** quantos litros de água são economizados por **cada 1 kg reciclado**, pelo mesmo raciocínio.

É um fator por material (PET, papelão, alumínio, vidro e assim por diante). Sem ele, o dashboard e os relatórios de impacto não têm base científica, e o artigo fica sem a justificativa que a banca vai pedir.

## O que você precisa encontrar

Para cada material cadastrado, dois valores com origem citável (autor, ano e documento), mais a unidade original, para converter para kg e litros por kg.

### Onde procurar

**Emissões evitadas (CO₂e)**

- **EPA WARM** (Waste Reduction Model): candidato que apareceu na busca. Fornece fatores de gases de efeito estufa por tonelada curta, com dados dos EUA. Os valores individuais não foram conferidos.
- **Estudos de Avaliação do Ciclo de Vida (ACV) brasileiros:** buscar por "reciclagem", "emissões evitadas" e o material.
- **Dados setoriais:** CEMPRE, associações como ABAL (alumínio) e ABIVIDRO, e trabalhos acadêmicos.

**Água economizada**

Costuma ser mais difícil de achar. Busque por "pegada hídrica" ou "water footprint" do material e por "consumo de água reciclagem versus matéria-prima virgem". Pode ser necessário usar uma fonte diferente da de CO₂, o que é aceitável se estiver documentado.

### Termos úteis de busca

- `fator de emissão evitada reciclagem kg CO2 por kg`
- `ACV reciclagem PET alumínio vidro papel Brasil`
- `pegada hídrica reciclagem papel plástico`
- `WARM emission factors recycling` (em inglês)

## Cuidados para o artigo

1. **Use poucas fontes e fontes consistentes.** Misturar metodologias diferentes por material compromete a comparação entre eles.
2. **Confira se o fator é "evitado".** O valor deve comparar a reciclagem com a produção virgem. Fatores de emissão de produção, sem comparação, são outra coisa.
3. **Converta as unidades com cuidado.** Fontes americanas usam toneladas curtas (1 tonelada curta = 907,18 kg), então converta para kg antes de gravar.
4. **Registre tudo em `fonte_referencia`** e liste cada fonte nas Referências do artigo.
5. **Assuma a limitação no texto.** Escreva que os valores são estimativas médias e que o impacto real varia com a tecnologia e a região.

## Tabela para preencher

| Material | CO₂ evitado (kg/kg) | Água economizada (L/kg) | Fonte (autor, ano) |
| --- | --- | --- | --- |
| PET | 1,11 | 2,50 | **EPA WARM** (2023) / **WWF Brasil** (2008) |
| Papelão | 3,14 | 98,00 | **EPA WARM** (2023) / **Recicla Sampa** (2018) |
| Alumínio | 10,03 | **7,60** | **EPA WARM** (2023) / **WWF Brasil** (2008) |
| Vidro | 0,28 | **1,50** | **EPA WARM** (2023) / **WWF Brasil** (2008) |

## Próximo passo

Quando a tabela estiver preenchida, o agente atualiza o `seed.sql` com os valores e as fontes e desliga o aviso de "fatores provisórios" (`FATORES_PROVISORIOS = false`).
