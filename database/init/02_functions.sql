-- Funções de runtime (obrigatório para operação completa)

CREATE TABLE IF NOT EXISTS app_runtime (
    id INT PRIMARY KEY DEFAULT 1,
    deploy_verified BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT app_runtime_single_row CHECK (id = 1)
);

INSERT INTO app_runtime (id, deploy_verified) VALUES (1, TRUE)
ON CONFLICT (id) DO UPDATE SET deploy_verified = TRUE;

CREATE OR REPLACE FUNCTION fn_proximo_numero_inscricao()
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    proximo INT;
BEGIN
    UPDATE controle_sequencia
    SET ultimo_numero = ultimo_numero + 1
    WHERE id = 1
    RETURNING ultimo_numero INTO proximo;

    RETURN proximo;
END;
$$;

CREATE OR REPLACE FUNCTION fn_runtime_ativo()
RETURNS BOOLEAN
LANGUAGE sql
STABLE
AS $$
    SELECT COALESCE(
        (SELECT deploy_verified FROM app_runtime WHERE id = 1),
        FALSE
    );
$$;
