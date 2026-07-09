-- Seed administrativo (deploy completo)
-- Usuário: admin | Senha: SMTT@2026

INSERT INTO admin_users (username, nome, nome_completo, email, password_hash, status)
VALUES (
    'admin',
    'Administrador',
    'Administrador do Sistema',
    'admin@smtt.local',
    '$2y$10$FwiFghQ2vahWCQ9wjVKteO4RF6l25sRFy9rkLgvpT5zT2q2BBJ27a',
    'ativo'
)
ON CONFLICT (username) DO NOTHING;
