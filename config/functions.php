<?php
/**
 * Funções Auxiliares do Sistema
 * Evento Bike SMTT Socorro
 * Data: 28/05/2026
 */

// Incluir configuração do banco
require_once 'database.php';

/**
 * Sanitizar dados de entrada
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar CPF
 */
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    // Calcula primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica primeiro dígito
    if ($cpf[9] != $dv1) {
        return false;
    }
    
    // Calcula segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    
    // Verifica segundo dígito
    return $cpf[10] == $dv2;
}

/**
 * Gerar próximo ID sequencial (CORRIGIDO)
 */
function gerarProximoID() {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        if (!$database->isRuntimeActive()) {
            throw new Exception('Ambiente de execução não está ativo');
        }

        $conn->beginTransaction();

        $stmt = $conn->query('SELECT fn_proximo_numero_inscricao() AS ultimo_numero');
        $result = $stmt->fetch();
        $numero = (int) ($result['ultimo_numero'] ?? 0);

        if ($numero <= 0) {
            throw new Exception('Falha ao gerar sequencial');
        }

        $id_formatado = sprintf('B%04d', $numero);

        $conn->commit();

        return [
            'numero' => $numero,
            'id_formatado' => $id_formatado,
        ];
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        throw new Exception('Erro ao gerar ID: ' . $e->getMessage());
    }
}

/**
 * Verificar limite de inscrições por CPF
 */
function verificarLimiteInscricoes($cpf) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT COUNT(*) as total FROM inscricoes WHERE cpf = :cpf AND status = 'ativa'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        // Retorna true se pode fazer mais inscrições (menos de 2)
        return $result['total'] < 2;
        
    } catch (Exception $e) {
        error_log('Erro ao verificar limite de inscrições: ' . $e->getMessage());
        return false;
    }
}

/**
 * Calcular idade
 */
function calcularIdade($data_nascimento) {
    try {
        $nascimento = new DateTime($data_nascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento);
        return $idade->y;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Formatar CPF
 */
function formatarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
    return $cpf;
}

/**
 * Formatar CEP
 */
function formatarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) === 8) {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }
    return $cep;
}

/**
 * Formatar telefone
 */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }
    return $telefone;
}

/**
 * Validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gerar hash seguro para senha
 */
function gerarHashSenha($senha) {
    return password_hash($senha, PASSWORD_ARGON2ID);
}

/**
 * Verificar senha
 */
function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

/**
 * Gerar token CSRF
 */
function gerarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log de atividades
 */
function logAtividade($acao, $detalhes = '') {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "INSERT INTO logs (acao, detalhes, ip_address, user_agent, data_log) 
                  VALUES (:acao, :detalhes, :ip, :user_agent, NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':acao', $acao);
        $stmt->bindParam(':detalhes', $detalhes);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log('Erro ao registrar log: ' . $e->getMessage());
    }
}

/**
 * Verificar se usuário está logado (admin)
 */
function verificarLogin() {
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Exportar dados para CSV
 */
function exportarCSV($dados, $nome_arquivo) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    if (!empty($dados)) {
        // Cabeçalhos
        fputcsv($output, array_keys($dados[0]), ';');
        
        // Dados
        foreach ($dados as $row) {
            fputcsv($output, $row, ';');
        }
    }
    
    fclose($output);
}

/**
 * Validar data no formato dd/mm/yyyy
 */
function validarDataNascimento($data) {
    $regex = '/^(\d{2})\/(\d{2})\/(\d{4})$/';
    if (!preg_match($regex, $data, $matches)) {
        return false;
    }
    
    $dia = (int)$matches[1];
    $mes = (int)$matches[2];
    $ano = (int)$matches[3];
    
    // Verificar se é uma data válida
    if (!checkdate($mes, $dia, $ano)) {
        return false;
    }
    
    // Verificar se não é uma data futura
    $data_nascimento = DateTime::createFromFormat('d/m/Y', $data);
    $hoje = new DateTime();
    
    if ($data_nascimento > $hoje) {
        return false;
    }
    
    // Verificar idade mínima (exemplo: 10 anos) e máxima (exemplo: 100 anos)
    $idade = $hoje->diff($data_nascimento)->y;
    if ($idade < 10 || $idade > 100) {
        return false;
    }
    
    return true;
}

/**
 * Converter data dd/mm/yyyy para yyyy-mm-dd
 */
function converterDataParaMySQL($data) {
    $parts = explode('/', $data);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return false;
}

/**
 * Converter data yyyy-mm-dd para dd/mm/yyyy
 */
function converterDataParaBR($data) {
    $parts = explode('-', $data);
    if (count($parts) === 3) {
        return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    }
    return $data;
}

/**
 * Buscar dados de endereço via ViaCEP (server-side)
 */
function buscarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
    if (strlen($cep) !== 8) {
        return false;
    }
    
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (!isset($data['erro'])) {
            return $data;
        }
    }
    
    return false;
}

/**
 * Função para debug (apenas em desenvolvimento)
 */
function debug($data) {
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}
?>
