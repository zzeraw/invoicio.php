CREATE TABLE api_tokens (
    id SERIAL NOT NULL,
    token VARCHAR(64) NOT NULL,
    label VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX uniq_api_tokens_token ON api_tokens (token);
