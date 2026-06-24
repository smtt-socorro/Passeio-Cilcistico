<?php
session_start();
require_once '../config/functions.php';
// Define o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Se já está logado, redirecionar para dashboard
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: index.php');
    exit();
}

$page_title = "Login Administrativo";
$error_message = '';

// Processar login
if ($_POST) {
    try {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validações básicas
        if (empty($username) || empty($password)) {
            throw new Exception('Todos os campos são obrigatórios');
        }
        
        if (strlen($username) < 3) {
            throw new Exception('Nome de usuário deve ter pelo menos 3 caracteres');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Senha deve ter pelo menos 6 caracteres');
        }
        
        // Buscar usuário no banco
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM admin_users WHERE username = :username AND status = 'ativo'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Log da tentativa de login inválida
            logAtividade('Login inválido', "Username: {$username}, IP: {$_SERVER['REMOTE_ADDR']}");
            
            // Incrementar tentativas de login
            $query = "UPDATE admin_users SET login_attempts = login_attempts + 1, last_login_attempt = NOW() WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            throw new Exception('Usuário ou senha inválidos');
        }
        
        // Verificar se conta não está bloqueada
        if ($user['login_attempts'] >= 5) {
            $lastAttempt = new DateTime($user['last_login_attempt']);
            $now = new DateTime();
            $diff = $now->diff($lastAttempt);
            
            // Bloquear por 15 minutos após 5 tentativas
            if ($diff->i < 15) {
                throw new Exception('Conta temporariamente bloqueada. Tente novamente em alguns minutos.');
            }
        }
        
        // Login bem-sucedido
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_nome'] = $user['nome'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_login_time'] = time();
        
        // Resetar tentativas de login
        $query = "UPDATE admin_users SET login_attempts = 0, last_login = NOW() WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();
        
        // Log do login bem-sucedido
        logAtividade('Login bem-sucedido', "Admin: {$user['nome']}, IP: {$_SERVER['REMOTE_ADDR']}");
        
        // Cookie "lembrar-me" (opcional)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('admin_remember', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 dias
            
            // Salvar token no banco (implementar tabela remember_tokens se necessário)
        }
        
        // Redirecionar para dashboard
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        
        // Redirecionar com erro para evitar resubmissão
        $error_param = '';
        if (strpos($error_message, 'obrigatórios') !== false) {
            $error_param = 'required';
        } elseif (strpos($error_message, 'inválidos') !== false) {
            $error_param = 'invalid';
        } elseif (strpos($error_message, 'bloqueada') !== false) {
            $error_param = 'blocked';
        }
        
        if ($error_param) {
            header("Location: login.php?error={$error_param}");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Evento Bike Socorro</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>

<div class="login-page">
    <div class="login-container">
        
        <!-- Header do Login -->
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-bicycle"></i>
            </div>
            <p class="login-subtitle">Get in Touch</p>
            <h1 class="login-title">Admin Access</h1>
            <p class="login-description">
                Acesse o painel administrativo do evento de ciclismo
            </p>
        </div>

        <!-- Mensagem de Erro -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form id="loginForm" method="POST" class="login-form">
            
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    Nome de Usuário
                </label>
                <input type="text" id="username" name="username" 
                       placeholder="Digite seu nome de usuário" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required autocomplete="username">
                <div class="field-error"></div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Senha
                </label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" 
                           placeholder="Digite sua senha" 
                           required autocomplete="current-password">
                    <button type="button" class="password-toggle" title="Mostrar senha">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="field-error"></div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Lembrar-me por 30 dias</label>
            </div>

            <button type="submit" id="loginButton" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Entrar no Sistema
            </button>

        </form>

        <!-- Informações de Segurança -->
        <div class="security-info">
            <h4>
                <i class="fas fa-shield-alt"></i>
                Acesso Seguro
            </h4>
            <p>
                Este é um sistema protegido. Todas as tentativas de acesso são monitoradas e registradas.
            </p>
        </div>

        <!-- Link Voltar -->
        <div class="back-link">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i>
                Voltar ao Site Principal
            </a>
        </div>

    </div>
</div>

<!-- JavaScript -->
<script src="assets/js/login.js"></script>

</body>
</html>
