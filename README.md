# 📦 Inventory Management System

A web-based **Inventory Management System** developed as a university/academic project using **PHP, MySQL/MariaDB, HTML5, CSS3, JavaScript, and XAMPP**.

The system is designed to manage products, suppliers, sales, stock levels, users, and inventory-related information through a simple web dashboard.

---

## 📌 Project Overview

The Inventory Management System provides a centralized interface for managing inventory operations.

The project includes:

- User authentication
- Dashboard with inventory statistics
- Product management
- Supplier management
- Sales management
- Stock monitoring
- Revenue and inventory-value summaries
- Sales analytics
- Best-selling product information
- Low-stock alerts
- Recent sales information
- Responsive modern UI
- Git version control

This project was created for **university/academic purposes** and can also serve as a foundation for a larger inventory application.

---

## ✨ Features

### 🔐 Authentication

- User login
- User logout
- Session-based authentication
- Protected dashboard access
- User information and role display

### 📊 Dashboard

The dashboard provides an overview of the current inventory system, including:

- Total Products
- Total Suppliers
- Total Users
- Total Sales
- Low Stock count
- Out of Stock count
- Total Inventory Value
- Total Revenue
- Today's Revenue
- Current Month's Revenue
- Last 12 months sales/revenue chart
- Stock status chart
- Top 5 best-selling products
- Low-stock alerts
- Recent sales
- Quick action buttons
- Live date and time

### 📦 Product Management

- View products
- Add products
- Edit products
- Delete products
- Search products
- Display product price
- Display product quantity
- Supplier information
- Product availability status
- Low-stock status
- Out-of-stock status
- Product image support

### 🚚 Supplier Management

- Add suppliers
- View suppliers
- Delete suppliers
- Store supplier name
- Store supplier email
- Store supplier phone
- Store supplier address

### 💰 Sales Management

- Add sales
- View sales
- Record product quantity sold
- Record unit price
- Calculate total sale price
- Store sale date
- Display recent sales
- Track total revenue
- Track daily revenue
- Track monthly revenue

### 📈 Inventory Analytics

The dashboard provides visual information about:

- Monthly revenue performance
- Current stock condition
- Available stock
- Low stock
- Out-of-stock products
- Best-selling products

Charts are implemented using **Chart.js**.

---

## 🛠️ Technologies Used

| Technology      | Purpose                         |
| --------------- | ------------------------------- |
| PHP 8.5.9       | Backend/server-side programming |
| MySQL / MariaDB | Database management             |
| HTML5           | Page structure                  |
| CSS3            | Styling and responsive UI       |
| JavaScript      | Client-side functionality       |
| Chart.js        | Dashboard charts                |
| XAMPP           | Local development environment   |
| Git             | Version control                 |
| GitHub          | Source-code hosting             |
| VS Code         | Development environment         |

---

## 💻 Development Environment

### Operating System

Windows

### PHP

```text
PHP 8.5.9
```

### XAMPP

```text
XAMPP Control Panel v3.3.0
```

### Database

```text
MySQL / MariaDB
```

### Editor

```text
Visual Studio Code
```

---

## 📂 Project Structure

```text
inventory/
│
├── .gitignore
├── README.md
├── schema.sql
├── db.php
├── index.php
├── dashboard.php
│
├── auth/
│   ├── check.php
│   ├── login.php
│   └── logout.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── products/
│   ├── add.php
│   ├── edit.php
│   ├── delete.php
│   └── view.php
│
├── suppliers/
│   ├── add.php
│   ├── delete.php
│   └── view.php
│
├── sales/
│   ├── add.php
│   └── view.php
│
└── assets/
    ├── css/
    │   └── style.css
    │
    └── uploads/
```

> The exact contents of the project folders may change as development continues.

---

## 🗄️ Database

The project uses a database named:

```text
inventory_db
```

The database contains the main entities required for the inventory system, including:

- `users`
- `products`
- `suppliers`
- `sales`

The database schema is provided in:

```text
schema.sql
```

### Main Relationships

```text
Suppliers
    │
    │ 1
    │
    │
    └──────────< Products
                    │
                    │ 1
                    │
                    └──────────< Sales
```

A supplier can be associated with multiple products.

A product can have multiple sales records.

---

## ⚙️ Installation & Setup

Follow these steps to run the project locally.

### 1. Install XAMPP

Install XAMPP on Windows.

Start the following services from the XAMPP Control Panel:

```text
Apache
MySQL
```

---

### 2. Copy the Project

Place the project inside the XAMPP `htdocs` directory.

Example:

```text
C:\xampp\htdocs\inventory
```

The main project file should therefore be:

```text
C:\xampp\htdocs\inventory\index.php
```

---

### 3. Create the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Create a database named:

```text
inventory_db
```

Alternatively, use the SQL file provided with the project:

```text
schema.sql
```

Import `schema.sql` into the `inventory_db` database.

---

### 4. Configure the Database Connection

Open:

```text
db.php
```

The database connection should contain the correct local MySQL/MariaDB configuration.

Typical XAMPP configuration:

```php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "inventory_db"
);
```

If your MySQL/MariaDB installation uses a password, update the password accordingly.

---

### 5. Start the Application

Open a browser and visit:

```text
http://localhost/inventory/
```

If the project opens through the dashboard directly, you can also use:

```text
http://localhost/inventory/dashboard.php
```

---

## 🔑 Login

The system uses session-based authentication.

A user must log in before accessing protected pages such as the dashboard.

The login page is located at:

```text
auth/login.php
```

You can access it through:

```text
http://localhost/inventory/auth/login.php
```

> For security, do not publish real passwords, database credentials, or private configuration information in the GitHub repository.

---

## 🧪 Testing Checklist

Before submitting or demonstrating the project, test the following.

### Authentication

- [ ] Login works
- [ ] Incorrect login credentials are rejected
- [ ] Logout works
- [ ] Protected pages redirect unauthenticated users
- [ ] Session information displays correctly

### Dashboard

- [ ] Dashboard loads without PHP errors
- [ ] Product count is correct
- [ ] Supplier count is correct
- [ ] User count is correct
- [ ] Sales count is correct
- [ ] Low-stock count is correct
- [ ] Out-of-stock count is correct
- [ ] Inventory value is correct
- [ ] Revenue values are correct
- [ ] Sales chart loads
- [ ] Stock chart loads
- [ ] Best-selling products display
- [ ] Low-stock alerts display
- [ ] Recent sales display
- [ ] Live date and time work

### Products

- [ ] Product list loads
- [ ] Add product works
- [ ] Edit product works
- [ ] Delete product works
- [ ] Product search works
- [ ] Supplier relationship works
- [ ] Stock status displays correctly
- [ ] Product image functionality works if enabled

### Suppliers

- [ ] Supplier list loads
- [ ] Add supplier works
- [ ] Delete supplier works
- [ ] Supplier information is stored correctly

### Sales

- [ ] New sale can be created
- [ ] Product is selected correctly
- [ ] Quantity is recorded correctly
- [ ] Total price is calculated correctly
- [ ] Sale appears in the sales list
- [ ] Dashboard revenue updates
- [ ] Recent sales update
- [ ] Stock quantity behaves correctly

### UI

- [ ] Navigation links work
- [ ] Buttons work
- [ ] Tables display correctly
- [ ] Forms display correctly
- [ ] Responsive layout works
- [ ] No broken images
- [ ] No console errors
- [ ] No PHP warnings/notices are visible

---

## 🔒 Security Considerations

This project is intended primarily for academic/local development use.

For a production deployment, additional security improvements should be implemented.

Recommended improvements include:

- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- Prepared SQL statements for user input
- Input validation
- Output escaping using `htmlspecialchars()`
- CSRF protection
- Secure session configuration
- Authorization checks based on user roles
- Secure file-upload validation
- Protection against SQL injection
- Protection against XSS
- Protection against unauthorized file access
- Production database credentials stored outside the source repository

Never commit sensitive credentials to GitHub.

---

## 🌐 Running the Project

After starting Apache and MySQL/MariaDB in XAMPP:

```text
http://localhost/inventory/
```

The application runs locally through the XAMPP Apache server.

---

## 🔧 Troubleshooting

### Apache does not start

Check whether another application is already using port 80 or another configured Apache port.

---

### MySQL does not start

Check whether another MySQL/MariaDB service is using the configured database port.

---

### Database connection error

Check:

- MySQL/MariaDB is running
- Database name is correct
- Username is correct
- Password is correct
- Host is correct
- `db.php` contains the correct configuration

---

### Table does not exist

Make sure `schema.sql` has been imported into:

```text
inventory_db
```

Then verify the database tables in phpMyAdmin or the MariaDB command line.

---

### Foreign key error

Make sure referenced records exist before creating dependent records.

For example, a product using a supplier ID must reference an existing supplier.

---

### PHP page shows a blank screen

Temporarily enable PHP error reporting during development:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Do not leave detailed error display enabled on a production server.

---

## 🧭 Development Roadmap

### Completed

- [X] Database setup
- [X] Authentication
- [X] Session management
- [X] Dashboard
- [X] Product management
- [X] Supplier management
- [X] Sales management
- [X] Stock monitoring
- [X] Revenue statistics
- [X] Dashboard charts
- [X] Best-selling product section
- [X] Low-stock alerts
- [X] Recent sales
- [X] Modern dashboard UI
- [X] Git version control

### Future Improvements

- [ ] Advanced role-based access control
- [ ] Admin/user permission management
- [ ] Advanced product filtering
- [ ] Pagination improvements
- [ ] Advanced reports
- [ ] PDF report generation
- [ ] Excel report export
- [ ] Sales report by date range
- [ ] Inventory report
- [ ] Supplier performance reports
- [ ] Email notifications for low stock
- [ ] Improved audit logging
- [ ] Automated database backup
- [ ] Production deployment
- [ ] Automated testing
- [ ] API integration

---

## 📊 Project Workflow

The basic workflow of the system is:

```text
User Login
    ↓
Dashboard
    ↓
┌───────────────┬───────────────┬───────────────┐
│   Products    │   Suppliers   │     Sales     │
└───────┬───────┴───────┬───────┴───────┬───────┘
        │               │               │
        ↓               ↓               ↓
   Stock Data     Supplier Data     Sales Data
        │               │               │
        └───────────────┼───────────────┘
                        ↓
                  Dashboard
                        ↓
              Reports / Analytics
```

---

## 🌱 Git & GitHub

Git is used for version control.

### Initialize the repository

From the project directory:

```powershell
cd C:\xampp\htdocs\inventory
git init
```

### Check project status

```powershell
git status
```

### Add files

```powershell
git add .
```

### Create a commit

```powershell
git commit -m "Initial Inventory Management System"
```

### Rename the default branch

```powershell
git branch -M main
```

### Connect to GitHub

After creating a GitHub repository, add its URL:

```powershell
git remote add origin YOUR_GITHUB_REPOSITORY_URL
```

Example:

```powershell
git remote add origin https://github.com/YOUR_USERNAME/inventory-management-system.git
```

### Push the project

```powershell
git push -u origin main
```

> Replace `YOUR_GITHUB_REPOSITORY_URL` and `YOUR_USERNAME` with your actual GitHub repository information.

---

## 📁 Recommended `.gitignore`

The project should not commit temporary files, secrets, or generated uploads.

A recommended `.gitignore` is:

```gitignore
# XAMPP / local environment
xampp/

# Environment / secrets
.env

# PHP temporary files
*.log

# Uploaded/generated files
assets/uploads/*
!assets/uploads/.gitkeep

# OS files
Thumbs.db
.DS_Store

# Editor settings
.vscode/
```

Review this file before pushing the project to GitHub.

---

## 📝 Academic Project Information

**Project Name:** Inventory Management System

**Project Type:** University / Academic Project

**Primary Purpose:** Inventory and sales management

**Backend:** PHP

**Database:** MySQL / MariaDB

**Local Server:** XAMPP

**Frontend:** HTML5, CSS3, JavaScript

**Charts:** Chart.js

**Version Control:** Git

**Development Environment:** Visual Studio Code

**Platform:** Windows

---

## 📄 License

This project is created for **educational and academic purposes**.

You may modify and extend the project for learning, coursework, experimentation, and personal development.

If this project is later released publicly, the license can be changed to a formal open-source license such as the **MIT License**.

---

## 👨‍💻 Developer

**Md. Tanvir Khan**

University / Academic Project

---

## ⭐ Acknowledgement

This project was developed as part of university-level academic work to practice:

- Web development
- PHP programming
- Database management
- SQL
- CRUD operations
- Authentication
- Session management
- Frontend UI development
- Data visualization
- Git and version control

---

## 🚀 Future Vision

The long-term goal of the project is to evolve from a basic academic inventory application into a more complete inventory and business management platform with:

- Advanced analytics
- Role-based permissions
- Automated reports
- Notifications
- Data export
- Backup systems
- Better security
- API support
- Production deployment

---

**Inventory Management System — Academic Project**

Built with ❤️ using PHP, MySQL/MariaDB, XAMPP, HTML, CSS, JavaScript, Chart.js, and Git.
