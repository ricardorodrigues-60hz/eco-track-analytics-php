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
    <!-- Barra de Navegação Inferior Fixa (Mobile) -->
    <nav class="et-bottom-nav" aria-label="Navegação Rápida Mobile">
        <a href="/eco-track-analytics-php/dashboard/index.php" class="et-bottom-nav__item <?= str_contains($pagina_atual, '/dashboard/') ? 'active' : '' ?>">
            <span style="font-size:1.2rem;">📊</span>
            <span>Painel</span>
        </a>

        <!-- Botão Central em Destaque: Registrar Coleta -->
        <a href="/eco-track-analytics-php/modules/coletas/form.php" class="et-bottom-nav__item et-bottom-nav__item--destaque">
            <span style="font-size:1.35rem; line-height:1;">⚖️</span>
            <span>Coletar</span>
        </a>

        <a href="/eco-track-analytics-php/modules/coletas/index.php" class="et-bottom-nav__item <?= str_contains($pagina_atual, '/coletas/index.php') ? 'active' : '' ?>">
            <span style="font-size:1.2rem;">📋</span>
            <span>Histórico</span>
        </a>

        <?php if ($usuario_logado['perfil'] === 'admin'): ?>
            <a href="/eco-track-analytics-php/reports/index.php" class="et-bottom-nav__item <?= str_contains($pagina_atual, '/reports/') ? 'active' : '' ?>">
                <span style="font-size:1.2rem;">📄</span>
                <span>Relatórios</span>
            </a>
        <?php endif; ?>

        <a href="/eco-track-analytics-php/auth/logout.php" class="et-bottom-nav__item" title="Sair da Conta">
            <span style="font-size:1.2rem;">🚪</span>
            <span>Sair</span>
        </a>
    </nav>
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS (Popper incluso) via CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
