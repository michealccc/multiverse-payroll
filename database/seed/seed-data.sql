-- Seed data for development and testing
-- This script will only run if SEED_DATA=true environment variable is set

-- Insert seed companies
INSERT INTO companies (id, name) VALUES
(1, 'BingBong LLC'),
(2, 'ACME Corporation'),
(3, 'Stark Industries'),
(4, 'Wayne Enterprises');

-- Insert seed employees for BingBong LLC
INSERT INTO employees (company_id, full_name, email, salary) VALUES
(1, 'Alice Smith', 'alice@bingbong.com', 50000.00),
(1, 'Bob Johnson', 'bob@bingbong.com', 55000.00);

-- Insert seed employees for ACME Corporation
INSERT INTO employees (company_id, full_name, email, salary) VALUES
(2, 'John Doe', 'johndoe@acme.com', 50000.00),
(2, 'Jane Doe', 'janedoe@acme.com', 55000.00),
(2, 'Bob Smith', 'bobsmith@acme.com', 60000.00),
(2, 'Alice Johnson', 'alicejohnson@acme.com', 65000.00);

-- Insert seed employees for Stark Industries
INSERT INTO employees (company_id, full_name, email, salary) VALUES
(3, 'Tony Stark', 'tony@stark.com', 100000.00),
(3, 'Pepper Potts', 'pepper@stark.com', 75000.00),
(3, 'Happy Hogan', 'happy@stark.com', 60000.00),
(3, 'Rhodey Rhodes', 'rhodey@stark.com', 80000.00);

-- Insert seed employees for Wayne Enterprises
INSERT INTO employees (company_id, full_name, email, salary) VALUES
(4, 'Bruce Wayne', 'bruce@wayneenterprises.com', 90000.00),
(4, 'Alfred Pennyworth', 'alfred@wayneenterprises.com', 50000.00),
(4, 'Dick Grayson', 'dick@wayneenterprises.com', 60000.00),
(4, 'Barbara Gordon', 'barbara@wayneenterprises.com', 55000.00);

-- End of seed data
