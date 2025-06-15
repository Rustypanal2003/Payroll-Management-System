-- Create database (run once)
CREATE DATABASE IF NOT EXISTS payroll;
USE payroll;

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL
);

-- Positions table
CREATE TABLE IF NOT EXISTS positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    salary_rate DECIMAL(10,2) NOT NULL
    
);

-- Employees table (uses salary_rate to match PHP code)
CREATE TABLE IF NOT EXISTS employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    position_id INT NOT NULL,
    salary_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    hire_date DATE NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    FOREIGN KEY (position_id) REFERENCES positions(position_id)
);

-- Users table (for admin/client login)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- plain text for testing
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
);

-- Insert sample departments
INSERT INTO departments (department_name) VALUES
('Human Resources'),
('Finance'),
('IT'),
('Marketing');

-- Insert sample positions
INSERT INTO positions (position_name, salary_rate) VALUES
('Manager', 50000.00),
('Staff', 25000.00),
('Developer', 40000.00),
('Designer', 30000.00);

-- Insert sample employees
INSERT INTO employees (firstname, lastname, department_id, position_id, salary_rate, hire_date) VALUES
('Juan', 'Dela Cruz', 1, 1, 50000.00, '2023-01-15'),
('Maria', 'Santos', 3, 3, 40000.00, '2022-06-01'),
('Pedro', 'Reyes', 2, 2, 25000.00, '2021-09-10'),
('Ana', 'Lopez', 4, 4, 30000.00, '2020-11-05');

-- Insert dummy users with plain text passwords
INSERT INTO users (username, password, role) VALUES
('admin1', 'admin123', 'admin'),
('user1', 'user123', 'user');
