-- Database setup for Sharing Excess with email verification
-- Run this in phpMyAdmin or MySQL command line

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS sharing_excess;
USE sharing_excess;

-- Create users table with verification fields
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor', 'recipient') DEFAULT 'recipient',
    verification_code VARCHAR(10) NULL,
    status ENUM('pending', 'active') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create food_listings table
CREATE TABLE IF NOT EXISTS food_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    quantity VARCHAR(50) NOT NULL,
    expiry_date DATE NOT NULL,
    location VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create food_requests table
CREATE TABLE IF NOT EXISTS food_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    listing_id INT NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    quantity VARCHAR(50) NOT NULL,
    needed_by DATE NOT NULL,
    location VARCHAR(200) NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES food_listings(id) ON DELETE CASCADE
);

-- Insert sample donor user (for testing)
INSERT INTO users (name, email, password, role, status, created_at) VALUES
('Sample Donor', 'donor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'donor', 'active', NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Show table structure
DESCRIBE users;
DESCRIBE food_listings;
DESCRIBE food_requests; 