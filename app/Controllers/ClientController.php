<?php

class ClientController
{
    public function index(): void
    {
        Auth::require();
        $q = trim((string) input('q', ''));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $clients = Db::all(
                'SELECT * FROM clients WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR id_number LIKE ? ORDER BY name',
                [$like, $like, $like, $like]
            );
        } else {
            $clients = Db::all('SELECT * FROM clients ORDER BY name');
        }
        $title = 'Clients';
        view('clients/index', compact('title', 'clients', 'q'));
    }

    public function show(array $p): void
    {
        Auth::require();
        $client = Db::one('SELECT * FROM clients WHERE id = ?', [(int) $p['id']]);
        if (!$client) { http_response_code(404); echo 'Not found'; return; }
        $bookings = Db::all(
            'SELECT b.*, r.number AS room_number, i.id AS invoice_id, i.total, i.paid_amount, i.status AS inv_status
             FROM bookings b
             JOIN rooms r ON r.id = b.room_id
             LEFT JOIN invoices i ON i.booking_id = b.id
             WHERE b.client_id = ? ORDER BY b.id DESC',
            [$client['id']]
        );
        $totals = [
            'bookings' => count($bookings),
            'spent'    => (float) (Db::scalar(
                'SELECT COALESCE(SUM(i.total),0) FROM invoices i JOIN bookings b ON b.id=i.booking_id WHERE b.client_id=?',
                [$client['id']]
            ) ?? 0),
            'owing'    => (float) (Db::scalar(
                "SELECT COALESCE(SUM(i.total - i.paid_amount),0) FROM invoices i JOIN bookings b ON b.id=i.booking_id
                 WHERE b.client_id=? AND i.status IN ('pending','partial')",
                [$client['id']]
            ) ?? 0),
        ];
        $title = 'Client: ' . $client['name'];
        view('clients/show', compact('title', 'client', 'bookings', 'totals'));
    }

    public function create(): void
    {
        Auth::require();
        $title = 'New Client';
        $client = ['name' => '', 'email' => '', 'phone' => '', 'id_number' => '', 'address' => '', 'notes' => ''];
        view('clients/form', compact('title', 'client'));
    }

    public function store(): void
    {
        Auth::require();
        csrf_check();
        $data = $this->validate();
        Db::q(
            'INSERT INTO clients (name, email, phone, id_number, address, notes) VALUES (?, ?, ?, ?, ?, ?)',
            [$data['name'], $data['email'], $data['phone'], $data['id_number'], $data['address'], $data['notes']]
        );
        $id = (int) Db::conn()->lastInsertId();
        flash('success', 'Client created.');
        redirect('/clients/' . $id);
    }

    public function edit(array $p): void
    {
        Auth::require();
        $client = Db::one('SELECT * FROM clients WHERE id = ?', [(int) $p['id']]);
        if (!$client) { http_response_code(404); echo 'Not found'; return; }
        $title = 'Edit Client';
        view('clients/form', compact('title', 'client'));
    }

    public function update(array $p): void
    {
        Auth::require();
        csrf_check();
        $data = $this->validate();
        Db::q(
            'UPDATE clients SET name=?, email=?, phone=?, id_number=?, address=?, notes=? WHERE id=?',
            [$data['name'], $data['email'], $data['phone'], $data['id_number'], $data['address'], $data['notes'], (int) $p['id']]
        );
        flash('success', 'Client updated.');
        redirect('/clients/' . (int) $p['id']);
    }

    public function destroy(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        $hasBookings = (int) Db::scalar('SELECT COUNT(*) FROM bookings WHERE client_id = ?', [(int) $p['id']]);
        if ($hasBookings > 0) {
            flash('error', 'Cannot delete a client with bookings.');
            redirect('/clients');
        }
        Db::q('DELETE FROM clients WHERE id = ?', [(int) $p['id']]);
        flash('success', 'Client deleted.');
        redirect('/clients');
    }

    private function validate(): array
    {
        $name = trim((string) input('name'));
        $email = trim((string) input('email'));
        $phone = trim((string) input('phone'));
        $id_number = trim((string) input('id_number'));
        $address = trim((string) input('address'));
        $notes = trim((string) input('notes'));
        if ($name === '') {
            flash('error', 'Name is required.');
            back();
        }
        return compact('name', 'email', 'phone', 'id_number', 'address', 'notes');
    }
}
