<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Processar exportação
if (isset($_GET['format'])) {
    $format = $_GET['format'];
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    
    // Construir query
    $where_clause = "1=1";
    $params = [];
    
    if (!empty($status)) {
        $where_clause .= " AND status = :status";
        $params[':status'] = $status;
    }
    
    $query = "SELECT 
                id_inscricao_formatado,
                nome_completo,
                cpf,
                data_nascimento,
                email,
                telefone,
                cep,
                logradouro,
                numero,
                complemento,
                bairro,
                cidade,
                estado,
                link_trajeto_maps,
                data_inscricao,
                status
              FROM inscricoes 
              WHERE $where_clause 
              ORDER BY data_inscricao DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $inscricoes = $stmt->fetchAll();
    
    if ($format === 'csv') {
        exportToCSV($inscricoes);
    } elseif ($format === 'excel') {
        exportToExcel($inscricoes);
    }
    exit();
}

function exportToCSV($data) {
    $filename = 'inscricoes_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fputs($output, "\xEF\xBB\xBF");
    
    // Cabeçalhos
    $headers = [
        'ID Inscrição',
        'Nome Completo',
        'CPF',
        'Data de Nascimento',
        'Idade',
        'Email',
        'Telefone',
        'CEP',
        'Logradouro',
        'Número',
        'Complemento',
        'Bairro',
        'Cidade',
        'Estado',
        'Link Trajeto Maps',
        'Data da Inscrição',
        'Status'
    ];
    
    fputcsv($output, $headers, ';');
    
    // Dados
    foreach ($data as $row) {
        $csv_row = [
            $row['id_inscricao_formatado'],
            $row['nome_completo'],
            preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['cpf']),
            date('d/m/Y', strtotime($row['data_nascimento'])),
            calcularIdade($row['data_nascimento']),
            $row['email'],
            $row['telefone'],
            preg_replace('/(\d{5})(\d{3})/', '$1-$2', $row['cep']),
            $row['logradouro'],
            $row['numero'],
            $row['complemento'],
            $row['bairro'],
            $row['cidade'],
            $row['estado'],
            $row['link_trajeto_maps'],
            date('d/m/Y H:i', strtotime($row['data_inscricao'])),
            ucfirst($row['status'])
        ];
        
        fputcsv($output, $csv_row, ';');
    }
    
    fclose($output);
}

function exportToExcel($data) {
    $filename = 'inscricoes_' . date('Y-m-d_H-i-s') . '.xls';
    
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF"; // BOM para UTF-8
    
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>ID Inscrição</th>";
    echo "<th>Nome Completo</th>";
    echo "<th>CPF</th>";
    echo "<th>Data de Nascimento</th>";
    echo "<th>Idade</th>";
    echo "<th>Email</th>";
    echo "<th>Telefone</th>";
    echo "<th>CEP</th>";
    echo "<th>Logradouro</th>";
    echo "<th>Número</th>";
    echo "<th>Complemento</th>";
    echo "<th>Bairro</th>";
    echo "<th>Cidade</th>";
    echo "<th>Estado</th>";
    echo "<th>Link Trajeto Maps</th>";
    echo "<th>Data da Inscrição</th>";
    echo "<th>Status</th>";
    echo "</tr>";
    
    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id_inscricao_formatado']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nome_completo']) . "</td>";
        echo "<td>" . preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['cpf']) . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['data_nascimento'])) . "</td>";
        echo "<td>" . calcularIdade($row['data_nascimento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefone']) . "</td>";
        echo "<td>" . preg_replace('/(\d{5})(\d{3})/', '$1-$2', $row['cep']) . "</td>";
        echo "<td>" . htmlspecialchars($row['logradouro']) . "</td>";
        echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
        echo "<td>" . htmlspecialchars($row['complemento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['bairro']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cidade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
        echo "<td>" . htmlspecialchars($row['link_trajeto_maps']) . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row['data_inscricao'])) . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Buscar estatísticas para a página
$stats = [];

$query = "SELECT status, COUNT(*) as total FROM inscricoes GROUP BY status";
$stmt = $conn->query($query);
while ($row = $stmt->fetch()) {
    $stats[$row['status']] = $row['total'];
}

$total_ativas = $stats['ativa'] ?? 0;
$total_canceladas = $stats['cancelada'] ?? 0;
$total_geral = $total_ativas + $total_canceladas;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Dados - Painel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header Admin -->
        <header class="admin-header">
            <div>
                <h1 style="color: var(--accent-blue); margin-bottom: 5px;">
                    <i class="fas fa-download"></i> Exportar Dados
                </h1>
                <p style="color: var(--gray-medium); margin: 0;">
                    Baixe os dados das inscrições em diferentes formatos
                </p>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="inscricoes.php" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Gerenciar
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </header>

        <!-- Estatísticas -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon" style="background: var(--accent-blue); color: var(--white);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-value"><?php echo $total_geral; ?></div>
                <div class="metric-label">Total de Inscrições</div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: var(--success); color: var(--white);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="metric-value"><?php echo $total_ativas; ?></div>
                <div class="metric-label">Inscrições Ativas</div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: var(--danger); color: var(--white);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="metric-value"><?php echo $total_canceladas; ?></div>
                <div class="metric-label">Inscrições Canceladas</div>
            </div>
        </div>

        <!-- Opções de Exportação -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Opções de Exportação</h2>
                <p class="card-subtitle">Escolha o formato e filtros para exportar os dados</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <!-- Exportar Todas as Inscrições -->
                <div style="border: 2px solid var(--light-blue); border-radius: 12px; padding: 25px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: var(--accent-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                        <i class="fas fa-users" style="font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 10px;">Todas as Inscrições</h3>
                    <p style="color: var(--gray-medium); margin-bottom: 25px;">
                        Exportar todas as inscrições (ativas e canceladas)
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="?format=csv" class="btn btn-primary">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                        <a href="?format=excel" class="btn btn-secondary">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>

                <!-- Exportar Apenas Ativas -->
                <div style="border: 2px solid var(--light-blue); border-radius: 12px; padding: 25px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 10px;">Apenas Ativas</h3>
                    <p style="color: var(--gray-medium); margin-bottom: 25px;">
                        Exportar somente as inscrições ativas
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="?format=csv&status=ativa" class="btn btn-primary">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                        <a href="?format=excel&status=ativa" class="btn btn-secondary">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>

                <!-- Exportar Apenas Canceladas -->
                <div style="border: 2px solid var(--light-blue); border-radius: 12px; padding: 25px; text-align: center;">
                    <div style="width: 60px; height: 60px; background: var(--danger); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                        <i class="fas fa-times-circle" style="font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 10px;">Apenas Canceladas</h3>
                    <p style="color: var(--gray-medium); margin-bottom: 25px;">
                        Exportar somente as inscrições canceladas
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="?format=csv&status=cancelada" class="btn btn-primary">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                        <a href="?format=excel&status=cancelada" class="btn btn-secondary">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>
            </div>

            <div style="background: var(--light-blue); padding: 20px; border-radius: 12px; margin-top: 30px;">
                <h4 style="color: var(--accent-blue); margin-bottom: 15px;">
                    <i class="fas fa-info-circle"></i> Informações sobre os Formatos
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h5 style="margin-bottom: 8px;"><i class="fas fa-file-csv"></i> CSV</h5>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--gray-medium);">
                            Formato compatível com planilhas. Ideal para análise de dados e importação em outros sistemas.
                        </p>
                    </div>
                    <div>
                        <h5 style="margin-bottom: 8px;"><i class="fas fa-file-excel"></i> Excel</h5>
                        <p style="margin: 0; font-size: 0.9rem; color: var(--gray-medium);">
                            Formato .xls para abertura direta no Microsoft Excel ou LibreOffice Calc.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
