# Multiverse Payroll

A comprehensive employee and company management system with RESTful API and CSV import capabilities.

## Last Tested Using

### Backend

- **PHP** - 8.4-fpm-alpine
- **MariaDB** - 11.6.2

### Frontend

- **Vue** - 3.5.22
- **Node** - 22-alpine

### Infrastructure

- **Docker** - 20.10+
- **Nginx** - 1.27.3-alpine

## Quick Start

### Prerequisites

- Docker Desktop 20.10+

### Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/michealccc/multiverse-payroll.git
   cd multiverse-payroll
   ```

2. **Configure environment:**

   ```bash
   cp .env.example .env
   ```

3. **Start the application:**

   ```bash
   docker-compose up -d
   ```

4. **Verify installation:**

   ```bash
   docker exec multiverse_backend php test-db-connection.php
   ```

### Access the Application

- **Web Application:** <http://localhost:9090>
- **API Endpoint:** <http://localhost:9090/api>
- **Database:** localhost:3307 (username: `multiverse_user`, password: `multiverse_password`)

The application includes:

- **Home:** Landing page
- **Companies:** View all companies with employee count and average salary
- **Employees:** View and edit employee email addresses
- **CSV Import:** Upload CSV files to import employees and companies

## Features

### Core Requirements

1. **CSV File Upload** - Web interface to accept and upload CSV files
2. **Database Import** - Automatic import of companies and employees from CSV
3. **Employee List** - Display all employees with their details
4. **Edit Email** - Inline editing of employee email addresses with validation
5. **Average Salary** - Display average salary for each company

## Testing

### Backend Tests (PHP/Pest)

**Run all backend tests:**

```bash
docker exec multiverse_backend ./vendor/bin/pest
```

**Run with coverage:**

```bash
docker exec multiverse_backend ./vendor/bin/pest --coverage
```

**Test Statistics:**

- Tests include: CRUD operations, employee counts, average salary calculations, CSV validation

### Frontend Tests (Vue/Vitest)

**Run all frontend tests:**

```bash
cd frontend && npm run test:unit
```

**Test Statistics:**

- Tests include: HTTP service, employee service, company service, CSV upload, component rendering

### Code Quality

**Run linting:**

```bash
cd frontend && npm run lint
```

**Run formatting:**

```bash
cd frontend && npm run format
```

## CSV Import Format

The CSV file follow this format:

```csv
Company Name,Employee Name,Email Address,Salary
ACME Corporation,John Doe,johndoe@acme.com,50000
ACME Corporation,Jane Doe,janedoe@acme.com,55000
ACME Corporation,Bob Smith,bobsmith@acme.com,60000
ACME Corporation,Alice Johnson,alicejohnson@acme.com,65000
Stark Industries,Tony Stark,tony@stark.com,100000
Stark Industries,Pepper Potts,pepper@stark.com,75000
Stark Industries,Happy Hogan,happy@stark.com,60000
Stark Industries,Rhodey Rhodes,rhodey@stark.com,80000
Wayne Enterprises,Bruce Wayne,bruce@wayneenterprises.com,90000
Wayne Enterprises,Alfred Pennyworth,alfred@wayneenterprises.com,50000
Wayne Enterprises,Dick Grayson,dick@wayneenterprises.com,60000
Wayne Enterprises,Barbara Gordon,barbara@wayneenterprises.com,55000
```

## API Documentation

### Base URL

```
http://localhost:9090/api
```

### Endpoints

#### Employees

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/employees` | Get all employees |
| GET | `/employees/{id}` | Get single employee |
| POST | `/employees` | Create new employee |
| PUT | `/employees/{id}` | Update employee |
| DELETE | `/employees/{id}` | Delete employee |

**Example Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "full_name": "John Doe",
      "email": "john@acme.com",
      "salary": "50000.00"
    }
  ]
}
```

#### Companies

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/companies` | Get all companies with stats |
| GET | `/companies/{id}` | Get single company with stats |
| POST | `/companies` | Create new company |
| PUT | `/companies/{id}` | Update company |
| DELETE | `/companies/{id}` | Delete company |

**Example Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "ACME Corporation",
      "employee_count": 4,
      "average_salary": "57500.00"
    }
  ]
}
```

#### CSV Upload

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/csv/upload` | Upload and import CSV file |

**Request Body:**

```json
{
  "csv_content": "Company Name,Employee Name,Email Address,Salary\nACME,John,john@acme.com,50000"
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "total_rows": 1,
    "companies_created": 1,
    "employees_imported": 1,
    "employees_failed": 0,
    "errors": []
  }
}
```

### Database Schema

**companies**

- `id` (BIGINT, PK, AUTO_INCREMENT)
- `name` (VARCHAR(191), UNIQUE)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

**employees**

- `id` (BIGINT, PK, AUTO_INCREMENT)
- `company_id` (BIGINT, FK → companies.id)
- `full_name` (VARCHAR(191))
- `email` (VARCHAR(254), UNIQUE)
- `salary` (DECIMAL(12,2))
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

**Indexes:**

- `ix_employees_company` on `company_id`
- `ix_employees_salary` on `salary`

## Development

### Local Development (Without Docker)

**Backend:**

```bash
cd backend
composer install
php -S localhost:8000 -t public
```

**Frontend:**

```bash
cd frontend
npm install
npm run dev
```

## Configuration

### Environment Variables

Configure the application by editing `.env`:

```env
# Database
DB_PORT=3307
MYSQL_ROOT_PASSWORD=root_password
MYSQL_DATABASE=multiverse_db
MYSQL_USER=multiverse_user
MYSQL_PASSWORD=multiverse_password
DB_HOST=db

# Application
APP_PORT=9090
```

## Troubleshooting

### Containers Won't Start

```bash
docker-compose down
docker-compose up -d
docker-compose logs
```

### Database Connection Issues

```bash
# Check container health
docker-compose ps

# View database logs
docker-compose logs db

# Test connection
docker exec multiverse_backend php test-db-connection.php
```

### Port Already in Use

Change ports in `.env`:

```env
APP_PORT=9091
DB_PORT=3308
```

### Reset Everything

```bash
docker-compose down -v
docker-compose up -d
```

**Warning:** This deletes all data!

## **Future Improvements**

### **1. Code & Architecture**

- Extract reusable components (EmployeeTable, CSVUploadForm)
- Move validation and API logic into composables/services
- Consider Pinia for state management

### **2. CRUD & Validation**

- Add employee creation, editing (all fields), and deletion
- Add company management (add/edit/delete)
- Client-side and server-side validation (email format, salary limits, duplicates)

### **3. Search, Filter & Sort**

- Search employees by name/email
- Filter by company
- Sort table columns (name, salary, company)

### **4. UI/UX Enhancements**

- Toast notifications for success/error
- Responsive layout
- CSV drag-and-drop upload with preview

### **5. Export**

- Export employee and company data to CSV or Excel

### **6. Authentication & Security**

- Basic user login  
- Role-based access (admin/viewer)
- CSRF/XSS protection and input sanitization

### **7. DevOps & Testing**

- GitHub Actions CI/CD
- Unit and integration tests for core features
