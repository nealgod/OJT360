# OJT360 - Administrator Management & Security

This guide covers how to secure and manage the primary Administrator account after installation.

---

## 1. Initial Access
After running the migrations or importing the SQL file, use these default credentials to log in:
- **Email**: `ojt3604dmin@gmail.com`
- **Password**: `12345678`

**Important**: Change these credentials immediately using one of the methods below.

---

## 2. Method 1: Update via System Seeder (Preferred for Fresh Install)
To start with a clean database using your own official credentials:

1. Open `database/seeders/AdminUserSeeder.php`.
2. Update the email and password values in the code:
   ```php
   // database/seeders/AdminUserSeeder.php
   $admin = User::firstOrCreate(
       ['email' => 'your-new-email@example.com'], // Change here
       [
           'name' => 'OJT360 Admin',
           'password' => Hash::make('your-secure-password'), // Change here
           'role' => 'admin',
           'email_verified_at' => now(),
       ]
   );
   ```
3. Run this command in your terminal:
   ```bash
   # This wipes the database and re-runs all migrations with your new admin details
   php artisan migrate:fresh --seed
   ```
   *Note: This will delete all existing data in the database.*

---

## 3. Method 2: Manual Reset via phpMyAdmin
If you lose access or don't have terminal access, reset the account through the database:

1. Go to phpMyAdmin and find the **`users`** table.
2. Find the row where **`role`** is `admin`.
3. Click **Edit**.
4. **Email**: Enter the new email address.
5. **Password**: 
   - Change the Function for the password field to **`BCRYPT`**.
   - Type your new password in the Value field.
6. Click **Go** to save.

---

## 4. Security Notes
- **Password Strength**: Use 12+ characters with a mix of letters, numbers, and symbols.
- **Access**: Don't share the main admin account; if more people need access, create them as coordinators.
- **Production Settings**: Keep `APP_DEBUG=false` in your `.env` to hide technical errors from users.
