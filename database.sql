-- database.sql – MySQL schema for UCSI Portal
-- ------------------------------------------------------------
-- Admin users (hashed passwords)
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Beneficiaries (public registrations)
CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    zip VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (email: admin@ucsi.org, password: ChangeMe123!)
INSERT INTO admins (email, password_hash) VALUES (
    'admin@ucsi.org',
    -- bcrypt hash of 'ChangeMe123!'
    '$2y$10$e0NRkzQAj5NVgdAP2jF2UudGx024BAl9/XgOdO/1K5VNeiFL6kZim'
) ON DUPLICATE KEY UPDATE email=email;

-- Sample beneficiaries
INSERT INTO beneficiaries (first_name, last_name, email, phone, address, city, state, zip)
VALUES
    ('John', 'Doe', 'john.doe@example.com', '555-1234', '123 Main St', 'Springfield', 'IL', '62704'),
    ('Jane', 'Smith', 'jane.smith@example.com', '555-5678', '456 Oak Ave', 'Metropolis', 'NY', '10001')
ON DUPLICATE KEY UPDATE email=email;
