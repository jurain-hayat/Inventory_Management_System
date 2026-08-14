# 📦 Inventory Management System

A web-based **Inventory Management System** developed as a university academic project using **PHP, MySQL/MariaDB, HTML5, CSS3, JavaScript, Chart.js, and XAMPP**.

The system provides functionality for managing products, suppliers, sales, inventory stock, users, dashboard analytics, and a custom **Inventory Query Language** developed as part of the Compiler Design component.

---

## 📌 Project Overview

The Inventory Management System provides a centralized interface for managing inventory operations.

### Main Features

- 🔐 User authentication
- 📊 Inventory dashboard
- 📦 Product management
- 🚚 Supplier management
- 💰 Sales management
- 📉 Stock monitoring
- 📈 Revenue statistics
- 📊 Dashboard analytics
- 🔎 Custom Inventory Query Language
- 🧠 Compiler Design integration
- 🔤 Lexical analysis
- 🌳 Syntax analysis
- 🧪 Semantic analysis
- ⚠️ Compiler error handling
- 🗃️ MySQL/MariaDB database
- 🔀 Git/GitHub version control
- 🎨 Responsive web interface

---

# ✨ Features

## 🔐 Authentication

The system includes session-based authentication.

Features:

- User login
- User logout
- Session management
- Protected pages
- Username display
- User role display

Login page:

```text
/auth/login.php
```

---

## 📊 Dashboard

The dashboard provides an overview of the inventory system.

It includes:

- Total Products
- Total Suppliers
- Total Users
- Total Sales
- Low Stock Count
- Out of Stock Count
- Total Inventory Value
- Total Revenue
- Today's Revenue
- Monthly Revenue
- Sales analytics
- Stock status information
- Best-selling products
- Low-stock alerts
- Recent sales
- Quick navigation actions
- Live date and time

Charts are implemented using **Chart.js**.

---

# 📦 Product Management

The product management module allows users to:

- View products
- Add products
- Edit products
- Delete products
- Search products
- Store product descriptions
- Store product prices
- Store product quantities
- Assign suppliers
- Upload product images
- Display stock availability
- Display low-stock status
- Display out-of-stock status

Product module:

```text
/products/
```

---

# 🚚 Supplier Management

The supplier module allows users to:

- Add suppliers
- View suppliers
- Delete suppliers
- Store supplier name
- Store supplier email
- Store supplier phone
- Store supplier address

Supplier module:

```text
/suppliers/
```

---

# 💰 Sales Management

The sales module allows users to:

- Add sales
- View sales
- Select products
- Record quantity sold
- Record unit price
- Calculate total sale price
- Store sale date
- Track revenue
- Display recent sales

Sales module:

```text
/sales/
```

---

# 🧠 Compiler Design Integration

The project includes a custom **Inventory Query Language (IQL)** developed as part of the Compiler Design component.

Users can enter simplified inventory queries such as:

```text
SHOW PRODUCTS WHERE quantity < 10
```

The query is processed through a compiler-style pipeline.

## Compiler Pipeline

```text
Inventory Query
       ↓
Lexical Analysis
       ↓
Syntax Analysis
       ↓
Semantic Analysis
       ↓
SQL Generation
       ↓
Database Execution
       ↓
Inventory Results
```

---

## 🔤 1. Lexical Analysis

The lexer converts the input query into tokens.

Example:

```text
SHOW PRODUCTS WHERE quantity < 10
```

Produces:

```text
KEYWORD    => SHOW
KEYWORD    => PRODUCTS
KEYWORD    => WHERE
IDENTIFIER => quantity
OPERATOR   => <
NUMBER     => 10
```

The lexer also detects invalid characters.

Example:

```text
SHOW PRODUCTS WHERE quantity @ 10
```

Produces:

```text
Lexical Error: Invalid character '@'.
```

---

## 🌳 2. Syntax Analysis

The parser checks whether the sequence of tokens follows the grammar of the Inventory Query Language.

The project uses **Recursive Descent Parsing**.

Example:

```text
SHOW PRODUCTS WHERE quantity < 10
```

Produces a parse tree similar to:

```text
Array
(
    [type] => QUERY
    [condition] => Array
        (
            [type] => EXPRESSION
            [field] => quantity
            [operator] => <
            [value] => 10
        )
)
```

Invalid syntax is reported with a syntax error.

Example:

```text
SHOW PRODUCTS WHERE quantity <
```

Produces:

```text
Syntax Error: Expected a value after operator '<'.
```

---

## 🧪 3. Semantic Analysis

The semantic analyzer checks whether the query makes logical and type-related sense.

It verifies:

- Valid fields
- Valid operators
- Compatible data types
- Symbol-table information
- Logical expressions

For example:

```text
SHOW PRODUCTS WHERE name > 100
```

is syntactically valid, but semantically invalid because the `>` operator is not valid for the `name` field.

The compiler reports:

```text
Semantic Error: Operator '>' is not valid for field 'name'.
```

Another example:

```text
SHOW PRODUCTS WHERE abc < 10
```

produces:

```text
Semantic Error: Unknown field 'abc'.
```

---

## 🔗 4. Logical Expressions

The query language supports logical operators such as:

```text
AND
OR
```

Example:

```text
SHOW PRODUCTS WHERE quantity < 10 AND price > 100
```

The parser generates a logical parse tree:

```text
LOGICAL
├── operator: AND
│
├── quantity < 10
│
└── price > 100
```

Another example:

```text
SHOW PRODUCTS WHERE quantity < 5 OR price > 500
```

---

## 🗃️ 5. Database Execution

After successful lexical, syntax, and semantic analysis, the validated query is converted into a SQL `WHERE` condition.

The system then executes the generated query against the inventory database.

The compiler therefore connects:

```text
Custom Query Language
        ↓
Compiler
        ↓
SQL
        ↓
MySQL/MariaDB
        ↓
Inventory Results
```

---

# ⚠️ Compiler Error Handling

The compiler identifies errors at different stages.

### Lexical Error

Example:

```text
SHOW PRODUCTS WHERE quantity @ 10
```

Output:

```text
Stage: LEXICAL ANALYSIS

Lexical Error: Invalid character '@'.
```

### Syntax Error

Example:

```text
SHOW PRODUCTS WHERE quantity <
```

Output:

```text
Stage: SYNTAX ANALYSIS

Syntax Error: Expected a value after operator '<'.
```

### Semantic Error

Example:

```text
SHOW PRODUCTS WHERE abc < 10
```

Output:

```text
Stage: SEMANTIC ANALYSIS

Semantic Error: Unknown field 'abc'.
```

---

# 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Backend programming |
| MySQL / MariaDB | Database management |
| HTML5 | Page structure |
| CSS3 | User interface and styling |
| JavaScript | Client-side functionality |
| Chart.js | Dashboard charts |
| XAMPP | Local development server |
| Git | Version control |
| GitHub | Source-code hosting |
| Visual Studio Code | Development environment |

---

# 💻 Development Environment

### Operating System

```text
Windows
```

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

# 📂 Project Structure

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
├── compiler/
│   ├── errors.php
│   ├── lexer.php
│   ├── parser.php
│   ├── semantic.php
│   ├── query.php
│   ├── test_lexer.php
│   └── test_parser.php
│
└── assets/
    ├── css/
    │   └── style.css
    │
    └── uploads/
```

---

# 🗄️ Database

The project uses:

```text
inventory_db
```

Main database tables:

```text
users
products
suppliers
sales
```

## Database Relationship

```text
Suppliers
    │
    │ 1
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

# ⚙️ Installation & Setup

## 1. Install XAMPP

Install XAMPP on Windows.

Start:

```text
Apache
MySQL
```

from the XAMPP Control Panel.

---

## 2. Copy the Project

Place the project inside the XAMPP `htdocs` directory.

Example:

```text
C:\xampp\htdocs\inventory
```

The main file should be:

```text
C:\xampp\htdocs\inventory\index.php
```

---

## 3. Create the Database

Open:

```text
http://localhost/phpmyadmin/
```

Create a database named:

```text
inventory_db
```

Then import:

```text
schema.sql
```

into the database.

---

## 4. Configure Database Connection

Open:

```text
db.php
```

A typical XAMPP configuration is:

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

## 5. Run the Application

Open:

```text
http://localhost/inventory/
```

The login page can be accessed through:

```text
http://localhost/inventory/auth/login.php
```

The compiler query analyzer is available at:

```text
http://localhost/inventory/compiler/query.php
```

---

# 🧪 Testing

## Authentication

- [ ] Login works
- [ ] Invalid credentials are rejected
- [ ] Logout works
- [ ] Protected pages require authentication

## Dashboard

- [ ] Dashboard loads
- [ ] Product statistics are correct
- [ ] Supplier statistics are correct
- [ ] Sales statistics are correct
- [ ] Stock information is correct
- [ ] Revenue information is correct
- [ ] Charts load correctly

## Products

- [ ] Products can be viewed
- [ ] Products can be added
- [ ] Products can be edited
- [ ] Products can be deleted
- [ ] Product search works
- [ ] Supplier relationships work
- [ ] Stock status is displayed correctly

## Suppliers

- [ ] Suppliers can be added
- [ ] Suppliers can be viewed
- [ ] Suppliers can be deleted

## Sales

- [ ] Sales can be added
- [ ] Sales can be viewed
- [ ] Quantity is recorded correctly
- [ ] Total price is calculated correctly
- [ ] Revenue updates correctly

## Compiler

- [ ] Valid queries are accepted
- [ ] Invalid characters generate lexical errors
- [ ] Invalid syntax generates syntax errors
- [ ] Unknown fields generate semantic errors
- [ ] Invalid operators generate semantic errors
- [ ] `AND` expressions work
- [ ] `OR` expressions work
- [ ] Valid queries return inventory results

---

# 🔎 Example Inventory Queries

### Query 1 — Low Stock

```text
SHOW PRODUCTS WHERE quantity < 10
```

### Query 2 — Expensive Products

```text
SHOW PRODUCTS WHERE price > 1000
```

### Query 3 — Very Low Stock

```text
SHOW PRODUCTS WHERE quantity <= 5
```

### Query 4 — Multiple Conditions

```text
SHOW PRODUCTS WHERE quantity < 10 AND price > 100
```

### Query 5 — OR Condition

```text
SHOW PRODUCTS WHERE quantity < 5 OR price > 500
```

---

# 🔒 Security Considerations

This project is primarily intended for academic and local development use.

For production deployment, additional security measures should be implemented.

Recommended improvements include:

- Use `password_hash()` for passwords
- Use `password_verify()` for login verification
- Use prepared SQL statements
- Validate user input
- Escape HTML output
- Add CSRF protection
- Configure secure sessions
- Implement proper role-based authorization
- Validate uploaded files
- Protect against SQL injection
- Protect against XSS
- Store production credentials outside the repository
- Never commit passwords or database credentials to GitHub

---

# 🔧 Troubleshooting

## Apache does not start

Check whether another application is using the configured Apache port.

## MySQL does not start

Check whether another MySQL/MariaDB service is using the database port.

## Database connection error

Check:

- MySQL/MariaDB is running
- Database name is correct
- Username is correct
- Password is correct
- `db.php` is configured correctly

## Table does not exist

Import:

```text
schema.sql
```

into:

```text
inventory_db
```

Then verify the tables.

## Foreign Key Error

Make sure referenced records exist before creating dependent records.

For example, a product's `supplier_id` must reference an existing supplier.

## PHP Error

During development, PHP errors can temporarily be enabled with:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Disable detailed error display on production systems.

---

# 🌱 Development Status

## Completed

- [x] Database setup
- [x] Authentication
- [x] Session management
- [x] Dashboard
- [x] Product management
- [x] Supplier management
- [x] Sales management
- [x] Stock monitoring
- [x] Revenue statistics
- [x] Dashboard charts
- [x] Best-selling products
- [x] Low-stock alerts
- [x] Recent sales
- [x] Modern UI
- [x] Git version control
- [x] Inventory Query Language
- [x] Lexical analysis
- [x] Recursive descent parser
- [x] Semantic analysis
- [x] Compiler error handling
- [x] Database query execution

---

# 🚀 Future Improvements

Possible future improvements include:

- [ ] Advanced role-based access control
- [ ] User management
- [ ] Advanced product filtering
- [ ] Pagination
- [ ] PDF reports
- [ ] Excel export
- [ ] Date-range sales reports
- [ ] Inventory reports
- [ ] Supplier performance reports
- [ ] Email notifications
- [ ] Audit logging
- [ ] Automated database backup
- [ ] Automated testing
- [ ] REST API
- [ ] Production deployment

---

# 🌱 Git & GitHub

The project uses Git for version control and GitHub for source-code hosting.

### Check Status

```powershell
git status
```

### Add Changes

```powershell
git add .
```

### Commit Changes

```powershell
git commit -m "Your commit message"
```

### Push Changes

```powershell
git push
```

### Create a Feature Branch

```powershell
git checkout -b feature/compiler-query
```

### Push a New Branch

```powershell
git push -u origin feature/compiler-query
```

---

# 📝 Academic Project Information

**Project Name:** Inventory Management System

**Project Type:** University / Academic Project

**Backend:** PHP

**Database:** MySQL / MariaDB

**Frontend:** HTML5, CSS3, JavaScript

**Charts:** Chart.js

**Local Server:** XAMPP

**Compiler Component:** Custom Inventory Query Language

**Version Control:** Git / GitHub

**Development Environment:** Visual Studio Code

**Platform:** Windows

---

# 📄 License

This project was developed for **educational and academic purposes**.

It may be modified and extended for learning, coursework, experimentation, and personal development.

A formal open-source license such as the MIT License can be added if the project is released for public use.

---

# 👨‍💻 Developer

**Md. Tanvir Khan**

University / Academic Project

---

# 🙏 Acknowledgement

This project was developed as part of university-level academic work to practice:

- Web development
- PHP programming
- Database management
- SQL
- CRUD operations
- Authentication
- Session management
- UI development
- Data visualization
- Compiler design
- Lexical analysis
- Syntax analysis
- Semantic analysis
- Git and GitHub

---

## ❤️ Inventory Management System

**Built with PHP, MySQL/MariaDB, XAMPP, HTML, CSS, JavaScript, Chart.js, and Git.**
