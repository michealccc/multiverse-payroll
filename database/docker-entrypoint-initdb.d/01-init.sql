-- Initialization script for Multiverse Payroll Database
-- This script runs automatically when the container is first created

-- Create database
CREATE DATABASE IF NOT EXISTS multiverse_db CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci;

USE multiverse_db;

-- Table: companies
DROP TABLE IF EXISTS companies;

CREATE TABLE companies (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(191) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_companies_name (name)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Table: employees
DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  full_name VARCHAR(191) NOT NULL,
  email VARCHAR(254) NOT NULL,
  salary DECIMAL(12, 2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_employees_email (email),
  KEY ix_employees_company (company_id),
  KEY ix_employees_salary (salary),
  CONSTRAINT fk_employees_company FOREIGN KEY (company_id) REFERENCES companies (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Seed Data
INSERT INTO companies (name) VALUES
  ('BingBong LLC');

-- BingBong LLC employees
INSERT INTO employees (company_id, full_name, email, salary)
SELECT id, 'Bing Bong', 'bingbong@bingbong.com', 50000.00 FROM companies WHERE name='BingBong LLC';
INSERT INTO employees (company_id, full_name, email, salary)
SELECT id, 'Bong Bing', 'bongbing@bingbong.com', 55000.00 FROM companies WHERE name='BingBong LLC';

-- End of initialization script