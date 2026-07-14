<?php
/**
 * Script de Instalação Automática
 * Sistema de Evento de Bicicleta SMTT
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Sistema Socorro no Pedal 2026</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; }
        h1 { color: #4a90e2; }
    </style>
</head>
<body>
    <h1>🚴‍♂️ Instalação do Sistema Socorro no Pedal 2026</h1>
    
    <?php
    if ($_POST) {
        $host = $_POST['host'] ?? 'localhost';
        $username = $_POST['username'] ?? 'root';
        $password = $_POST['password'] ?? '';
        $create_sample = isset($_POST['create_sample']);
        
        try {
            // Conectar ao MySQL
            $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            echo "<div class='success'>✅ Conexão com MySQL estabelecida com sucesso!</div>";
            
            // Executar script de criação
            $sql = file_get_contents(__DIR__ . '/create_database.sql');
            
            // Dividir em comandos individuais
            $commands = explode(';', $sql);
            $success_count = 0;
            
            foreach ($commands as $command) {
                $command = trim($command);
                if (!empty($command) && !preg_match('/^--/', $command)) {
                    try {
                        $pdo->exec($command);
                        $success_count++;
                    } catch (Exception $e) {
                        // Ignorar erros de comandos já executados
                        if (!strpos($e->getMessage(), 'already exists')) {
                            echo "<div class='error'>❌ Erro ao executar comando: " . $e->getMessage() . "</div>";
                        }
                    }
                }
            }
            
            echo "<div class='success'>✅ Banco de dados criado com sucesso! ($success_count comandos executados)</div>";
            
            // Criar dados de exemplo se solicitado
            if ($create_sample) {
                $sample_sql = file_get_contents(__DIR__ . '/sample_data.sql');
                $sample_commands = explode(';', $sample_sql);
                
                foreach ($sample_commands as $command) {
                    $command = trim($command);
                    if (!empty($command) && !preg_match('/^--/', $command)) {
                        try {
                            $pdo->exec($command);
                        } catch (Exception $e) {
                            echo "<div class='error'>⚠️ Erro nos dados de exemplo: " . $e->getMessage() . "</div>";
                        }
                    }
                }
                
                echo "<div class='success'>✅ Dados de exemplo inseridos com sucesso!</div>";
            }
            
            // Atualizar arquivo de configuração
            $config_content = "<?php
class Database {
    private \$host = '$host';
    private \$db_name = 'evento_bike_smtt';
    private \$username = '$username';
    private \$password = '$password';
    private \$conn;

    public function getConnection() {
        \$this->conn = null;
        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=utf8mb4\",
                \$this->username,
                \$this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch(PDOException \$exception) {
            echo \"Erro de conexão: \" . \$exception->getMessage();
        }
        return \$this->conn;
    }
}
?>";
            
            file_put_contents('../config/database.php', $config_content);
            echo "<div class='success'>✅ Arquivo de configuração atualizado!</div>";
            
            echo "<div class='info'>
                <h3>🎉 Instalação Concluída!</h3>
                <p><strong>Dados de acesso administrativo:</strong></p>
                <div class='code'>
                    Usuário: admin<br>
                    Senha: admin123<br>
                    <strong style='color: red;'>⚠️ ALTERE A SENHA EM PRODUÇÃO!</strong>
                </div>
                <p><a href='../index.php' style='background: #4a90e2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Acessar Sistema</a></p>
            </div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Erro: " . $e->getMessage() . "</div>";
        }
    } else {
    ?>
    
    <div class="info">
        <h3>📋 Pré-requisitos</h3>
        <ul>
            <li>PHP 7.4+ ou 8+</li>
            <li>MySQL 5.7+ ou MariaDB</li>
            <li>Extensão PDO ativada</li>
            <li>Servidor web (Apache/Nginx) ou PHP built-in server</li>
        </ul>
    </div>
    
    <form method="POST">
        <h3>🔧 Configurações do Banco de Dados</h3>
        
        <p>
            <label for="host">Host do MySQL:</label><br>
            <input type="text" id="host" name="host" value="localhost" style="width: 300px; padding: 8px;">
        </p>
        
        <p>
            <label for="username">Usuário:</label><br>
            <input type="text" id="username" name="username" value="root" style="width: 300px; padding: 8px;">
        </p>
        
        <p>
            <label for="password">Senha:</label><br>
            <input type="password" id="password" name="password" value="" style="width: 300px; padding: 8px;">
        </p>
        
        <p>
            <label>
                <input type="checkbox" name="create_sample" checked>
                Criar dados de exemplo para testes
            </label>
        </p>
        
        <p>
            <button type="submit" style="background: #4a90e2; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer;">
                🚀 Instalar Sistema
            </button>
        </p>
    </form>
    
    <?php } ?>
</body>
</html>
