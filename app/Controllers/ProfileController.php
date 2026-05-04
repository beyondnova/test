<?php

class ProfileController
{
    public function show(): void
    {
        Auth::require();
        $user = Db::one('SELECT id, name, email, role, created_at FROM users WHERE id = ?', [(int) $_SESSION['uid']]);
        $title = 'My Profile';
        view('profile/show', compact('title', 'user'));
    }

    public function update(): void
    {
        Auth::require();
        csrf_check();
        $name = trim((string) input('name'));
        $email = trim((string) input('email'));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Name and a valid email are required.');
            redirect('/profile');
        }
        try {
            Db::q('UPDATE users SET name=?, email=? WHERE id=?', [$name, $email, (int) $_SESSION['uid']]);
            $_SESSION['name'] = $name;
            flash('success', 'Profile updated.');
        } catch (PDOException $e) {
            flash('error', 'That email is already in use.');
        }
        redirect('/profile');
    }

    public function password(): void
    {
        Auth::require();
        csrf_check();
        $current = (string) input('current_password');
        $new = (string) input('new_password');
        $confirm = (string) input('confirm_password');

        $row = Db::one('SELECT password FROM users WHERE id = ?', [(int) $_SESSION['uid']]);
        if (!$row || !password_verify($current, $row['password'])) {
            flash('error', 'Current password is incorrect.');
            redirect('/profile');
        }
        if (strlen($new) < 6) {
            flash('error', 'New password must be at least 6 characters.');
            redirect('/profile');
        }
        if ($new !== $confirm) {
            flash('error', 'New password and confirmation do not match.');
            redirect('/profile');
        }
        Db::q('UPDATE users SET password=? WHERE id=?', [password_hash($new, PASSWORD_DEFAULT), (int) $_SESSION['uid']]);
        flash('success', 'Password changed.');
        redirect('/profile');
    }
}
