# Admin User Credentials

## Super Admin
- **Email:** admin@cms.local
- **Password:** admin123
- **Role:** Super Admin
- **Permissions:** All (17 permissions)

## Content Editor
- **Email:** editor@cms.local
- **Password:** editor123
- **Role:** Admin
- **Permissions:** Content, Themes, Plugins, Users, Settings

## Content Mentor
- **Email:** mentor@cms.local
- **Password:** mentor123
- **Role:** Mentor
- **Permissions:** Page Management (5 permissions)

## Test User
- **Email:** test@example.com
- **Password:** password
- **Role:** None (assign manually)

---

## Admin Dashboard Access

After seeding, you can access the admin dashboard at:

```
http://localhost:8000/admin
```

Login with any of the admin credentials above.

---

## Features Available

### Super Admin Can:
- ✅ Create, edit, delete pages
- ✅ Publish/unpublish pages
- ✅ Manage themes (activate/deactivate)
- ✅ Manage plugins (install/activate/deactivate)
- ✅ Manage users and roles
- ✅ Manage permissions
- ✅ Configure settings
- ✅ View audit logs

### Admin Can:
- ✅ Create, edit, delete pages
- ✅ Publish/unpublish pages
- ✅ Manage themes
- ✅ Manage plugins
- ✅ Manage users (limited)
- ✅ Configure settings

### Mentor Can:
- ✅ View pages
- ✅ Create pages
- ✅ Edit pages
- ✅ Delete pages
- ✅ Publish pages

### Student Can:
- ✅ View published pages only

---

## Security Notes

**IMPORTANT:** These are development credentials. In production:

1. Change all default passwords
2. Use strong, unique passwords
3. Enable 2FA for admin accounts
4. Regularly rotate credentials
5. Audit user access logs
6. Disable unused accounts

---

## Password Reset

To reset a password:

```bash
php artisan tinker
>>> $user = User::where('email', 'admin@cms.local')->first();
>>> $user->password = Hash::make('new-password');
>>> $user->save();
```

---

## Creating Additional Admin Users

```bash
php artisan tinker
>>> $user = User::create([
...   'name' => 'New Admin',
...   'email' => 'newadmin@cms.local',
...   'password' => Hash::make('password'),
...   'email_verified_at' => now()
... ]);
>>> $role = Role::where('slug', 'admin')->first();
>>> $user->assignRole($role->id);
```
