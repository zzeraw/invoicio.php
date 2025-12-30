CREATE TABLE clients (
    id SERIAL NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    legal_address TEXT DEFAULT NULL,
    country_code VARCHAR(2) DEFAULT NULL,
    tax_id VARCHAR(64) DEFAULT NULL,
    tax_kpp VARCHAR(64) DEFAULT NULL,
    registration_number VARCHAR(64) DEFAULT NULL,
    legal_details JSONB DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX idx_clients_user ON clients (user_id);

ALTER TABLE clients
    ADD CONSTRAINT fk_clients_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;
