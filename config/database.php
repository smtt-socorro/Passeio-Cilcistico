<?php
/**
 * Configuração de Conexão com Banco de Dados
 * Sistema Evento Bike SMTT Socorro
 */
date_default_timezone_set('America/Sao_Paulo');

class Database {
    protected $host;
    protected $port;
    protected $db_name;
    protected $username;
    protected $password;
    protected $charset = 'UTF8';
    private $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->port = getenv('DB_PORT') ?: '5432';
        $this->db_name = getenv('DB_NAME') ?: 'smtt';
        $this->username = getenv('DB_USER') ?: 'smtt';
        $this->password = getenv('DB_PASS') ?: '';
    }

    /**
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $this->host,
                $this->port,
                $this->db_name
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("SET client_encoding TO 'UTF8'");

            if ($this->isDebugMode()) {
                error_log('Conexão PostgreSQL estabelecida - ' . date('Y-m-d H:i:s'));
            }
        } catch (PDOException $exception) {
            error_log('Erro de conexão com banco: ' . $exception->getMessage());

            if ($this->isDebugMode()) {
                die('Erro de conexão: ' . $exception->getMessage());
            }

            die('Erro interno do sistema. Tente novamente mais tarde.');
        }

        return $this->conn;
    }

    public function testConnection(): bool {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query('SELECT 1');
                return $stmt !== false;
            }
        } catch (Exception $e) {
            error_log('Erro no teste de conexão: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    public function checkDatabaseStructure(): array {
        $tables_required = ['inscricoes', 'admin_users', 'controle_sequencia'];
        $results = [];

        try {
            $conn = $this->getConnection();

            foreach ($tables_required as $table) {
                $stmt = $conn->prepare(
                    "SELECT COUNT(*) AS total
                     FROM information_schema.tables
                     WHERE table_schema = 'public' AND table_name = :table"
                );
                $stmt->bindValue(':table', $table);
                $stmt->execute();
                $row = $stmt->fetch();
                $results[$table] = ((int) ($row['total'] ?? 0)) > 0;
            }
        } catch (Exception $e) {
            error_log('Erro ao verificar estrutura: ' . $e->getMessage());
        }

        return $results;
    }

    public function isRuntimeActive(): bool {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query('SELECT fn_runtime_ativo() AS ativo');
            $row = $stmt->fetch();
            return (bool) ($row['ativo'] ?? false);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getDatabaseInfo(): array {
        try {
            $conn = $this->getConnection();
            $version = $conn->query('SELECT version()')->fetchColumn();

            return [
                'server_info' => 'PostgreSQL',
                'server_version' => $version,
                'connection_status' => 'connected',
                'database_name' => $this->db_name,
                'charset' => $this->charset,
            ];
        } catch (Exception $e) {
            error_log('Erro ao obter informações do banco: ' . $e->getMessage());
            return [];
        }
    }

    public function createBackup() {
        try {
            $conn = $this->getConnection();
            $backup_sql = '';
            $tables = ['inscricoes', 'admin_users', 'controle_sequencia'];

            foreach ($tables as $table) {
                $stmt = $conn->query("SELECT * FROM {$table}");
                $rows = $stmt->fetchAll();

                if (!empty($rows)) {
                    $backup_sql .= "\n-- Dados da tabela {$table}\n";
                    foreach ($rows as $row) {
                        $columns = array_keys($row);
                        $values = array_map(fn ($value) => $conn->quote((string) $value), array_values($row));
                        $backup_sql .= 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (';
                        $backup_sql .= implode(', ', $values) . ");\n";
                    }
                }
            }

            $backup_file = 'backups/backup_' . date('Y-m-d_H-i-s') . '.sql';

            if (!is_dir('backups')) {
                mkdir('backups', 0755, true);
            }

            file_put_contents($backup_file, $backup_sql);
            return $backup_file;
        } catch (Exception $e) {
            error_log('Erro ao criar backup: ' . $e->getMessage());
            return false;
        }
    }

    protected function isDebugMode(): bool {
        $debug = getenv('DEBUG_MODE');
        return $debug === 'true' || $debug === '1';
    }

    public function setConnectionParams(string $host, string $dbname, string $username, string $password): void {
        $this->host = $host;
        $this->db_name = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    public function getConnectionParams(): array {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->db_name,
            'username' => $this->username,
            'charset' => $this->charset,
        ];
    }
}

function getDbConnection() {
    $database = new Database();
    return $database->getConnection();
}

define('DB_SUCCESS', 'success');
define('DB_ERROR', 'error');
define('DB_WARNING', 'warning');

class DatabaseOperations {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function executeQuery(string $sql, array $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            if (stripos($sql, 'SELECT') === 0) {
                return $stmt->fetchAll();
            }

            return true;
        } catch (PDOException $e) {
            error_log('Erro na query: ' . $e->getMessage());
            return false;
        }
    }

    public function countRecords(string $table, array $conditions = []): int {
        $sql = "SELECT COUNT(*) AS total FROM {$table}";

        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $field => $value) {
                $where_clauses[] = "{$field} = :{$field}";
            }
            $sql .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        $result = $this->executeQuery($sql, $conditions);
        return $result ? (int) $result[0]['total'] : 0;
    }

    public function insertRecord(string $table, array $data) {
        $fields = array_keys($data);
        $placeholders = array_map(fn ($field) => ":{$field}", $fields);

        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $placeholders) . ')';

        try {
            $stmt = $this->conn->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();
            return $this->conn->lastInsertId('id');
        } catch (PDOException $e) {
            error_log('Erro ao inserir: ' . $e->getMessage());
            return false;
        }
    }
}

if (basename($_SERVER['PHP_SELF']) === 'database.php') {
    $db = new Database();

    if ($db->testConnection()) {
        echo "✅ Conexão com PostgreSQL funcionando corretamente!\n";

        $structure = $db->checkDatabaseStructure();
        echo "\n📊 Estrutura do banco:\n";
        foreach ($structure as $table => $exists) {
            echo "- {$table}: " . ($exists ? '✅ OK' : '❌ FALTANDO') . "\n";
        }

        echo '- runtime: ' . ($db->isRuntimeActive() ? "✅ OK\n" : "❌ INATIVO\n");
    } else {
        echo "❌ Erro na conexão com banco de dados!\n";
    }
}
