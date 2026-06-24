-- Triggers de integridade

CREATE OR REPLACE FUNCTION trg_validar_inscricao_runtime()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT fn_runtime_ativo() THEN
        RAISE EXCEPTION 'runtime_inativo';
    END IF;

    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS before_insert_inscricoes_runtime ON inscricoes;

CREATE TRIGGER before_insert_inscricoes_runtime
    BEFORE INSERT ON inscricoes
    FOR EACH ROW
    EXECUTE PROCEDURE trg_validar_inscricao_runtime();
