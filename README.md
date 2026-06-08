# VICOBRIDGE - VICOBA Management System

A comprehensive Laravel-based web application for managing multiple groups of VICOBA (Village Community Banks). VICOBRIDGE provides a complete digital solution for VICOBA groups to manage members, collections, loans, expenditures, and generate financial reports.

## Features

### 1. Landing Page
- Home page with feature highlights and system overview
- About page with mission, vision, and core values
- Contact page with inquiry form
- Subscription plans page with pricing

### 2. Group Registration
- Chairman registration with personal details
- Group information (name, registration number, location)
- Subscription plan selection (monthly/quarterly/annually)
- 14-day free trial period
- Immediate payment option via Selcom Payment Gateway

### 3. Profile Wizard (6 Steps)
- Step 1: Personal Details (name, gender, phone, email)
- Step 2: Location Details (region, district, ward, village, cell leader, LG chairperson)
- Step 3: Guarantor Details (name, phone, relationship)
- Step 4: Marital Status & Family (spouse, dependents)
- Step 5: Inheritor Details
- Step 6: Verification & Terms Acceptance

### 4. Dashboards
- **Super Admin Dashboard**: Total groups, active groups, expired groups, revenue (yearly/monthly), member statistics, charts
- **Group Dashboard**: Total members, active/inactive members, collections by fund, loans summary, expenditure, defaulters, charts

### 5. Group Management (Super Admin)
- Pending approvals with trial tracking
- Approved groups management
- Subscription renewals with expiration notifications
- Group suspension/activation
- SMS notification to chairpersons

### 6. Member Management
- Complete member registration with profile picture upload
- Personal details, location, guarantor, family, dependents, inheritors
- Member status tracking (active, inactive, suspended, deceased)
- Search and filter functionality

### 7. Collections Management
- Record collections for multiple funds (Hisa, Jamii, Rejesho, Ada, Faini, Mradi)
- Payment channel tracking (cash, bank, mobile money, Selcom)
- Control number and transaction reference support
- Bulk collection entry

### 8. Loan Management
- Loan application with amount, term, interest rate
- Loan approval/rejection workflow
- Loan disbursement with automatic EMI schedule generation
- Repayment tracking with payment recording
- Support for Flat Rate, Reducing Balance, and Simple interest
- Overdue repayment detection
- Loan status: pending, approved, disbursed, repaying, completed, defaulted

### 9. Expenditure Management
- Record expenses with categories
- Receipt attachment support
- Approval workflow (pending, approved, rejected)
- Category-based tracking

### 10. Reports
- **Admin Reports**: Revenue collection report, group subscription report
- **Group Reports**: Monthly collection report, loans report, year-end report, expenditure report, financial statement/balance sheet
- Date range filtering
- PDF export capability

### 11. User Management
- System users management (super admin)
- Group users management with role assignment
- Activity logs tracking
- Role-based access control

### 12. Settings
- Subscription plans management (add/edit/delete)
- Collection funds management (Hisa, Jamii, Rejesho, Ada, Faini, Mradi)
- Calendar year management with current year selection
- Loan types configuration with interest rate settings

## Technical Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates, Bootstrap 5, Chart.js
- **Database**: MySQL 8.0
- **Authentication**: Laravel Auth with Spatie Permission
- **Payment Gateway**: Selcom API integration
- **PDF Export**: Laravel DomPDF
- **Excel Export**: Laravel Excel (Maatwebsite)

## Installation

### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Node.js & NPM (optional, for asset compilation)

### Step 1: Clone and Install Dependencies
```bash
cd vicobridge
composer install
```

### Step 2: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```env
DB_DATABASE=vicobridge
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Database Setup
```bash
php artisan migrate --seed
```

This will create all tables and seed:
- Default subscription plans (Monthly, Quarterly, Annual)
- Default roles and permissions
- Super admin user (admin@vicobridge.com / admin123)

### Step 4: Storage Link
```bash
php artisan storage:link
```

### Step 5: Serve the Application
```bash
php artisan serve
```

Access the application at `http://localhost:8000`

## Default Login Credentials

### Super Admin
- **Email**: admin@vicobridge.com
- **Password**: admin123

## Directory Structure

```
vicobridge/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # All controllers
│   │   └── Middleware/      # Custom middleware
│   ├── Models/              # Eloquent models
│   └── Services/            # Business logic services
├── bootstrap/
├── config/
├── database/
│   ├── migrations/          # All database migrations
│   └── seeders/             # Database seeders
├── public/                  # Web root
├── resources/
│   └── views/               # Blade templates
├── routes/
│   └── web.php              # Web routes
└── .env                     # Environment configuration
```

## Key Services

### SelcomPaymentService
Handles all payment gateway operations:
- Create payment orders
- Check payment status
- Process callbacks
- Generate control numbers
- Direct payment initiation

### LoanService
Manages loan calculations:
- EMI calculation (flat, reducing balance, simple)
- Repayment schedule generation
- Loan application processing
- Disbursement workflow
- Overdue repayment updates

## Role-Based Access Control

### Roles
- **super-admin**: Full system access
- **group-admin**: Group management access
- **chairperson**: Group leadership access
- **secretary**: Record keeping access
- **treasurer**: Financial management access
- **member**: Basic viewing access

### Permissions
Over 50 granular permissions covering all modules (dashboard, members, collections, loans, expenditures, reports, users, settings).

## API Integration

### Selcom Payment Gateway
The application integrates with Selcom for:
- Subscription payments
- Collection payment processing
- Control number generation
- Payment status verification

Configure in `.env`:
```env
SELCOM_BASE_URL=https://api.selcommobile.com
SELCOM_API_KEY=your_api_key
SELCOM_API_SECRET=your_api_secret
SELCOM_VENDOR_ID=your_vendor_id
```

## Calendar Year System

The application uses a calendar year system where:
- Each group can have multiple calendar years
- One calendar year is marked as "current"
- All collections, loans, and expenditures are tied to a calendar year
- Users can switch between calendar years via the top navigation dropdown

## License

This is a proprietary software developed for VICOBA group management.

## Support

For support and inquiries:
- Email: support@vicobridge.com
- Phone: +255 700 123 456
