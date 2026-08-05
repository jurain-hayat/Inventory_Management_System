# Running this in XAMPP + VS Code

## 1. Place the folder
Copy this whole `inventory` folder into XAMPP's `htdocs` directory:
- Windows: `C:\xampp\htdocs\inventory`
- Mac: `/Applications/XAMPP/htdocs/inventory`
- Linux: `/opt/lampp/htdocs/inventory`

## 2. Start services
Open **XAMPP Control Panel** and click **Start** next to both **Apache** and **MySQL**.

## 3. Create the database
Go to `http://localhost/phpmyadmin`, click the **SQL** tab, paste the contents
of `schema.sql` (included in this folder), and click **Go**. This creates the
`inventory_db` database with the `products`, `suppliers`, and `sales` tables,
plus one sample product so the dashboard isn't empty.

## 4. Open in VS Code
`code .` from inside the `inventory` folder, or File > Open Folder. The PHP
extension isn't required to run it (XAMPP's Apache handles execution) — it's
just useful for syntax highlighting and autocomplete. The **PHP Intelephense**
extension is a good free option.

## 5. Open it in your browser
Visit `http://localhost/inventory/`. Click **Login**, use:
- Username: `admin`
- Password: `1234`

From the dashboard you can manage Products, Suppliers, and Sales.

## Notes
- `db.php` is already set for XAMPP's defaults (`localhost`, user `root`, no
  password). No changes needed unless you set a MySQL root password.
- `connect.php` (from the original upload) was **left out** of this package —
  it contained live credentials for a remote InfinityFree database. If you
  need it for deployment later, keep those credentials out of anything you
  share or commit to version control.
- This is a learning/demo project. Before using it anywhere real, see the
  security notes from earlier in this conversation (SQL injection, plaintext
  password, no CSRF protection, GET-based deletes).
