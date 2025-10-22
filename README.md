# Multiverse Payroll

A comprehensive employee and company management system with RESTful API and CSV import capabilities.

## Tech Stack Version Information

### Backend

- **PHP** - 8.4.13
- **MariaDB** - 12.0.2

### Frontend

- **Vue** - 3.5.22

### Infrastructure

- **Docker** - 28.5.1
- **Nginx** - 1.29.2

## Quick Start

### Prerequisites

- Docker Desktop 20.10+

### Installation

1. **Clone the repository:**

   ```bash
   git clone <repository-url>
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

- **API Endpoint:** http://localhost:9090/api
- **Database:** localhost:3307


## Testing

### Run All Tests

```bash
docker exec multiverse_backend composer test
```

### Run Specific Test Suites

```bash
# Unit tests only
docker exec multiverse_backend composer test:unit

# Feature tests only
docker exec multiverse_backend composer test:feature
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

## Development

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
