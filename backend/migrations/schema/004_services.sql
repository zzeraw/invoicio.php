CREATE TABLE services (
    id SERIAL NOT NULL,
    user_id INT NOT NULL,
    name_ru VARCHAR(255) DEFAULT NULL,
    name_en VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX idx_services_user ON services (user_id);

ALTER TABLE services
    ADD CONSTRAINT fk_services_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;
