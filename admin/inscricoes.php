<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Parâmetros de busca e paginação
$busca = isset($_GET['busca']) ? sanitize($_GET['busca']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Construir query de busca
$where_conditions = ["1=1"];
$params = [];

if (!empty($busca)) {
    $where_conditions[] = "(nome_completo LIKE :busca OR cpf LIKE :busca_cpf OR id_inscricao_formatado LIKE :busca_id OR email LIKE :busca_email)";
    $busca_param = '%' . $busca . '%';
    $params[':busca'] = $busca_param;
    $params[':busca_cpf'] = $busca_param;
    $params[':busca_id'] = $busca_param;
    $params[':busca_email'] = $busca_param;
}

if (!empty($status)) {
    $where_conditions[] = "status = :status";
    $params[':status'] = $status;
}

$where_clause = implode(' AND ', $where_conditions);

// Contar total de registros
$count_query = "SELECT COUNT(*) as total FROM inscricoes WHERE $where_clause";
$count_stmt = $conn->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_records = $count_stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// Buscar inscrições
$query = "SELECT * FROM inscricoes WHERE $where_clause ORDER BY data_inscricao DESC LIMIT :offset, :per_page";
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();
$inscricoes = $stmt->fetchAll();

// Processar ações (excluir, ativar, etc.)
if ($_POST) {
    try {
        $action = $_POST['action'];
        $inscricao_id = $_POST['inscricao_id'];
        
        switch ($action) {
            case 'delete':
                $update_query = "UPDATE inscricoes SET status = 'cancelada' WHERE id = :id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bindParam(':id', $inscricao_id);
                $update_stmt->execute();
                $success_message = "Inscrição cancelada com sucesso!";
                break;
                
            case 'activate':
                $update_query = "UPDATE inscricoes SET status = 'ativa' WHERE id = :id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bindParam(':id', $inscricao_id);
                $update_stmt->execute();
                $success_message = "Inscrição ativada com sucesso!";
                break;
        }
    } catch (Exception $e) {
        $error_message = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Inscrições - Painel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filters-section {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
        }
        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 20px;
            align-items: end;
        }
        .table-container {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background: var(--gray-light);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-dark);
            border-bottom: 1px solid #dee2e6;
        }
        .table td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background: var(--light-blue);
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-ativa {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelada {
            background: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
        }
        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            color: var(--gray-dark);
            border: 1px solid #dee2e6;
            border-radius: 6px;
            transition: var(--transition);
        }
        .pagination a:hover, .pagination a.active {
            background: var(--accent-blue);
            color: var(--white);
            border-color: var(--accent-blue);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Admin -->
        <header class="admin-header">
            <div>
                <h1 style="color: var(--accent-blue); margin-bottom: 5px;">
                    <i class="fas fa-users"></i> Gerenciar Inscrições
                </h1>
                <p style="color: var(--gray-medium); margin: 0;">
                    Total: <?php echo $total_records; ?> inscrições
                </p>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="export.php" class="btn btn-primary">
                    <i class="fas fa-download"></i> Exportar
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </header>

        <!-- Mensagens -->
        <?php if (isset($success_message)): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="filters-section">
            <form method="GET" class="filters-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="busca">Buscar</label>
                    <input type="text" class="form-control" id="busca" name="busca" 
                           placeholder="Nome, CPF, ID ou Email" value="<?php echo htmlspecialchars($busca); ?>">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="ativa" <?php echo $status === 'ativa' ? 'selected' : ''; ?>>Ativa</option>
                        <option value="cancelada" <?php echo $status === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Tabela de Inscrições -->
        <div class="table-container">
            <?php if (empty($inscricoes)): ?>
                <div style="text-align: center; padding: 60px; color: var(--gray-medium);">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                    <h3>Nenhuma inscrição encontrada</h3>
                    <p>Tente ajustar os filtros de busca</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Email</th>
                            <th>Idade</th>
                            <th>Data de Inscrição</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inscricoes as $inscricao): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--accent-blue);">
                                        <?php echo htmlspecialchars($inscricao['id_inscricao_formatado']); ?>
                                    </strong>
                                </td>
                                <td><?php echo htmlspecialchars($inscricao['nome_completo']); ?></td>
                                <td><?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $inscricao['cpf']); ?></td>
                                <td><?php echo htmlspecialchars($inscricao['email']); ?></td>
                                <td><?php echo calcularIdade($inscricao['data_nascimento']); ?> anos</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $inscricao['status']; ?>">
                                        <?php echo ucfirst($inscricao['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="viewInscricao(<?php echo $inscricao['id']; ?>)" 
                                                class="btn btn-sm btn-secondary" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($inscricao['status'] === 'ativa'): ?>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Cancelar esta inscrição?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['id']; ?>">
                                                <button type="submit" class="btn btn-sm" 
                                                        style="background: var(--danger); color: white;" title="Cancelar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="inscricao_id" value="<?php echo $inscricao['id']; ?>">
                                                <button type="submit" class="btn btn-sm" 
                                                        style="background: var(--success); color: white;" title="Ativar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page-1; ?>&busca=<?php echo urlencode($busca); ?>&status=<?php echo urlencode($status); ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&status=<?php echo urlencode($status); ?>" 
                       class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page+1; ?>&busca=<?php echo urlencode($busca); ?>&status=<?php echo urlencode($status); ?>">
                        Próxima <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de Visualização -->
    <div id="viewModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title">Detalhes da Inscrição</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="viewModalContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>

    <script>
        function viewInscricao(id) {
            // Mostrar modal
            document.getElementById('viewModal').style.display = 'block';
            document.getElementById('viewModalContent').innerHTML = '<p style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Carregando...</p>';
            
            // Carregar dados via AJAX
            fetch('view_inscricao.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('viewModalContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('viewModalContent').innerHTML = '<p style="color: var(--danger);">Erro ao carregar dados</p>';
                });
        }

        // Modal controls
        document.querySelector('.modal-close').addEventListener('click', function() {
            document.getElementById('viewModal').style.display = 'none';
        });

        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</body>
</html>
