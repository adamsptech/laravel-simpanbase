# SimpanBase - Maintenance Management System (CMMS)

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-4.4-FFA500?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge" alt="License"></a>
</p>

<p align="center">
  <a href="#-about">About</a> •
  <a href="#-features">Features</a> •
  <a href="#-requirements">Requirements</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-usage">Usage</a> •
  <a href="#-technologies">Technologies</a>
</p>

---


## 🙏 Acknowledgments

This Laravel version of SimpanBase is a port/rewrite of the original CodeIgniter 4 version:
- **[Original SimpanBase (CI4)](https://github.com/mafazaazihi/simpanbase)** - The original CodeIgniter 4 implementation by [mafazaazihi](https://github.com/mafazaazihi)

Special thanks to:
- [Laravel](https://laravel.com) - The PHP Framework
- [Filament](https://filamentphp.com) - The Admin Panel Framework
- All contributors and maintainers

---

## 📋 About

**SimpanBase** is a comprehensive **Computerized Maintenance Management System (CMMS)** and **Enterprise Asset Management (EAM)** solution built with Laravel and Filament. It helps organizations efficiently manage maintenance operations, track equipment performance, monitor inventory, and optimize maintenance workflows.

> **Note**: This is a Laravel port of the original [SimpanBase](https://github.com/mafazaazihi/simpanbase) project, which was built with CodeIgniter 4. This version leverages Laravel's modern features and Filament's powerful admin panel capabilities.

### What is SimpanBase?

SimpanBase is designed for manufacturing plants, facilities, and organizations that need to:
- **Manage work orders** and maintenance tasks efficiently
- **Track equipment** performance and downtime
- **Monitor inventory** levels and parts usage
- **Schedule preventive maintenance** activities
- **Generate reports** on equipment availability and OEE (Overall Equipment Effectiveness)
- **Streamline approval workflows** for maintenance operations
- **Maintain audit trails** of all maintenance activities

---

## ✨ Features

### 🔧 Work Order Management
- **Multi-level approval workflow** (Supervisor → Manager → Customer)
- **Recurring maintenance tasks** (Daily, Weekly, Monthly)
- **Priority-based task assignment** (Low, Medium, High)
- **Task status tracking** (Open, Submitted, Closed)
- **File attachments** for documentation
- **Shift-based scheduling**
- **Due date tracking** with overdue alerts

### 🏭 Equipment Management
- **Equipment registry** with serial numbers and categories
- **Location hierarchy** (Location → Sublocation → Equipment)
- **Warranty tracking** with expiration alerts
- **Supplier management**
- **Equipment history** and maintenance records

### 📊 Reporting & Analytics
- **OEE (Overall Equipment Effectiveness) Reports**
  - Availability, Performance, Quality metrics
  - Monthly OEE tracking per equipment
  - Plant breakdown analysis
- **Machine Downtime Reports**
  - Downtime frequency tracking
  - Availability percentage calculations
  - Root cause analysis
- **Dashboard** with role-based statistics
  - Task overview (Open, Pending, Closed, Overdue)
  - Equipment status
  - Performance metrics

### 📦 Inventory Management
- **Parts stock tracking** with minimum quantity alerts
- **Part usage history**
- **Part additions** and stock movements
- **Low stock notifications**
- **SAP integration** support (SAP ID field)
- **Price tracking**

### ✅ Maintenance Checklists
- **Type checks** and maintenance checklists
- **Preventive Maintenance (PM)** scheduling
- **Period-based PM** management
- **Checklist templates** for recurring tasks

### 🔔 Notifications & Alerts
- **Overdue work order alerts** (Daily)
- **Low stock notifications** (Weekly)
- **Warranty expiration warnings** (Daily)
- **Weekly maintenance schedule** reminders
- **Approval request notifications**
- **Task assignment notifications**

### 👥 User Management & Security
- **Role-based access control** (Engineer, Supervisor, Manager, Planner, Customer)
- **User activity audit logging**
- **Role-specific dashboards** and permissions
- **Active/inactive user management**

### 📈 Additional Features
- **Machine downtime tracking** with root cause analysis
- **Audit trail** for all data changes
- **Export capabilities** (Excel/CSV)
- **Real-time filtering** and search
- **Responsive admin panel** built with Filament

---

## 📋 Requirements

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x and **npm** >= 9.x
- **SQLite** (included) or MySQL/PostgreSQL
- **Web server** (Apache/Nginx) or PHP built-in server

---

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-simpanbase.git
cd laravel-simpanbase
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

Copy the environment file and configure it:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure your database:

```env
DB_CONNECTION=sqlite
# Or for MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=simpanbase
# DB_USERNAME=root
# DB_PASSWORD=

# Mail configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@simpanbase.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 4: Create Database (SQLite)

If using SQLite, create the database file:

```bash
touch database/database.sqlite
```

### Step 5: Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will:
- Create all database tables
- Seed initial data (roles, admin user, categories, etc.)

### Step 6: Install Node Dependencies and Build Assets

```bash
npm install
npm run build
```

### Step 7: Create Storage Link

```bash
php artisan storage:link
```

### Step 8: Set Up Scheduled Tasks (Optional but Recommended)

Add this to your crontab for scheduled notifications:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or on Windows, use Task Scheduler to run:
```
php artisan schedule:run
```

### Step 9: Start the Development Server

```bash
php artisan serve
```

Or use the convenient dev script:

```bash
composer run dev
```

This will start:
- Laravel development server (http://localhost:8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server

### Step 10: Access the Application

1. Open your browser and navigate to: `http://localhost:8000/admin`
2. Login with the default admin credentials (check `database/seeders/AdminUserSeeder.php`)

---

## 💻 Usage

### Default Admin Account

After running seeders, you can log in with:
- **Email**: Check `database/seeders/AdminUserSeeder.php`
- **Password**: Check `database/seeders/AdminUserSeeder.php`

### Quick Start Guide

1. **Set Up Locations**: Navigate to Locations → Create Location → Create Sublocations
2. **Add Equipment**: Go to Equipment → Create Equipment → Link to Sublocation
3. **Create Work Orders**: Navigate to Work Orders → Create Work Order
4. **Manage Inventory**: Go to Part Stocks → Add Parts → Set Minimum Quantities
5. **View Reports**: Check Dashboard, OEE Reports, and Machine Downtime Reports

### Scheduled Commands

The system includes several scheduled commands:

- **Daily at 8:00 AM**: Overdue work order alerts
- **Daily at 9:00 AM**: Warranty expiration checks
- **Monday at 8:00 AM**: Low stock alerts
- **Thursday at 8:00 AM**: Weekly maintenance schedule
- **1st of each month at 1:00 AM**: Generate monthly OEE data

Run manually:
```bash
php artisan app:send-overdue-alerts
php artisan app:send-low-stock-alert
php artisan app:check-warranty-expiration
php artisan app:send-weekly-maintenance-schedule
php artisan app:generate-monthly-oee
```

---

## 🛠 Technologies

- **[Laravel 12](https://laravel.com)** - PHP Framework
- **[Filament 4.4](https://filamentphp.com)** - Admin Panel Builder
- **[PHP 8.2+](https://php.net)** - Programming Language
- **[SQLite](https://sqlite.org)** - Database (can be switched to MySQL/PostgreSQL)
- **[Tailwind CSS 4](https://tailwindcss.com)** - CSS Framework
- **[Vite](https://vitejs.dev)** - Build Tool
- **[Livewire](https://livewire.laravel.com)** - Full-stack Framework (via Filament)

---

## 📁 Project Structure

```
laravel-simpanbase/
├── app/
│   ├── Console/Commands/      # Scheduled commands
│   ├── Filament/               # Filament admin panel resources
│   ├── Models/                 # Eloquent models
│   ├── Notifications/          # Email notifications
│   ├── Observers/              # Model observers
│   └── Policies/               # Authorization policies
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   └── views/                  # Blade templates
└── routes/
    └── console.php             # Scheduled tasks
```

---

## 🔒 Security

- Password hashing using Laravel's built-in hashing
- CSRF protection enabled
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Role-based access control (RBAC)
- Audit logging for data changes

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📞 Support

For support, open an issue in the repository.

---

<p align="center">Made with ❤️ using Laravel & Filament</p>
