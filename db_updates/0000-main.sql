CREATE TABLE users(
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL
);

CREATE TABLE permissions(
    permission_id SERIAL PRIMARY KEY,
    permission VARCHAR(125) NOT NULL
);

CREATE TABLE user_permissions(
    user_permission_id SERIAL PRIMARY KEY,
    permission_id INTEGER REFERENCES permissions (permission_id) NOT NULL,
    user_id INTEGER REFERENCES users (user_id) NOT NULL
);

INSERT INTO permissions (permission) VALUES('can_view');
INSERT INTO permissions (permission) VALUES('can_add');
INSERT INTO permissions (permission) VALUES('can_manage');
