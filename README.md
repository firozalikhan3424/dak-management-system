# Army DAK Management System

Professional, military-style DAK (official correspondence) management system built with **Core PHP + MySQL + Bootstrap 5**.

## Key Highlights
- Clear **Admin Panel** and **User Panel** separation.
- Role-based access: `admin`, `dispatcher`, `head_clerk`, `branch_clerk`, `officer`, `co`.
- Secure authentication with password hashing and session timeout.
- Sequential control number generation using configurable format (`DAK-YYYY-XXXX`).
- Complete DAK workflow: incoming entry → head clerk marking → branch action → reporting.
- Search, overdue highlighting, speak-case tracking, and export to Excel/PDF (print).

## Project Structure
```text
dak-management/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   └── sidebar.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── branches.php
│   ├── sub_branches.php
│   └── dak_number_settings.php
├── user/
│   ├── dashboard.php
│   ├── incoming_dak.php
│   ├── mark_dak.php
│   ├── branch_action.php
│   ├── dak_list.php
│   └── reports.php
├── auth/
│   ├── login.php
│   └── logout.php
├── uploads/
└── sql/
    └── dak_management.sql
```

## Installation (XAMPP / Apache / PHP 8+)
1. Copy project into web root (e.g., `htdocs/dak-management`).
2. Create database from SQL:
   - Import `sql/dak_management.sql` (creates database `dak_system`).
3. Configure environment (optional):
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
4. Start Apache and MySQL.
5. Open: `http://localhost/dak-management/auth/login.php`

## Default Demo Credentials
- `admin / admin123`
- `dispatcher1 / admin123`
- `headclerk1 / admin123`
- `brancha1 / admin123`
- `co1 / admin123`

## Security Controls
- PDO prepared statements
- CSRF tokens on forms
- Password hashing (`password_hash`, `password_verify`)
- Session authentication + inactivity timeout
- Output escaping to reduce XSS risk

## Reports & Export
- Pending DAK
- Branch-wise pending
- Speak cases
- Date-wise incoming
- Reply pending
- Export as CSV (Excel) and PDF via browser print
