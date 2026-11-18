<?php
/**
 * EcoTrack Analytics — Rodapé e Barra de Navegação Inferior
 * Renderiza: Fechamento de tags estruturais, Barra de Navegação Inferior Mobile,
 * Bootstrap 5 JS Bundle e scripts globais.
 */

$usuario_logado = obter_usuario_logado();
$pagina_atual = $_SERVER['SCRIPT_NAME'] ?? '';
?>
    </main>
</div><!-- /.et-app-layout -->

<?php if ($usuario_logado): ?>
    <!-- Barra de Navegação Inferior Fixa (Mobile — Máximo 4 destinos) -->
    <nav class="et-bottom-nav" aria-label="Navegação Principal Mobile">
        <a href="/eco-track-analytics-php/dashboard/index.php"
           class="et-bottom-nav__item <?= str_contains($pagina_atual, '/dashboard/') ? 'active' : '' ?>"
           <?= str_contains($pagina_atual, '/dashboard/') ? 'aria-current="page"' : '' ?>>
            <span class="et-bottom-nav__icon">📊</span>
            <span>Painel</span>
        </a>

        <!-- Botão Central em Destaque: Registrar Coleta (Formato Próprio) -->
        <a href="/eco-track-analytics-php/modules/coletas/form.php"
           class="et-bottom-nav__item et-bottom-nav__item--destaque <?= str_contains($pagina_atual, '/coletas/form.php') ? 'active' : '' ?>"
           <?= str_contains($pagina_atual, '/coletas/form.php') ? 'aria-current="page"' : '' ?>
           aria-label="Registrar nova pesagem de coleta">
            <span class="et-bottom-nav__icon" style="font-size:1.35rem; line-height:1;">⚖️</span>
            <span>Coletar</span>
        </a>

        <a href="/eco-track-analytics-php/modules/coletas/index.php"
           class="et-bottom-nav__item <?= (str_contains($pagina_atual, '/coletas/index.php') || (str_contains($pagina_atual, '/coletas/') && !str_contains($pagina_atual, 'form.php'))) ? 'active' : '' ?>"
           <?= (str_contains($pagina_atual, '/coletas/index.php') || (str_contains($pagina_atual, '/coletas/') && !str_contains($pagina_atual, 'form.php'))) ? 'aria-current="page"' : '' ?>>
            <span class="et-bottom-nav__icon">📋</span>
            <span><?= $usuario_logado['perfil'] === 'admin' ? 'Coletas' : 'Histórico' ?></span>
        </a>

        <?php if ($usuario_logado['perfil'] === 'admin'): ?>
            <a href="/eco-track-analytics-php/reports/index.php"
               class="et-bottom-nav__item <?= str_contains($pagina_atual, '/reports/') ? 'active' : '' ?>"
               <?= str_contains($pagina_atual, '/reports/') ? 'aria-current="page"' : '' ?>>
                <span class="et-bottom-nav__icon">📄</span>
                <span>Relatórios</span>
            </a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS (Popper incluso) via CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>

<!-- Scripts Globais do Sistema -->
<script src="<?= asset('assets/js/confirmar-acao.js') ?>" defer></script>

<!-- Scripts Específicos por Página -->
<?php if (!empty($scripts_especificos) && is_array($scripts_especificos)): ?>
    <?php foreach ($scripts_especificos as $script_item): ?>
        <script src="<?= asset($script_item) ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
