CREATE TABLE invoice_items (
    id SERIAL NOT NULL,
    invoice_id INT NOT NULL,
    service_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    hours DOUBLE PRECISION NOT NULL,
    unit_price INT NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX idx_invoice_items_invoice ON invoice_items (invoice_id);
CREATE INDEX idx_invoice_items_service ON invoice_items (service_id);

ALTER TABLE invoice_items
    ADD CONSTRAINT fk_invoice_items_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE;

ALTER TABLE invoice_items
    ADD CONSTRAINT fk_invoice_items_service
        FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE SET NULL;
