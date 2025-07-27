<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Connect to MySQL without selecting a database
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS sharing_excess";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Database created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating database: ' . $conn->error]);
        exit;
    }
    
    // Select the database
    $conn->select_db('sharing_excess');
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('donor', 'recipient', 'admin', 'officer') NOT NULL,
        name VARCHAR(255) NOT NULL,
        status ENUM('active') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Users table created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating users table: ' . $conn->error]);
        exit;
    }
    
    // Create food_listings table
    $sql = "CREATE TABLE IF NOT EXISTS food_listings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_id INT NOT NULL,
        food_name VARCHAR(255) NOT NULL,
        quantity VARCHAR(100) NOT NULL,
        expiry_date DATE,
        location VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES users(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Food listings table created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating food_listings table: ' . $conn->error]);
        exit;
    }
    
    // Create food_requests table
    $sql = "CREATE TABLE IF NOT EXISTS food_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient_id INT NOT NULL,
        food_name VARCHAR(255) NOT NULL,
        quantity VARCHAR(100) NOT NULL,
        needed_by DATE,
        location VARCHAR(255),
        status ENUM('pending', 'accepted', 'declined', 'picked_up', 'delivering', 'delivered') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipient_id) REFERENCES users(id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Food requests table created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating food_requests table: ' . $conn->error]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Database setup completed successfully!']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 