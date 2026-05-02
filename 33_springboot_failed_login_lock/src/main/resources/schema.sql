CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    account_non_locked BOOLEAN NOT NULL DEFAULT TRUE,
    failed_attempt INT NOT NULL DEFAULT 0,
    lock_time TIMESTAMP
);
