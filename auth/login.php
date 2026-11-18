<?php
/**
 * EcoTrack Analytics — Autenticação de Usuários
 * 
 * Regras de Segurança:
 * - Validação obrigatória de CSRF em todo POST
 * - Bloqueio temporário de 15 minutos após 5 tentativas consecutivas com falha
 * - Mensagens opacas de erro (não revelam existência de contas)
 * - session_regenerate_id(true) após login bem-sucedido
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/funcoes.php';

// Se já estiver logado, redireciona diretamente ao painel
if (esta_logado()) {
    header('Location: /eco-track-analytics-php/dashboard/index.php');
    exit;
}

$erro = '';
$email_digitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $email_digitado = trim($_POST['email'] ?? '');
    $senha_digitada = (string) ($_POST['senha'] ?? '');

    if ($email_digitado === '' || $senha_digitada === '') {
        $erro = 'Informe o e-mail e a senha cadastrados para prosseguir.';
    } else {
        try {
            $pdo = obter_pdo();
            
            // Busca o usuário pelo e-mail
            $stmt = $pdo->prepare('SELECT id, nome, email, senha, perfil, ativo, tentativas_login, bloqueado_ate FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email_digitado]);
            $usuario = $stmt->fetch();

            $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $esta_bloqueado = false;

            if ($usuario && !empty($usuario['bloqueado_ate'])) {
                $bloqueio_fim = new DateTime($usuario['bloqueado_ate'], new DateTimeZone('America/Sao_Paulo'));
                if ($agora < $bloqueio_fim) {
                    $esta_bloqueado = true;
                    $minutos_restantes = (int) ceil(($bloqueio_fim->getTimestamp() - $agora->getTimestamp()) / 60);
                    $erro = "Conta temporariamente bloqueada por excesso de tentativas. Tente novamente em {$minutos_restantes} minuto(s).";
                }
            }

            if (!$esta_bloqueado) {
                $login_valido = false;

                if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
                    if ((int) $usuario['ativo'] !== 1) {
                        $erro = 'Acesso não permitido: esta conta de usuário encontra-se inativa.';
                    } else {
                        $login_valido = true;
                    }
                }

                if ($login_valido) {
                    // Reseta contador de tentativas
                    $stmt_reset = $pdo->prepare('UPDATE usuarios SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = :id');
                    $stmt_reset->execute([':id' => $usuario['id']]);

                    // Regenera ID da sessão para prevenir Session Fixation
                    session_regenerate_id(true);

                    $_SESSION['usuario_id']     = (int) $usuario['id'];
                    $_SESSION['usuario_nome']   = (string) $usuario['nome'];
                    $_SESSION['usuario_email']  = (string) $usuario['email'];
                    $_SESSION['usuario_perfil'] = (string) $usuario['perfil'];

                    $destino = !empty($_GET['redir']) ? $_GET['redir'] : '/eco-track-analytics-php/dashboard/index.php';
                    header('Location: ' . $destino);
                    exit;
                } else {
                    // Falha no login: incrementa tentativas se o usuário existir
                    if ($usuario) {
                        $novas_tentativas = (int) $usuario['tentativas_login'] + 1;
                        if ($novas_tentativas >= 5) {
                            $bloqueio = clone $agora;
                            $bloqueio->modify('+15 minutes');
                            $bloqueio_str = $bloqueio->format('Y-m-d H:i:s');

                            $stmt_bloq = $pdo->prepare('UPDATE usuarios SET tentativas_login = :t, bloqueado_ate = :b WHERE id = :id');
                            $stmt_bloq->execute([
                                ':t'  => $novas_tentativas,
                                ':b'  => $bloqueio_str,
                                ':id' => $usuario['id']
                            ]);
                            $erro = 'Conta bloqueada temporariamente após 5 tentativas incorretas. Aguarde 15 minutos.';
                        } else {
                            $stmt_tent = $pdo->prepare('UPDATE usuarios SET tentativas_login = :t WHERE id = :id');
                            $stmt_tent->execute([
                                ':t'  => $novas_tentativas,
                                ':id' => $usuario['id']
                            ]);
                            $restantes = 5 - $novas_tentativas;
                            $erro = "E-mail ou senha incorretos. Você possui mais {$restantes} tentativa(s) antes do bloqueio temporário.";
                        }
                    } else {
                        // Mensagem genérica se o e-mail não existir
                        $erro = 'E-mail ou senha incorretos. Verifique suas credenciais e tente novamente.';
                    }
                }
            }
        } catch (PDOException $e) {
            $erro = 'Falha ao processar autenticação com o banco de dados. Tente novamente mais tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Acesso ao Sistema — EcoTrack Analytics</title>
    
    <!-- Google Fonts: Barlow Condensed & Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Design System Mineral EcoTrack -->
    <link rel="stylesheet" href="/eco-track-analytics-php/assets/css/ecotrack.css">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: var(--et-concreto); padding: 1.5rem 1rem;">

    <div style="width: 100%; max-width: 440px;">
        
        <!-- Cabeçalho Institucional -->
        <div class="text-center mb-4">
            <h1 class="mb-0" style="font-family: 'Barlow Condensed', sans-serif; font-size: 2.8rem; letter-spacing: 0.05em; color: var(--et-tinta); line-height: 1;">
                EcoTrack
            </h1>
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--et-cinza); letter-spacing: 0.25em; text-transform: uppercase; margin-top: 0.25rem;">
                Analytics & Triagem
            </div>
        </div>

        <!-- Ticket de Autenticação -->
        <div class="et-ticket">
            <div class="et-ticket__header">
                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--et-cinza); letter-spacing: 0.05em;">Acesso ao Terminal</span>
                    <h2 style="font-size: 1.4rem; margin: 0; color: var(--et-tinta);">Identificação</h2>
                </div>
                <span style="font-size: 1.6rem;">🔒</span>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger mb-4 py-2" role="alert" style="background-color: #F8D7DA; border-color: var(--et-perigo); color: #721C24;">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/eco-track-analytics-php/auth/login.php<?= !empty($_GET['redir']) ? '?redir=' . urlencode($_GET['redir']) : '' ?>">
                <?= campo_csrf() ?>

                <div class="mb-3">
                    <label for="email" class="form-label" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: var(--et-tinta);">E-mail Cadastrado</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= e($email_digitado) ?>" required autofocus placeholder="seu.usuario@ecotrack.local" autocomplete="username">
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: var(--et-tinta);">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required placeholder="••••••••" autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2" style="font-size: 1rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                    Acessar Terminal
                </button>
            </form>

            <div class="mt-4 pt-3 text-center" style="border-top: 1px dashed var(--et-linha); font-size: 0.8rem; color: var(--et-cinza);">
                <div>FATEC Botucatu — Trabalho de Graduação</div>
                <div style="font-size: 0.75rem; margin-top: 0.25rem;">Credenciais padrão: <strong>admin@ecotrack.local</strong> ou <strong>coletor@ecotrack.local</strong></div>
            </div>
        </div>

    </div>

</body>
</html>
