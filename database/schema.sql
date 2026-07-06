-- HS CAR RENTAL - Database Schema (MySQL)
-- Tables: admins, users, cars, car_images, bookings, booking_persons, payments, reviews, notifications, cms_settings, website_settings, contact_messages, activity_logs

CREATE DATABASE IF NOT EXISTS hs_car_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hs_car_rental;

-- Admins table
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('superadmin','admin') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mobile VARCHAR(20) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  address TEXT,
  city VARCHAR(50),
  state VARCHAR(50),
  pincode VARCHAR(10),
  role ENUM('user','admin') DEFAULT 'user',
  status ENUM('active','inactive','banned') DEFAULT 'active',
  email_verified TINYINT(1) DEFAULT 0,
  reset_token VARCHAR(255) DEFAULT NULL,
  reset_expires DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cars table
CREATE TABLE cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(50) NOT NULL,
  model VARCHAR(50) NOT NULL,
  registration_no VARCHAR(50) NOT NULL UNIQUE,
  fuel_type ENUM('Petrol','Diesel','Electric','Hybrid','CNG') NOT NULL,
  transmission ENUM('Manual','Automatic') NOT NULL,
  seating_capacity INT NOT NULL,
  price_per_hour DECIMAL(10,2) NOT NULL,
  price_per_day DECIMAL(10,2) NOT NULL,
  security_deposit DECIMAL(10,2) NOT NULL,
  description TEXT,
  features TEXT,
  status ENUM('available','rented','maintenance','disabled') DEFAULT 'available',
  rating DECIMAL(2,1) DEFAULT 0,
  review_count INT DEFAULT 0,
  featured TINYINT(1) DEFAULT 0,
  popular TINYINT(1) DEFAULT 0,
  latest TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Car images table
CREATE TABLE car_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  is_primary TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id VARCHAR(30) NOT NULL UNIQUE,
  car_id INT NOT NULL,
  user_id INT DEFAULT NULL,
  customer_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(50) NOT NULL,
  state VARCHAR(50) NOT NULL,
  pincode VARCHAR(10) NOT NULL,
  pickup_date DATE NOT NULL,
  pickup_time TIME NOT NULL,
  return_date DATE NOT NULL,
  return_time TIME NOT NULL,
  hours INT NOT NULL,
  days INT NOT NULL,
  rental_amount DECIMAL(10,2) NOT NULL,
  security_deposit DECIMAL(10,2) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  payment_method ENUM('upi','cod') DEFAULT 'upi',
  payment_status ENUM('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  booking_status ENUM('Pending','Approved','Rejected','Active','Completed','Cancelled') DEFAULT 'Pending',
  payment_screenshot VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Booking persons / KYC table
CREATE TABLE booking_persons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  person_name VARCHAR(100) NOT NULL,
  aadhaar_number VARCHAR(12) NOT NULL,
  mobile_number VARCHAR(10) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method ENUM('upi','cod') NOT NULL,
  transaction_id VARCHAR(100) DEFAULT NULL,
  screenshot_path VARCHAR(255) DEFAULT NULL,
  status ENUM('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  verified_by INT DEFAULT NULL,
  verified_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (verified_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Reviews table
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  booking_id INT DEFAULT NULL,
  user_name VARCHAR(100) NOT NULL,
  rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  status ENUM('pending','approved','hidden') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

-- Notifications table
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('booking','payment','contact','review') NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT,
  is_read TINYINT(1) DEFAULT 0,
  admin_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- CMS settings table
CREATE TABLE cms_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Website settings table
CREATE TABLE website_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity logs table
CREATE TABLE activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  admin_id INT DEFAULT NULL,
  action VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Insert default admin
INSERT INTO admins (full_name, email, password_hash, role) VALUES
('HS Admin', 'admin@hscarrental.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');
-- Default password: password

-- Insert default CMS settings
INSERT INTO cms_settings (setting_key, setting_value) VALUES
('website_name', 'HS CAR RENTAL'),
('logo', 'assets/logo.png'),
('favicon', 'assets/favicon.svg'),
('hero_title', 'Drive Your Dreams'),
('hero_subtitle', 'Premium luxury cars for rent across India'),
('hero_banner', 'assets/banner.jpg'),
('about_content', 'HS CAR RENTAL is India\'s premium car rental service...'),
('contact_content', 'Have questions? Contact us 24/7.'),
('footer_text', '© 2025 HS CAR RENTAL. All rights reserved.'),
('terms_content', 'Terms and conditions content...'),
('privacy_content', 'Privacy policy content...'),
('refund_content', 'Refund policy content...'),
('primary_color', '#D4AF37'),
('secondary_color', '#0a0a0a');

-- Insert default website settings
INSERT INTO website_settings (setting_key, setting_value) VALUES
('office_address', 'HS CAR RENTAL, 123 MG Road, Bangalore, Karnataka 560001'),
('contact_number', '+91 98765 43210'),
('whatsapp_number', '+91 98765 43210'),
('email', 'info@hscarrental.com'),
('facebook', 'https://facebook.com/hscarrental'),
('instagram', 'https://instagram.com/hscarrental'),
('twitter', 'https://twitter.com/hscarrental'),
('youtube', 'https://youtube.com/hscarrental'),
('google_maps', 'https://maps.google.com/?q=Bangalore'),
('upi_id', 'hscarrental@upi'),
('qr_code', 'assets/qr.png'),
('cod_enabled', '1');
