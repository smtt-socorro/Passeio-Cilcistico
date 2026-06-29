<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = "Gerenciar Usuários Admin";

// Processar ações
if ($_POST) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $username = sanitize($_POST['username']);
                $nome_completo = sanitize($_POST['nome_completo']);
                $email = sanitize($_POST['email']);
                $password = $_POST['password'];
                
                if (empty($username) || empty($nome_completo) || empty($email) || empty($password)) {
                    throw new Exception('Todos os campos são obrigatórios');
                }
                
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $query = "INSERT INTO admin_users (username, nome_completo, email, password_hash, status) VALUES (?, ?, ?, ?, 'ativo')";
                $stmt = $conn->prepare($query);
                $stmt->execute([$username, $nome_completo, $email, $password_hash]);
                
                $success_message = "Usuário criado com sucesso!";
                break;
                
            case 'update':
                $id = $_POST['id'];
                $username = sanitize($_POST['username']);
                $nome_completo = sanitize($_POST['nome_completo']);
                $email = sanitize($_POST['email']);
                $status = $_POST['status'];
                
                $query = "UPDATE admin_users SET username = ?, nome_completo = ?, email = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$username, $nome_completo, $email, $status, $id]);
                
                // Atualizar senha se fornecida
                if (!empty($_POST['password'])) {
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $query = "UPDATE admin_users SET password_hash = ? WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$password_hash, $id]);
                }
                
                $success_message = "Usuário atualizado com sucesso!";
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $query = "UPDATE admin_users SET status = 'inativo' WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                
                $success_message = "Usuário desativado com sucesso!";
                break;
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Buscar usuários
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM admin_users ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
    
} catch (Exception $e) {
    $usuarios = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Pedala Socorro 2026</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-index.css">
    <link rel="stylesheet" href="assets/css/usuarios.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-dashboard">
    <!-- Header Admin -->
    <header class="admin-header">
        <nav class="admin-nav">
            <a href="index.php" class="admin-logo">
                <i class="fas fa-bicycle"></i>
                Admin - Pedala Socorro 2026
            </a>
            
            <div class="admin-user-menu">
                <div class="admin-user-info">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_nome'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="admin-user-details">
                        <strong><?php echo htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador'); ?></strong>
                        <span>Administrador do Sistema</span>
                    </div>
                </div>
                
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </nav>
    </header>

    <!-- Container Principal -->
    <div class="admin-container">
        
        <!-- Header da Página -->
        <div class="page-header">
            <div class="page-title-section">
                <h1 class="page-title">Gerenciar Usuários Admin</h1>
                <p class="page-subtitle">Criar, editar e gerenciar usuários administrativos do sistema</p>
            </div>
            
            <div class="page-actions">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao Dashboard
                </a>
                <button class="btn-primary" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i>
                    Novo Usuário
                </button>
            </div>
        </div>

        <!-- Mensagens -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Tabela de Usuários -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Usuários Administrativos</h3>
                <span class="table-count"><?php echo count($usuarios); ?> usuário(s)</span>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Último Login</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-users"></i>
                                <h3>Nenhum usuário encontrado</h3>
                                <p>Clique em "Novo Usuário" para criar o primeiro usuário</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($usuario['username']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['nome_completo']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $usuario['status']; ?>">
                                        <?php echo ucfirst($usuario['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $usuario['last_login'] ? date('d/m/Y H:i', strtotime($usuario['last_login'])) : 'Nunca'; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editUser(<?php echo htmlspecialchars(json_encode($usuario)); ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($usuario['id'] != $_SESSION['admin_id']): ?>
                                            <button class="btn-action btn-delete" onclick="deleteUser(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['username']); ?>')" title="Desativar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal para Criar/Editar Usuário -->
<div id="userModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="userModalTitle">Novo Usuário</h3>
                <button class="modal-close" onclick="closeUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="userForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nome_completo">Nome Completo</label>
                            <input type="text" id="nome_completo" name="nome_completo" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password">
                            <small id="passwordHelp">Deixe em branco para manter a senha atual (apenas ao editar)</small>
                        </div>
                        
                        <div class="form-group" id="statusGroup" style="display: none;">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeUserModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="assets/js/usuarios.js"></script>

</body>
</html>
