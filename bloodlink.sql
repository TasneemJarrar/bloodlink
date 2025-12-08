-- BloodLink Management System Database
-- Run this in phpMyAdmin or MySQL

CREATE DATABASE IF NOT EXISTS bloodlink_db;
USE bloodlink_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    age INT NOT NULL,
    last_donation_date DATE DEFAULT NULL,
    photo VARCHAR(255) DEFAULT 'default-avatar.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin account
INSERT INTO users (name, email, password, role, blood_type, age, photo) VALUES
('Dr. Sarah Admin', 'admin@bloodlink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'O+', 35, 'admin.jpg'),
('John Donor', 'john@bloodlink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'A-', 28, 'john.jpg'),
('Jane Smith', 'jane@bloodlink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'AB+', 42, 'jane.jpg');

-- Note: Default password for all users is 'password123'
-- Password hash generated using: password_hash('password123', PASSWORD_DEFAULT)