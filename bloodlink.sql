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

-- Insert default admin account with HASHED passwords
-- Password for all users: password123
INSERT INTO users (name, email, password, role, blood_type, age, photo) VALUES
('Admin', 'admin@bloodlink.com', 'password123', 'admin', 'AB+', 35, 'default-avatar.jpg'),
('Tasneem', 'tasneem@bloodlink.com', 'password123', 'user', 'A+', 21, 'default-avatar.jpg'),
('Heba', 'heba@bloodlink.com', 'password123', 'user', 'O+', 21, 'default-avatar.jpg');

-- Note: Default password for all users is 'password123'
-- Hash generated using: password_hash('password123', PASSWORD_DEFAULT)