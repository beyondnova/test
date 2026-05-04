<?php

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = Db::one('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        $_SESSION['uid'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['uid']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return Db::one('SELECT id, name, email, role FROM users WHERE id = ?', [$_SESSION['uid']]);
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::require();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo 'Forbidden: admin only.';
            exit;
        }
    }
}
