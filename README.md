# Army DAK Management System (Core PHP + MySQL)

Professional DAK management platform for Army administration workflows.

## Features
- Role-based authentication with session timeout and password hashing.
- Dispatcher incoming DAK entry with automatic control numbers (`DAK-YYYY-XXXX`).
- Head clerk marking and branch assignment.
- Branch action workflow with file number range validation.
- Dashboard metrics + Chart.js monthly trends.
- Global search, pagination utility, and overdue highlighting.
- Reporting screens (pending, branch-wise, speak cases, summary).
- Audit logging for key actions.

## Stack
- PHP 8+
- MySQL 8+
- Bootstrap 5.3
- Chart.js

## Folder Structure
Matches requested modular architecture under `assets/`, `config/`, `helpers/`, `includes/`, `modules/`, `uploads/`, `logs/`, and `sql/`.

## Setup Instructions
1. Create database and schema:
   - Import `sql/dak_management.sql` in MySQL.
2. Configure DB credentials using environment variables (recommended):
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
3. Start local PHP server from project root:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open:
   - `http://localhost:8000`
5. Default admin login:
   - Username: `admin`
   - Password: `admin123`

## Security Controls Implemented
- PDO prepared statements
- CSRF tokens on state-changing forms
- XSS-safe output escaping (`htmlspecialchars`)
- Session inactivity timeout
- Password hashing (`password_hash` + `password_verify`)

## Notes
- PDF/Excel export hooks can be plugged into report endpoints.
- Reminder scheduling for pending >7 days can be added via cron job and mail/SMS integration.
