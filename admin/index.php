<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = "Dashboard Administrativo";

// Buscar métricas
try {
    $database = new Database();
    $conn = $database->getConnection();

    // Total de inscrições
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM inscricoes WHERE status = 'ativa'");
    $total_inscricoes = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Inscrições hoje
    $stmt = $conn->query("SELECT COUNT(*) AS hoje FROM inscricoes WHERE data_inscricao::date = CURRENT_DATE AND status = 'ativa'");
    $inscricoes_hoje = (int) $stmt->fetch(PDO::FETCH_ASSOC)['hoje'];

    // Total de bairros cadastrados
    $stmt = $conn->query("
        SELECT COUNT(DISTINCT bairro) AS total
        FROM inscricoes
        WHERE status = 'ativa'
          AND bairro IS NOT NULL
          AND TRIM(bairro) <> ''
    ");
    $bairros_cadastrados = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Bairros mais cadastrados
    $stmt = $conn->query("
        SELECT bairro, COUNT(*) AS total
        FROM inscricoes
        WHERE status = 'ativa'
          AND bairro IS NOT NULL
          AND TRIM(bairro) <> ''
        GROUP BY bairro
        ORDER BY total DESC
        LIMIT 3
    ");
    $bairros_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Média de idade
    $stmt = $conn->query("
        SELECT COALESCE(ROUND(AVG(EXTRACT(YEAR FROM AGE(CURRENT_DATE, data_nascimento)))), 0) AS media_idade
        FROM inscricoes
        WHERE status = 'ativa'
          AND data_nascimento IS NOT NULL
    ");
    $media_idade = (int) $stmt->fetch(PDO::FETCH_ASSOC)['media_idade'];

    // Inscrições recentes
    $stmt = $conn->query("
        SELECT *
        FROM inscricoes
        WHERE status = 'ativa'
        ORDER BY data_inscricao DESC
        LIMIT 10
    ");
    $inscricoes_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log('Erro no dashboard admin: ' . $e->getMessage());

    $total_inscricoes = 0;
    $inscricoes_hoje = 0;
    $bairros_cadastrados = 0;
    $bairros_populares = [];
    $media_idade = 0;
    $inscricoes_recentes = [];
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
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
        
        <!-- Header da Dashboard -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">Dashboard Administrativo</h1>
            <p class="dashboard-subtitle">
                Gerencie as inscrições e acompanhe as métricas do evento de ciclismo
            </p>
            <div class="dashboard-date">
                <i class="fas fa-calendar-alt"></i>
                <?php echo date('d/m/Y - H:i'); ?>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="metrics-grid">
            <div class="metric-card total-inscricoes" data-metric="total-inscricoes">
                <div class="metric-header">
                    <div class="metric-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="metric-value"><?php echo number_format($total_inscricoes, 0, ',', '.'); ?></div>
                <div class="metric-label">Total de Inscrições</div>
                <div class="metric-change positive">
                    <i class="fas fa-arrow-up"></i>
                    12%
                </div>
            </div>

            <div class="metric-card inscricoes-hoje" data-metric="inscricoes-hoje">
                <div class="metric-header">
                    <div class="metric-icon hoje">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="metric-value"><?php echo $inscricoes_hoje; ?></div>
                <div class="metric-label">Inscrições Hoje</div>
                <div class="metric-change positive">
                    <i class="fas fa-arrow-up"></i>
                    8%
                </div>
            </div>

            <div class="metric-card bairros-populares" data-metric="bairros-populares">
                <div class="metric-header">
                    <div class="metric-icon duplicados">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="metric-value"><?php echo $bairros_cadastrados; ?></div>
                <div class="metric-label">Bairros Cadastrados</div>
                <div class="metric-details">
                    <?php foreach (array_slice($bairros_populares, 0, 2) as $bairro): ?>
                        <small><?php echo htmlspecialchars($bairro['bairro']); ?> (<?php echo $bairro['total']; ?>)</small><br>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="metric-card media-idade" data-metric="media-idade">
                <div class="metric-header">
                    <div class="metric-icon idade">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                </div>
                <div class="metric-value"><?php echo $media_idade; ?> anos</div>
                <div class="metric-label">Média de Idade</div>
                <div class="metric-change positive">
                    <i class="fas fa-arrow-up"></i>
                    2%
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="quick-actions">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Ações Rápidas
            </h2>
            
            <div class="actions-grid">
                <a href="inscricoes.php" class="action-card">
                    <div class="action-icon manage">
                        <i class="fas fa-list"></i>
                    </div>
                    
                    <div class="action-title">Gerenciar Inscrições</div>
                    <div class="action-description">Visualizar, editar e gerenciar todas as inscrições do evento</div>
                </a>

               <!-- <a href="usuarios.php" class="action-card">
                    <div class="action-icon manage">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="action-title">Gerenciar Usuários Admin</div>
                    <div class="action-description">Criar, editar e gerenciar usuários administrativos do sistema</div>
                </a> -->

                <a href="export.php" class="action-card">
                    <div class="action-icon export">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="action-title">Exportar Dados</div>
                    <div class="action-description">Baixar relatórios em CSV ou Excel com dados dos participantes</div>
                </a>

                <a href="reports.php" class="action-card">
                    <div class="action-icon reports">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-title">Relatórios</div>
                    <div class="action-description">Visualizar gráficos e estatísticas detalhadas do evento</div>
                </a>
                <a href="certificados.php" class="action-card">
    <div class="action-icon certificate">
                        <i class="fa fa-print"></i>
                    </div>
    <div class="action-title">Imprimir Certificados</div>
    <div class="action-description">Buscar participantes e imprimir certificados de participação</div>
</a>
            </div>
        </div>

        <!-- Tabela de Inscrições Recentes -->
        <div class="recent-registrations">
            <h2 class="section-title">
                <i class="fas fa-clock"></i>
                Inscrições Recentes
            </h2>
            
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Últimas 10 Inscrições</h3>
                    <a href="inscricoes.php" class="btn-view-all">Ver Todas</a>
                </div>
                
                <table class="admin-table" id="recent-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Email</th>
                            <th>Bairro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inscricoes_recentes)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3>Nenhuma inscrição encontrada</h3>
                                    <p>As inscrições aparecerão aqui conforme forem realizadas</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inscricoes_recentes as $inscricao): ?>
                                <tr>
                                    <td>
                                        <span class="inscription-id"><?php echo htmlspecialchars($inscricao['id_inscricao_formatado']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($inscricao['nome_completo']); ?></td>
                                    <td><?php echo formatarCPF($inscricao['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($inscricao['email']); ?></td>
                                    <td><?php echo htmlspecialchars($inscricao['bairro']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewRegistration('<?php echo $inscricao['id']; ?>')" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-edit" onclick="editRegistration('<?php echo $inscricao['id']; ?>')" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteRegistration('<?php echo $inscricao['id']; ?>')" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
</div>

<!-- Modal para Visualizar/Editar Inscrição -->
<div id="inscricaoModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Detalhes da Inscrição</h3>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body" id="modalBody">
                <!-- Conteúdo será carregado via AJAX -->
            </div>

            <div class="modal-footer" id="modalFooter">
                <!-- Botões serão adicionados dinamicamente -->
            </div>
        </div>
    </div>
</div>

<style>
    #inscricaoModal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow-y: auto;
    }

    #inscricaoModal .modal-container {
        width: 100%;
        max-width: 950px;
        max-height: 90vh;
        display: flex;
    }

    #inscricaoModal .modal-content {
        width: 100%;
        max-height: 90vh;
        background: #fff;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #inscricaoModal .modal-header,
    #inscricaoModal .modal-footer {
        flex-shrink: 0;
    }

    #inscricaoModal .modal-body {
        overflow-y: auto;
        max-height: calc(90vh - 140px);
        padding: 24px;
    }
</style>

<!-- JavaScript -->
<script src="assets/js/admin-index.js"></script>

</body>
</html>
