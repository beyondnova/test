<?php

class BookingController
{
    public function index(): void
    {
        Auth::require();
        $status = (string) input('status', '');
        $where = '';
        $params = [];
        if ($status !== '') {
            $where = ' WHERE b.status = ?';
            $params[] = $status;
        }
        $bookings = Db::all(
            "SELECT b.*, c.name AS client_name, r.number AS room_number,
                    i.id AS invoice_id, i.status AS inv_status, i.total AS inv_total, i.paid_amount AS inv_paid
             FROM bookings b
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             LEFT JOIN invoices i ON i.booking_id = b.id
             $where
             ORDER BY b.id DESC",
            $params
        );
        $title = 'Bookings';
        view('bookings/index', compact('title', 'bookings', 'status'));
    }

    public function show(array $p): void
    {
        Auth::require();
        $booking = $this->findFull((int) $p['id']);
        if (!$booking) { http_response_code(404); echo 'Not found'; return; }
        $invoice = Db::one('SELECT * FROM invoices WHERE booking_id = ?', [$booking['id']]);
        $title = 'Booking #' . $booking['id'];
        view('bookings/show', compact('title', 'booking', 'invoice'));
    }

    public function create(): void
    {
        Auth::require();
        $clients = Db::all('SELECT id, name FROM clients ORDER BY name');
        $rooms = Db::all("SELECT id, number, type, rate FROM rooms WHERE status != 'maintenance' ORDER BY number");
        $selectedClient = (int) input('client_id', 0);
        $title = 'New Booking';
        $booking = [
            'client_id' => $selectedClient,
            'room_id' => '',
            'check_in' => date('Y-m-d'),
            'check_out' => date('Y-m-d', strtotime('+1 day')),
            'notes' => '',
        ];
        view('bookings/form', compact('title', 'clients', 'rooms', 'booking'));
    }

    public function store(): void
    {
        Auth::require();
        csrf_check();
        $data = $this->validate();
        $room = Db::one('SELECT * FROM rooms WHERE id = ?', [$data['room_id']]);
        if (!$room) { flash('error', 'Room not found.'); back(); }

        $overlap = (int) Db::scalar(
            "SELECT COUNT(*) FROM bookings
             WHERE room_id = ? AND status IN ('booked','checked_in')
             AND NOT (date(check_out) <= date(?) OR date(check_in) >= date(?))",
            [$data['room_id'], $data['check_in'], $data['check_out']]
        );
        if ($overlap > 0) {
            flash('error', 'Room is already booked for those dates.');
            back();
        }

        $nights = nights_between($data['check_in'], $data['check_out']);
        Db::q(
            'INSERT INTO bookings (client_id, room_id, check_in, check_out, nights, rate, status, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['client_id'], $data['room_id'], $data['check_in'], $data['check_out'],
                $nights, (float) $room['rate'], 'booked', $data['notes'], $_SESSION['uid'] ?? null,
            ]
        );
        $bid = (int) Db::conn()->lastInsertId();

        $room_charge = $nights * (float) $room['rate'];
        $tax = round($room_charge * 0.1, 2);
        $total = $room_charge + $tax;
        Db::q(
            'INSERT INTO invoices (booking_id, room_charge, extra_charges, discount, tax, total, paid_amount, status)
             VALUES (?, ?, 0, 0, ?, ?, 0, ?)',
            [$bid, $room_charge, $tax, $total, 'pending']
        );

        flash('success', 'Booking created with invoice.');
        redirect('/bookings/' . $bid);
    }

    public function checkIn(array $p): void
    {
        Auth::require();
        csrf_check();
        $booking = Db::one('SELECT * FROM bookings WHERE id = ?', [(int) $p['id']]);
        if (!$booking) { flash('error', 'Not found.'); redirect('/bookings'); }
        if ($booking['status'] !== 'booked') {
            flash('error', 'Only booked reservations can be checked in.');
            back();
        }
        Db::q("UPDATE bookings SET status='checked_in' WHERE id=?", [$booking['id']]);
        Db::q("UPDATE rooms SET status='occupied' WHERE id=?", [$booking['room_id']]);
        flash('success', 'Client checked in.');
        back();
    }

    public function checkOut(array $p): void
    {
        Auth::require();
        csrf_check();
        $booking = Db::one('SELECT * FROM bookings WHERE id = ?', [(int) $p['id']]);
        if (!$booking) { flash('error', 'Not found.'); redirect('/bookings'); }
        if ($booking['status'] !== 'checked_in') {
            flash('error', 'Only checked-in bookings can be checked out.');
            back();
        }
        Db::q("UPDATE bookings SET status='checked_out' WHERE id=?", [$booking['id']]);
        Db::q("UPDATE rooms SET status='available' WHERE id=?", [$booking['room_id']]);
        flash('success', 'Client checked out.');
        back();
    }

    public function cancel(array $p): void
    {
        Auth::require();
        csrf_check();
        $booking = Db::one('SELECT * FROM bookings WHERE id = ?', [(int) $p['id']]);
        if (!$booking) { flash('error', 'Not found.'); redirect('/bookings'); }
        if (in_array($booking['status'], ['checked_out', 'cancelled'], true)) {
            flash('error', 'Cannot cancel a completed booking.');
            back();
        }
        Db::q("UPDATE bookings SET status='cancelled' WHERE id=?", [$booking['id']]);
        if ($booking['status'] === 'checked_in') {
            Db::q("UPDATE rooms SET status='available' WHERE id=?", [$booking['room_id']]);
        }
        Db::q("UPDATE invoices SET status='cancelled' WHERE booking_id=? AND status IN ('pending','partial')", [$booking['id']]);
        flash('success', 'Booking cancelled.');
        back();
    }

    private function validate(): array
    {
        $client_id = (int) input('client_id');
        $room_id = (int) input('room_id');
        $check_in = (string) input('check_in');
        $check_out = (string) input('check_out');
        $notes = trim((string) input('notes'));
        if ($client_id <= 0 || $room_id <= 0 || !$check_in || !$check_out) {
            flash('error', 'Please fill in all required fields.');
            back();
        }
        if (strtotime($check_out) <= strtotime($check_in)) {
            flash('error', 'Check-out must be after check-in.');
            back();
        }
        return compact('client_id', 'room_id', 'check_in', 'check_out', 'notes');
    }

    private function findFull(int $id): ?array
    {
        return Db::one(
            'SELECT b.*, c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                    r.number AS room_number, r.type AS room_type, u.name AS created_by_name
             FROM bookings b
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             LEFT JOIN users u ON u.id = b.created_by
             WHERE b.id = ?',
            [$id]
        );
    }
}
