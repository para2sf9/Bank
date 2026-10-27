# PGi Bankings & Finance - Bank Management System (MongoDB Atlas Edition)

A complete banking management system with customer and admin interfaces, powered by MongoDB Atlas.

## Features

### Customer Module
- Account Registration & Login
- Dashboard with account overview
- Deposit / Withdraw funds
- Transfer money to other accounts
- View transaction history
- Apply for loans (Home, Auto, Education, Personal, SME)
- View cards
- Profile management
- Support ticket system

### Admin Module
- Admin Dashboard with statistics
- Manage all customers
- View all accounts
- View all transactions
- Approve/Reject loan requests
- View support tickets

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Customer | demo@pgibank.com | demo123 |
| Admin | admin@pgibank.com | admin123 |

## Installation

### Prerequisites
- PHP 7.4 or higher
- MongoDB PHP Extension installed
- Composer installed
- Apache/Nginx web server

### Step 1: Install MongoDB PHP Extension
```bash
sudo pecl install mongodb
```
Then add this line to your `php.ini`:
```ini
extension=mongodb.so
```

### Step 2: Install MongoDB PHP Library
```bash
composer require mongodb/mongodb
```

### Step 3: Create MongoDB Atlas Cluster (Free Tier)
1. Go to https://www.mongodb.com/cloud/atlas
2. Sign up and create a FREE M0 cluster
3. Create a database user (remember username & password)
4. Go to Network Access -> Add IP Address -> Allow from anywhere: `0.0.0.0/0`
5. Click Connect -> Drivers -> PHP -> Copy your connection string

### Step 4: Configure Connection
Open `includes/config.php` and replace the connection string with yours:
```php
$MONGODB_URI = "mongodb+srv://your_username:your_password@yourcluster.mongodb.net/pgi_bank?retryWrites=true&w=majority";
```

### Step 5: Deploy
Place all files in your web server directory (e.g., `htdocs/pgi-bank/`).
Access `index.php` in your browser.

### Step 6: Test Connection
Open `pages/connection-test.php` in your browser to verify MongoDB is connected.

## File Structure
```
pgi-bank/
  index.php              # Homepage
  .htaccess              # Apache config
  includes/
    config.php           # MongoDB & app config
  pages/
    login.php            # Login
    register.php         # Registration
    forgot-password.php  # Password reset
    dashboard.php        # Customer dashboard
    accounts.php         # My accounts
    transactions.php     # Transaction history
    transfer.php         # Money transfer
    deposit.php          # Deposit funds
    withdraw.php         # Withdraw funds
    loans.php            # Loan application
    cards.php            # My cards
    profile.php          # Profile settings
    support.php          # Support tickets
    services.php         # Public services page
    about.php            # About page
    contact.php          # Contact page
    connection-test.php  # MongoDB connection test
  admin/
    dashboard.php        # Admin dashboard
    users.php            # All customers
    accounts.php         # All accounts
    transactions.php     # All transactions
    loans.php            # Loan requests
    support.php          # Support tickets
    loan-action.php      # Approve/Reject loans
  api/
    logout.php           # Logout handler
  assets/
    css/
      style.css          # Main stylesheet
      auth.css           # Auth page styles
      dashboard.css      # Dashboard styles
      forms.css          # Form styles
      responsive.css     # Responsive styles
    js/
      main.js            # Main JavaScript
      auth.js            # Auth page JavaScript
  database/
    MONGODB_SCHEMA.md    # MongoDB schema documentation
```

## MongoDB Collections
- `users` - Registered users
- `accounts` - Bank accounts
- `transactions` - All transactions
- `loans` - Loan applications
- `cards` - Debit/Credit cards
- `tickets` - Support tickets
- `activity_log` - User activity logs

## Demo Mode
If MongoDB is not connected, the system automatically falls back to demo mode. You can still login and explore all features with demo data.

## License
This project is for educational and demonstration purposes.
