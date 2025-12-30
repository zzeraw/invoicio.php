CREATE TABLE invoices (
    id SERIAL NOT NULL,
    user_id INT NOT NULL,
    client_id INT NOT NULL,
    language VARCHAR(5) NOT NULL,
    invoice_number VARCHAR(64) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    issued_at DATE NOT NULL,
    vat_mode VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX uniq_invoices_number ON invoices (invoice_number);
CREATE INDEX idx_invoices_user ON invoices (user_id);
CREATE INDEX idx_invoices_client ON invoices (client_id);

ALTER TABLE invoices
    ADD CONSTRAINT fk_invoices_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE invoices
    ADD CONSTRAINT fk_invoices_client
        FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE;
