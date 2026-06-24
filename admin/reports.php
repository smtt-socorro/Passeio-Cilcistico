<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = "Relatórios Avançados";

// Buscar dados iniciais para filtros
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Buscar bairros únicos
    $query = "SELECT DISTINCT bairro FROM inscricoes WHERE status = 'ativa' ORDER BY bairro ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $bairros = $stmt->fetchAll();
    
    // Buscar cidades únicas
    $query = "SELECT DISTINCT cidade FROM inscricoes WHERE status = 'ativa' ORDER BY cidade ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $cidades = $stmt->fetchAll();
    
    // Buscar estados únicos
    $query = "SELECT DISTINCT estado FROM inscricoes WHERE status = 'ativa' ORDER BY estado ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $estados = $stmt->fetchAll();
    
} catch (Exception $e) {
    $bairros = [];
    $cidades = [];
    $estados = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Evento Bike Socorro</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-index.css">
    <link rel="stylesheet" href="assets/css/reports.css">
    
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
                Admin - Evento Bike Socorro
            </a>
            <div class="admin-user-menu">
                <div class="admin-user-info">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr(htmlspecialchars($_SESSION['admin_nome'] ?? 'A'), 0, 1)); ?>
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
                <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
                <p class="page-subtitle">Gere relatórios detalhados com filtros personalizados e opções de impressão</p>
            </div>
            <div class="page-actions">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao Dashboard
                </a>
            </div>
        </div>

        <!-- Área de Filtros -->
        <div class="filters-area">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros do Relatório</h3>
                <button id="resetFilters" class="btn-reset">
                    <i class="fas fa-undo"></i>
                    Limpar Filtros
                </button>
            </div>
            
            <form id="filtersForm" class="filters-form">
                <div class="filters-grid">
                    <!-- Filtros de Data -->
                    <div class="filter-group">
                        <label>Período de Inscrição</label>
                        <div class="date-range">
                            <input type="date" id="dataInicio" name="data_inicio" placeholder="Data Início">
                            <span>até</span>
                            <input type="date" id="dataFim" name="data_fim" placeholder="Data Fim">
                        </div>
                    </div>
                    
                    <!-- Filtro de Status -->
                    <div class="filter-group">
                        <label>Status da Inscrição</label>
                        <select id="status" name="status">
                            <option value="">Todos os Status</option>
                            <option value="ativa">Ativa</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    
                    <!-- Filtro de Bairro -->
                    <div class="filter-group">
                        <label>Bairro</label>
                        <select id="bairro" name="bairro">
                            <option value="">Todos os Bairros</option>
                            <?php foreach ($bairros as $bairro): ?>
                                <option value="<?php echo htmlspecialchars($bairro['bairro']); ?>">
                                    <?php echo htmlspecialchars($bairro['bairro']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro de Cidade -->
                    <div class="filter-group">
                        <label>Cidade</label>
                        <select id="cidade" name="cidade">
                            <option value="">Todas as Cidades</option>
                            <?php foreach ($cidades as $cidade): ?>
                                <option value="<?php echo htmlspecialchars($cidade['cidade']); ?>">
                                    <?php echo htmlspecialchars($cidade['cidade']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro de Estado -->
                    <div class="filter-group">
                        <label>Estado</label>
                        <select id="estado" name="estado">
                            <option value="">Todos os Estados</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?php echo htmlspecialchars($estado['estado']); ?>">
                                    <?php echo htmlspecialchars($estado['estado']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro de Faixa Etária -->
                    <div class="filter-group">
                        <label>Faixa Etária</label>
                        <div class="age-range">
                            <input type="number" id="idadeMin" name="idade_min" placeholder="Idade Min" min="0" max="120">
                            <span>a</span>
                            <input type="number" id="idadeMax" name="idade_max" placeholder="Idade Max" min="0" max="120">
                        </div>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i>
                        Gerar Relatório
                    </button>
                </div>
            </form>
        </div>

        <!-- Área de Estatísticas -->
        <div id="statsArea" class="stats-area" style="display: none;">
            <div class="stats-header">
                <h3><i class="fas fa-chart-pie"></i> Estatísticas</h3>
            </div>
            <div class="stats-grid" id="statsGrid">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>

        <!-- Área de Resultados -->
        <div id="resultsArea" class="results-area" style="display: none;">
            <div class="results-header">
                <h3><i class="fas fa-table"></i> Resultados do Relatório</h3>
                <div class="results-actions">
                    <button id="printReport" class="btn-print">
                        <i class="fas fa-print"></i>
                        Imprimir
                    </button>
                    <button id="exportCSV" class="btn-export">
                        <i class="fas fa-file-csv"></i>
                        Exportar CSV
                    </button>
                    <button id="exportExcel" class="btn-export">
                        <i class="fas fa-file-excel"></i>
                        Exportar Excel
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <div id="loadingReport" class="loading-report" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Gerando relatório...</span>
                </div>
                
                <table id="reportTable" class="report-table" style="display: none;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome Completo</th>
            <th>CPF</th>
            <th>Idade</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>CEP</th>
            <th>Logradouro</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Bairro</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>Data Inscrição</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="reportTableBody">
        <!-- Será preenchido via JavaScript -->
    </tbody>
</table>
                
                <div id="emptyReport" class="empty-report" style="display: none;">
                    <i class="fas fa-inbox"></i>
                    <h3>Nenhum resultado encontrado</h3>
                    <p>Tente ajustar os filtros para encontrar inscrições.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal para Impressão -->
<div id="printModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-print"></i> Opções de Impressão</h3>
                <button class="modal-close" onclick="closePrintModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="print-options">
                    <div class="print-option">
                        <input type="radio" id="printComplete" name="printType" value="complete" checked>
                        <label for="printComplete">
                            <strong>Relatório Completo</strong>
                            <small>Inclui todas as colunas e dados completos</small>
                        </label>
                    </div>
                    <div class="print-option">
                        <input type="radio" id="printSummary" name="printType" value="summary">
                        <label for="printSummary">
                            <strong>Relatório Resumido</strong>
                            <small>Apenas nome, ID, telefone e bairro</small>
                        </label>
                    </div>
                    <div class="print-option">
                        <input type="radio" id="printStats" name="printType" value="stats">
                        <label for="printStats">
                            <strong>Apenas Estatísticas</strong>
                            <small>Gráficos e resumos numéricos</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closePrintModal()">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button class="btn-primary" onclick="executePrint()">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="assets/js/reports.js"></script>

</body>
</html>
