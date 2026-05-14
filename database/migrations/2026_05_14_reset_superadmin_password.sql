-- ----------------------------------------------------------------------------
-- Reset superadmin@pedicare.com password to "Admin@123".
--
-- The previous seed hash did not match the documented default password, so
-- nobody could actually log in with the superadmin account. Run this once
-- against any existing database that was seeded before this fix.
--
-- The hash below is a verified bcrypt ($2y$10$) of "Admin@123" and was also
-- updated in the SQL seed (u854009390_sllsggt (1).sql) so fresh installs
-- come up with the correct credentials.
--
-- Usage:
--   mysql -u <user> -p <database> < database/migrations/2026_05_14_reset_superadmin_password.sql
-- ----------------------------------------------------------------------------

UPDATE users
SET password = '$2y$10$I5m78rij9.msBIOVQJNcbuZprpKnz8QepbLjvw5dgmiopLxdBI2/O',
    force_password_change = 0,
    login_attempts = 0,
    locked_until = NULL,
    status = 'active'
WHERE email = 'superadmin@pedicare.com'
  AND user_type = 'SUPERADMIN';
