-- Seed administrativo (deploy completo)
-- Usuário: admin | Senha: admin123

INSERT INTO admin_users (username, nome, nome_completo, email, password_hash, status)
VALUES (
    'admin',
    'Administrador',
    'Administrador do Sistema',
    'admin@smtt.local',
    '$2y$10$IFAz/WwhO8JmgkWSefqLqOtCWwQoQnXYgp9Z73GR3nWZBMMaj9y3K',
    'ativo'
)
ON CONFLICT (username) DO NOTHING;
