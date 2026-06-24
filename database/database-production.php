<?php
/**
 * Configuração de Banco para Produção
 * Sistema Evento Bike SMTT Socorro
 */

class ProductionDatabase extends Database {
    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'smtt-pgsql';
        $this->port = $_ENV['DB_PORT'] ?? '5432';
        $this->db_name = $_ENV['DB_NAME'] ?? 'smtt';
        $this->username = $_ENV['DB_USER'] ?? 'smtt';
        $this->password = $_ENV['DB_PASS'] ?? '';

        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
    }

    protected function isDebugMode(): bool {
        return false;
    }
}
