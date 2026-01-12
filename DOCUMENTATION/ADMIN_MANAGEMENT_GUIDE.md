# OJT360 - Admin Management Guide

## Default Admin Credentials
- Email: `ojt3604dmin@gmail.com`
- Password: `12345678`

---

## Change Admin via Database

1. Open phpMyAdmin
2. Select `ojt360` database
3. Open `users` table
4. Find the admin row (role = `admin`)
5. Click Edit
6. Change `email` field as needed
7. For password: Use BCRYPT function, enter new password
8. Click Go

---

## Change Admin via Seeder

File: `database/seeders/AdminUserSeeder.php`

```php
['email' => 'your-new-email@example.com'],
[
    'name' => 'New Admin Name',
    'password' => Hash::make('your-new-password'),
]
```

Then run:
```
php artisan db:seed --class=AdminUserSeeder
```

---

## Source Code
GitHub: https://github.com/nealgod/OJT360
