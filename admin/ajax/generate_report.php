<?php
session_start();
require_once '../../config/functions.php';

header('Content-Type: application/json');

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Construir query com filtros
    $where_conditions = ['1=1'];
    $params = [];
    
    // Filtro de data
    if (!empty($_POST['data_inicio'])) {
        $where_conditions[] = 'DATE(data_inscricao) >= ?';
        $params[] = $_POST['data_inicio'];
    }
    
    if (!empty($_POST['data_fim'])) {
        $where_conditions[] = 'DATE(data_inscricao) <= ?';
        $params[] = $_POST['data_fim'];
    }
    
    // Filtro de status
    if (!empty($_POST['status'])) {
        $where_conditions[] = 'status = ?';
        $params[] = $_POST['status'];
    }
    
    // Filtro de bairro
    if (!empty($_POST['bairro'])) {
        $where_conditions[] = 'bairro = ?';
        $params[] = $_POST['bairro'];
    }
    
    // Filtro de cidade
    if (!empty($_POST['cidade'])) {
        $where_conditions[] = 'cidade = ?';
        $params[] = $_POST['cidade'];
    }
    
    // Filtro de estado
    if (!empty($_POST['estado'])) {
        $where_conditions[] = 'estado = ?';
        $params[] = $_POST['estado'];
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // ATUALIZADO: Query principal com todos os campos de endereço
    $query = "SELECT 
                id,
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
                data_inscricao,
                status,
                EXTRACT(YEAR FROM AGE(CURRENT_DATE, data_nascimento)) as idade
              FROM inscricoes 
              WHERE $where_clause 
              ORDER BY data_inscricao DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Aplicar filtro de idade se especificado
    if (!empty($_POST['idade_min']) || !empty($_POST['idade_max'])) {
        $data = array_filter($data, function($row) {
            $idade = $row['idade'];
            $idade_min = !empty($_POST['idade_min']) ? (int)$_POST['idade_min'] : 0;
            $idade_max = !empty($_POST['idade_max']) ? (int)$_POST['idade_max'] : 120;
            
            return $idade >= $idade_min && $idade <= $idade_max;
        });
        
        $data = array_values($data);
    }
    
    // Calcular estatísticas
    $stats = [
        'total' => count($data),
        'ativas' => count(array_filter($data, function($row) { return $row['status'] == 'ativa'; })),
        'canceladas' => count(array_filter($data, function($row) { return $row['status'] == 'cancelada'; })),
        'total_bairros' => count(array_unique(array_column($data, 'bairro'))),
        'total_cidades' => count(array_unique(array_column($data, 'cidade'))),
        'media_idade' => count($data) > 0 ? round(array_sum(array_column($data, 'idade')) / count($data), 1) : 0
    ];
    
    logAtividade('Relatório gerado', "Filtros aplicados, {$stats['total']} registros encontrados. Admin: {$_SESSION['admin_nome']}");
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'stats' => $stats,
        'filters_applied' => $_POST
    ]);
    
} catch (Exception $e) {
    error_log("Erro em generate_report.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor ao gerar relatório'
    ]);
}
?>
