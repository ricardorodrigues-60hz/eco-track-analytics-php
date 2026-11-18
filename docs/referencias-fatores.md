# Referências dos Fatores Ambientais

Este documento consolida as fontes bibliográficas e metodológicas utilizadas para os campos `fator_co2_kg` (CO₂ evitado) e `fator_agua_litros` (água economizada) da tabela `materiais` no sistema **EcoTrack Analytics**.

---

## 1. Tabela de Fatores e Fontes

| Material | Categoria | CO₂ evitado (`fator_co2_kg`) | Água economizada (`fator_agua_litros`) | Fonte CO₂ | Fonte Água |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PET** | Plástico (`#CC0000`) | 1,1100 kg CO₂e / kg | 2,50 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |
| **Papelão** | Papel (`#005EB8`) | 3,1400 kg CO₂e / kg | 98,00 L / kg | EPA WARM v16 (2023) | Recicla Sampa (2018) |
| **Alumínio** | Metal (`#FFC000`) | 10,0300 kg CO₂e / kg | 7,60 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |
| **Vidro** | Vidro (`#007A33`) | 0,2800 kg CO₂e / kg | 1,50 L / kg | EPA WARM v16 (2023) | WWF Brasil (2008) |

---

## 2. Referências Bibliográficas Citáveis

### Emissões Evitadas de Gases de Efeito Estufa (CO₂e)
- **EPA (2023).** *Waste Reduction Model (WARM)*, Version 16. United States Environmental Protection Agency (U.S. EPA). Disponível em: <https://www.epa.gov/warm>.
  - *Nota metodológica:* Valores convertidos de toneladas métricas de carbono equivalente por tonelada curta (MTCO₂E/short ton) para quilogramas de CO₂ equivalente por quilograma de material reciclado (kg CO₂e/kg), considerando 1 short ton = 907,1847 kg.

### Economia de Recursos Hídricos (Água)
- **WWF Brasil (2008).** *Relatório Planeta Vivo Brasil 2008: Pegada Ecológica e Biocapacidade*. WWF-Brasil, Brasília/DF.
- **Recicla Sampa (2018).** *Caderno de Educação Ambiental e Gestão de Resíduos Sólidos*. Prefeitura do Município de São Paulo / Autoridade Municipal de Limpeza Urbana (AMLURB).

---

## 3. Limitações Metodológicas Declaradas para o Artigo

1. **Matriz Energética:** O modelo EPA WARM utiliza como linha de base a matriz energética norte-americana. Como a matriz elétrica brasileira possui maior proporção de fontes renováveis (hidrelétrica, biomassa, eólica), os valores de emissão evitada representam estimativas aproximadas que podem diferir ligeiramente de um estudo de Avaliação do Ciclo de Vida (ACV) específico para a indústria nacional.
2. **Fontes Distintas:** Devido à escassez de dados unificados de pegada hídrica e pegada de carbono para todos os tipos de materiais no mesmo estudo, os fatores de água provêm de referências setoriais diferentes dos fatores de emissão gasosa.
3. **Escopo Operacional:** Os fatores consideram valores médios consolidados de processos industriais de reciclagem contra a produção a partir de matéria-prima virgem, abstraindo variações operacionais de logística e transporte regional.
