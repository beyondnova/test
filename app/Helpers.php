<?php

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][$type] = $message;
}

function take_flash(): array
{
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        echo 'CSRF token mismatch';
        exit;
    }
}

function view(string $name, array $data = []): void
{
    extract($data);
    $content = (function () use ($name, $data) {
        extract($data);
        ob_start();
        require __DIR__ . '/Views/' . $name . '.php';
        return ob_get_clean();
    })();
    require __DIR__ . '/Views/layouts/app.php';
}

function view_raw(string $name, array $data = []): void
{
    extract($data);
    require __DIR__ . '/Views/' . $name . '.php';
}

function money(float $n): string
{
    return '$' . number_format($n, 2);
}

function nights_between(string $in, string $out): int
{
    $a = new DateTime($in);
    $b = new DateTime($out);
    $diff = (int) $a->diff($b)->format('%r%a');
    return max(1, $diff);
}

function active(string $prefix): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($prefix === '/') return $path === '/' ? 'active' : '';
    return str_starts_with($path, $prefix) ? 'active' : '';
}

function back(): void
{
    redirect($_SERVER['HTTP_REFERER'] ?? '/');
}

function input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}
