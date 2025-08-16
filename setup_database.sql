-- Souls Blood Donation Website - Complete Database Setup
-- This file contains all database tables and structure for the complete system

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    phone VARCHAR(20),
    role ENUM('donor', 'admin') DEFAULT 'donor',
    last_activity TIMESTAMP NULL DEFAULT NULL,
    email_notifications TINYINT(1) DEFAULT 1,
    push_notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_blood_group (blood_group),
    INDEX idx_role (role)
);

-- Create blood_requests table
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    requester_name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    urgency ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    request_date DATE NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_blood_group (blood_group),
    INDEX idx_urgency (urgency),
    INDEX idx_request_date (request_date),
    INDEX idx_status_urgency (status, urgency),
    INDEX idx_blood_group_status (blood_group, status)
);

-- Create donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    donor_name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    location VARCHAR(255) NOT NULL,
    donation_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_donation_date (donation_date),
    INDEX idx_blood_group (blood_group)
);

-- Create donation_confirmations table
CREATE TABLE IF NOT EXISTS donation_confirmations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    donor_id INT NOT NULL,
    recipient_id INT NOT NULL,
    donor_confirmed TINYINT(1) DEFAULT 0,
    recipient_confirmed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_request_donor (request_id, donor_id),
    INDEX idx_request_id (request_id),
    INDEX idx_donor_id (donor_id),
    INDEX idx_recipient_id (recipient_id)
);

-- Create forum_posts table
CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- Create chat_messages table
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender_receiver (sender_id, receiver_id),
    INDEX idx_receiver_read (receiver_id, is_read),
    INDEX idx_created_at (created_at)
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created_at (created_at)
);

-- Create search_logs table
CREATE TABLE IF NOT EXISTS search_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    search_term VARCHAR(255) NOT NULL,
    filters JSON NULL,
    results_count INT DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_search_term (search_term),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- Update existing users to have last_activity
UPDATE users SET last_activity = NOW() WHERE last_activity IS NULL; 