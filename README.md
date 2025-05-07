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
git clone https://github.com/parmodkumarr/customer-portal.git
cd customer-portal
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_portal
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Also configure mailtrap or any smtp in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=5abc751*******
MAIL_PASSWORD=91585d2*******
```

6. Run migrations:
```bash
php artisan migrate --seed
```

8. Run This
```
php artisan passport:client --personal

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
# generate with Swagger
`php artisan l5-swagger:generate`

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
