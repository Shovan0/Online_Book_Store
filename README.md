# Online Book Store

## Project Structure

- `app/`
  - `config/` - application configuration and database connection.
  - `controllers/` - controller classes for request handling.
  - `models/` - data models and database entities.
  - `middleware/` - request middleware and validation.
  - `helpers/` - reusable utility functions.
  - `services/` - business services and integrations.
- `public/`
  - `assets/css/` - stylesheets.
  - `assets/js/` - frontend scripts.
  - `assets/images/` - image assets.
  - `uploads/` - uploaded files.
  - `index.php` - application entry point.
- `views/`
  - `layouts/` - layout templates.
  - `auth/` - authentication views.
  - `admin/` - admin views.
  - `books/` - book-related views.
  - `cart/` - shopping cart views.
  - `orders/` - order views.
- `.env.example` - environment variable template.
- `.gitignore` - files to ignore in Git.
- `composer.json` - Composer dependencies and autoload configuration.

## Installation

1. Clone or copy the repository into `C:\Projects\Online Book Store`.
2. Install PHP dependencies using Composer.

```bash
composer install
```

## Environment Configuration

1. Copy `.env.example` to `.env`.
2. Add your PostgreSQL/Supabase credentials and Supabase values.

Example:

```dotenv
APP_NAME=Online Book Store
APP_ENV=development
APP_DEBUG=true

DB_HOST=your_db_host
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SUPABASE_URL=https://your-supabase-url
SUPABASE_ANON_KEY=your_anon_key
```

## Running the Project

From the project root, start the built-in PHP server:

```bash
php -S localhost:8000 -t public
```

Then open `http://localhost:8000` in your browser.

## Notes

- This foundation includes the base structure, configuration, and database connection sample.
- Business logic, authentication, CRUD operations, and shopping cart modules are not implemented yet.
"# Online_Book_Store" 
