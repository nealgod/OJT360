# OJT360 - Admin Credentials Management Guide

This document provides the technical instructions for managing the Master Administrator credentials for the OJT360 system.

## Default Credentials
The system is currently configured with the following default administrator account:
- **Email**: `ojt3604dmin@gmail.com`
- **Password**: `12345678`

---

## Method 1: Changing via Database Seeder
To change the default values that are applied during a fresh system installation or database reset, modify the seeder file.

1.  **File Location**: `database/seeders/AdminUserSeeder.php`
2.  **Configuration Lines** (Lines 18-21):
    ```php
    ['email' => 'ojt3604dmin@gmail.com'],
    [
        'name' => 'OJT360 Admin',
        'password' => Hash::make('12345678'),
    ]
    ```
3.  Modify the email and password strings as desired.
4.  Apply changes by running:
    ```bash
    php artisan db:seed --class=AdminUserSeeder
    ```

---

## Method 2: Manual Update via phpMyAdmin
To update credentials directly in the database without modifying the source code:

1.  Access **phpMyAdmin** and select the `ojt360` database.
2.  Open the `users` table and locate the record with `role` = `admin`.
3.  Click **Edit**.
4.  **Field: `email`**: Enter the new administrator email.
5.  **Field: `password`**: 
    - Change the **Function** dropdown to **BCRYPT** (required for Laravel authentication).
    - Enter the new password string in the value box.
6.  Click **Go** to save changes.
