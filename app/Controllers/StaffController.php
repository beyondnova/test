<?php

class StaffController
{
    public function index(): void
    {
        Auth::requireAdmin();
        $staff = Db::all('SELECT id, name, email, role, active, created_at FROM users ORDER BY role DESC, name');
        $title = 'Staff & Users';
        view('staff/index', compact('title', 'staff'));
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $title = 'New User';
        $member = ['name' => '', 'email' => '', 'role' => 'staff', 'active' => 1];
        view('staff/form', compact('title', 'member'));
    }

    public function store(): void
    {
        Auth::requireAdmin();
        csrf_check();
        $data = $this->validate(true);
        try {
            Db::q(
                'INSERT INTO users (name, email, password, role, active) VALUES (?, ?, ?, ?, ?)',
                [$data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $data['active']]
            );
            flash('success', 'User created.');
            redirect('/staff');
        } catch (PDOException $e) {
            flash('error', 'Email must be unique.');
            back();
        }
    }

    public function edit(array $p): void
    {
        Auth::requireAdmin();
        $member = Db::one('SELECT id, name, email, role, active FROM users WHERE id = ?', [(int) $p['id']]);
        if (!$member) { http_response_code(404); echo 'Not found'; return; }
        $title = 'Edit User';
        view('staff/form', compact('title', 'member'));
    }

    public function update(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        $data = $this->validate(false);
        $params = [$data['name'], $data['email'], $data['role'], $data['active']];
        $sql = 'UPDATE users SET name=?, email=?, role=?, active=?';
        if (!empty($data['password'])) {
            $sql .= ', password=?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id=?';
        $params[] = (int) $p['id'];
        try {
            Db::q($sql, $params);
            flash('success', 'User updated.');
            redirect('/staff');
        } catch (PDOException $e) {
            flash('error', 'Email must be unique.');
            back();
        }
    }

    public function destroy(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        if ((int) $p['id'] === (int) ($_SESSION['uid'] ?? 0)) {
            flash('error', 'You cannot delete your own account.');
            redirect('/staff');
        }
        Db::q('UPDATE users SET active = 0 WHERE id = ?', [(int) $p['id']]);
        flash('success', 'User deactivated.');
        redirect('/staff');
    }

    private function validate(bool $requirePassword): array
    {
        $name = trim((string) input('name'));
        $email = trim((string) input('email'));
        $password = (string) input('password', '');
        $role = (string) input('role', 'staff');
        $active = (int) (input('active') ? 1 : 0);
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Name and valid email are required.');
            back();
        }
        if (!in_array($role, ['admin', 'staff'], true)) { flash('error', 'Invalid role.'); back(); }
        if ($requirePassword && strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            back();
        }
        if (!empty($password) && strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            back();
        }
        return compact('name', 'email', 'password', 'role', 'active');
    }
}
