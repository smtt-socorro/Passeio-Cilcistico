<?php
/**
 * Script de Verificação do Banco de Dados
 * Uso: php check-database.php
 */

require_once '../config/database.php';

echo "🔍 Verificando Sistema de Banco de Dados...\n\n";

// Teste de conexão
$db = new Database();
$connection_test = $db->testConnection();

if ($connection_test) {
    echo "✅ Conexão: OK\n";
    
    // Informações do banco
    $db_info = $db->getDatabaseInfo();
    echo "📊 Informações do Banco:\n";
    echo "   - Servidor: {$db_info['server_version']}\n";
    echo "   - Database: {$db_info['database_name']}\n";
    echo "   - Charset: {$db_info['charset']}\n\n";
    
    // Verificar estrutura
    echo "🏗️ Estrutura das Tabelas:\n";
    $structure = $db->checkDatabaseStructure();
    
    $all_ok = true;
    foreach ($structure as $table => $exists) {
        $status = $exists ? "✅ OK" : "❌ FALTANDO";
        echo "   - $table: $status\n";
        if (!$exists) $all_ok = false;
    }
    
    if ($all_ok) {
        echo "\n🎉 Sistema de banco está funcionando perfeitamente!\n";
        
        // Teste de operações
        $db_ops = new DatabaseOperations();
        
        // Contar inscrições
        $total_inscricoes = $db_ops->countRecords('inscricoes');
        echo "📈 Total de inscrições: $total_inscricoes\n";
        
        // Contar admins
        $total_admins = $db_ops->countRecords('admin_users');
        echo "👥 Total de administradores: $total_admins\n";
        
    } else {
        echo "\n⚠️ Execute o instalador para criar as tabelas faltantes.\n";
        echo "   Acesse: database/install.php\n";
    }
    
} else {
    echo "❌ Erro na conexão com banco de dados!\n";
    echo "⚠️ Verifique as configurações em config/database.php\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Verificação concluída em " . date('Y-m-d H:i:s') . "\n";
?>
