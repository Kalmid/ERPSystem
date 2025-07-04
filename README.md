ERP System – PHP & MySQL
========================

This is a mini ERP system built with PHP, MySQL, and Bootstrap. It allows users to manage customers, items, and invoices, and generate reports with filters and dynamic dropdowns.

------------------------
Features
------------------------
- Customer registration
- Item management (Add/Edit/Delete)
- Category & subcategory handling with dynamic loading
- Invoice creation and management
- Reports with date filters:
  • Invoice Report
  • Invoice Item Report
  • Item Report
- Error handling & input validation
- Bootstrap-based responsive UI

------------------------
Setup Instructions
------------------------

Prerequisites:
--------------
- XAMPP installed
- PHP 7.0+
- MySQL

Steps to Run Locally:
---------------------
1. Clone this repository:
   git clone https://github.com/Kalmid/erp-system.git

2. Move the project into your XAMPP `htdocs` folder:
   C:\xampp\htdocs\erp-system

3. Create the Database:
   - Open http://localhost:8080/phpmyadmin
   - Create a new database named: erp_db
   - Import the SQL dump file provided in the `/database` folder

4. Update the Database Connection:
   Open the file `db/connection.php` and edit the following:
   $conn = new mysqli("localhost", "root", "", "erp_db");

5. Run the Project:
   Visit in your browser:
   http://localhost:8080/erp-system

------------------------
Project Structure
------------------------
erp-system/
├── customer/
├── item/
├── invoice/
├── reports/
├── db/
├── assets/
└── README.md

------------------------
Assumptions
------------------------
- Subcategories are dynamically loaded based on the selected category via AJAX.
- Districts are stored as IDs and joined with the district table for names.
- Item table stores category and subcategory as IDs, not names.
- Invoice data is linked using the invoice_master table.
- No login/authentication system is implemented for simplicity.

------------------------
Future Improvements
------------------------
- Add authentication and role-based access
- Export reports to PDF or Excel
- UI enhancements (icons, search, modals)
- Pagination for long lists

------------------------
Author
------------------------
Kalmi Dilara  

------------------------
License
------------------------
This project is created for educational and academic use only.
