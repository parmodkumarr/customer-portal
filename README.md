# Customer Portal

A Laravel-based Customer Database Management Portal with OAuth2 authentication and MFA support.

## Features

- RESTful API with OAuth2 authentication
- Multi-factor authentication (MFA)
- Customer CRUD operations
- Modern UI with Bootstrap 5
- Comprehensive test coverage

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and NPM (for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd customer-portal
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install frontend dependencies:
```bash
npm install
npm run dev
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:
```bash
php artisan migrate
```

8. Install Passport:
```bash
php artisan passport:install
```

## Running the Application

1. Start the development server:
```bash
php artisan serve
```

2. Access the application at `http://localhost:8000`

## Testing

Run the test suite:
```bash
php artisan test
```

## API Documentation

The API endpoints are:

### Authentication
- POST `/api/login` - Login with email and password
- POST `/api/verify-mfa` - Verify MFA token
- POST `/api/logout` - Logout (requires authentication)

### Customers
- GET `/api/customers` - List all customers
- POST `/api/customers` - Create a new customer
- GET `/api/customers/{id}` - Get customer details
- PUT `/api/customers/{id}` - Update customer
- DELETE `/api/customers/{id}` - Delete customer

All customer endpoints require OAuth2 authentication.

## Security

- OAuth2 authentication using Laravel Passport
- Multi-factor authentication with email verification
- CSRF protection
- Input validation
- SQL injection prevention through Eloquent ORM

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request
