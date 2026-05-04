<?php

class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) redirect('/');
        $title = 'Login';
        $noLayout = true;
        view('auth/login', compact('title', 'noLayout'));
    }

    public function login(): void
    {
        csrf_check();
        $email = trim((string) input('email'));
        $password = (string) input('password');
        if (!Auth::attempt($email, $password)) {
            flash('error', 'Invalid email or password.');
            $_SESSION['_old']['email'] = $email;
            redirect('/login');
        }
        unset($_SESSION['_old']);
        flash('success', 'Welcome back, ' . $_SESSION['name'] . '.');
        redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
