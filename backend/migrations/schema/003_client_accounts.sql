CREATE TABLE client_accounts (
    id SERIAL NOT NULL,
    client_id INT NOT NULL,
    type VARCHAR(20) NOT NULL,
    label VARCHAR(255) DEFAULT NULL,
    currency_code VARCHAR(3) DEFAULT NULL,
    details JSONB DEFAULT NULL,
    is_default BOOLEAN NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX idx_client_accounts_client ON client_accounts (client_id);

ALTER TABLE client_accounts
    ADD CONSTRAINT fk_client_accounts_client
        FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE;
