<?php

declare(strict_types=1);

session_start();

spl_autoload_register(function ($class) {
    $base = __DIR__ . '/../app/';
    $candidates = [
        $base . $class . '.php',
        $base . 'Controllers/' . $class . '.php',
    ];
    foreach ($candidates as $file) {
        if (is_file($file)) { require $file; return; }
    }
});

require __DIR__ . '/../app/Helpers.php';

Db::conn();

$router = new Router();

// Auth
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/', 'DashboardController@index');
$router->get('/reports', 'DashboardController@reports');

// Rooms
$router->get('/rooms', 'RoomController@index');
$router->get('/rooms/create', 'RoomController@create');
$router->post('/rooms', 'RoomController@store');
$router->get('/rooms/{id}/edit', 'RoomController@edit');
$router->post('/rooms/{id}', 'RoomController@update');
$router->post('/rooms/{id}/delete', 'RoomController@destroy');

// Clients
$router->get('/clients', 'ClientController@index');
$router->get('/clients/create', 'ClientController@create');
$router->post('/clients', 'ClientController@store');
$router->get('/clients/{id}', 'ClientController@show');
$router->get('/clients/{id}/edit', 'ClientController@edit');
$router->post('/clients/{id}', 'ClientController@update');
$router->post('/clients/{id}/delete', 'ClientController@destroy');

// Bookings
$router->get('/bookings', 'BookingController@index');
$router->get('/bookings/create', 'BookingController@create');
$router->post('/bookings', 'BookingController@store');
$router->get('/bookings/{id}', 'BookingController@show');
$router->post('/bookings/{id}/check-in', 'BookingController@checkIn');
$router->post('/bookings/{id}/check-out', 'BookingController@checkOut');
$router->post('/bookings/{id}/cancel', 'BookingController@cancel');

// Invoices
$router->get('/invoices', 'InvoiceController@index');
$router->get('/invoices/{id}', 'InvoiceController@show');
$router->post('/invoices/{id}/update', 'InvoiceController@update');
$router->post('/invoices/{id}/pay', 'InvoiceController@pay');
$router->post('/invoices/{id}/payments/{payment_id}/delete', 'InvoiceController@deletePayment');

// Staff
$router->get('/staff', 'StaffController@index');
$router->get('/staff/create', 'StaffController@create');
$router->post('/staff', 'StaffController@store');
$router->get('/staff/{id}/edit', 'StaffController@edit');
$router->post('/staff/{id}', 'StaffController@update');
$router->post('/staff/{id}/delete', 'StaffController@destroy');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
