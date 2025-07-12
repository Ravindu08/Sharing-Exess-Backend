-- Create database
CREATE DATABASE IF NOT EXISTS sharing_excess;
USE sharing_excess;

-- Create users table with verification fields
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor', 'recipient') NOT NULL,
    name VARCHAR(255) NOT NULL,
    status ENUM('pending', 'active') DEFAULT 'pending',
    verification_code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create food_listings table
CREATE TABLE IF NOT EXISTS food_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    quantity VARCHAR(100) NOT NULL,
    expiry_date DATE,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id)
);

-- Create food_requests table
CREATE TABLE IF NOT EXISTS food_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    quantity VARCHAR(100) NOT NULL,
    needed_by DATE,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_id) REFERENCES users(id)
);

-- Insert sample data for testing
INSERT INTO users (name, email, password, role, status) VALUES 
('John Donor', 'donor@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor', 'active'),
('Jane Recipient', 'recipient@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'recipient', 'active');

-- Insert sample food listings
INSERT INTO food_listings (donor_id, food_name, quantity, expiry_date, location) VALUES 
(1, 'Cooked Rice', '15kg', '2025-07-12', 'Colombo 03'),
(1, 'Fresh Vegetables', '10kg', '2025-07-14', 'Negombo');

-- Insert sample food requests
INSERT INTO food_requests (recipient_id, food_name, quantity, needed_by, location) VALUES 
(2, 'Rice', '10kg', '2025-07-15', 'Colombo'),
(2, 'Bread', '20 loaves', '2025-07-12', 'Galle'); 