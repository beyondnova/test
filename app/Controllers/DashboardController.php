<?php

class DashboardController
{
    public function index(): void
    {
        Auth::require();
        $stats = [
            'rooms_total'      => (int) Db::scalar('SELECT COUNT(*) FROM rooms'),
            'rooms_available'  => (int) Db::scalar("SELECT COUNT(*) FROM rooms WHERE status='available'"),
            'rooms_occupied'   => (int) Db::scalar("SELECT COUNT(*) FROM rooms WHERE status='occupied'"),
            'rooms_maint'      => (int) Db::scalar("SELECT COUNT(*) FROM rooms WHERE status='maintenance'"),
            'bookings_active'  => (int) Db::scalar("SELECT COUNT(*) FROM bookings WHERE status IN ('booked','checked_in')"),
            'bookings_today'   => (int) Db::scalar("SELECT COUNT(*) FROM bookings WHERE date(check_in)=date('now')"),
            'clients'          => (int) Db::scalar('SELECT COUNT(*) FROM clients'),
            'invoices_unpaid'  => (int) Db::scalar("SELECT COUNT(*) FROM invoices WHERE status IN ('pending','partial')"),
            'revenue_total'    => (float) (Db::scalar('SELECT COALESCE(SUM(amount),0) FROM payments') ?? 0),
            'outstanding'      => (float) (Db::scalar('SELECT COALESCE(SUM(total - paid_amount),0) FROM invoices WHERE status IN (\'pending\',\'partial\')') ?? 0),
        ];
        $recentBookings = Db::all(
            'SELECT b.*, c.name AS client_name, r.number AS room_number
             FROM bookings b
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             ORDER BY b.id DESC LIMIT 8'
        );
        $unpaidInvoices = Db::all(
            'SELECT i.*, c.name AS client_name, r.number AS room_number
             FROM invoices i
             JOIN bookings b ON b.id = i.booking_id
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             WHERE i.status IN (\'pending\',\'partial\')
             ORDER BY i.id DESC LIMIT 6'
        );
        $title = 'Dashboard';
        view('dashboard/index', compact('title', 'stats', 'recentBookings', 'unpaidInvoices'));
    }

    public function reports(): void
    {
        Auth::requireAdmin();
        $monthly = Db::all("
            SELECT strftime('%Y-%m', paid_at) AS month, SUM(amount) AS total, COUNT(*) AS count
            FROM payments
            GROUP BY month
            ORDER BY month DESC LIMIT 12
        ");
        $byRoomType = Db::all("
            SELECT r.type, COUNT(b.id) AS bookings, COALESCE(SUM(i.total),0) AS revenue
            FROM rooms r
            LEFT JOIN bookings b ON b.room_id = r.id
            LEFT JOIN invoices i ON i.booking_id = b.id
            GROUP BY r.type
        ");
        $topClients = Db::all('
            SELECT c.name, c.email, COUNT(b.id) AS bookings, COALESCE(SUM(i.total),0) AS spent
            FROM clients c
            LEFT JOIN bookings b ON b.client_id = c.id
            LEFT JOIN invoices i ON i.booking_id = b.id
            GROUP BY c.id
            ORDER BY spent DESC LIMIT 10
        ');
        $title = 'Reports';
        view('dashboard/reports', compact('title', 'monthly', 'byRoomType', 'topClients'));
    }
}
