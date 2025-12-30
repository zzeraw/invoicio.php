CREATE TABLE users (
    id SERIAL NOT NULL,
    email VARCHAR(180) NOT NULL,
    status VARCHAR(20) NOT NULL,
    role VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    password_reset_token VARCHAR(255) DEFAULT NULL,
    password_reset_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX uniq_users_email ON users (email);
