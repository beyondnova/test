<?php

class RoomController
{
    public function index(): void
    {
        Auth::require();
        $rooms = Db::all('SELECT * FROM rooms ORDER BY number');
        $title = 'Rooms';
        view('rooms/index', compact('title', 'rooms'));
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $title = 'New Room';
        $room = ['number' => '', 'type' => 'single', 'rate' => '', 'status' => 'available', 'description' => ''];
        view('rooms/form', compact('title', 'room'));
    }

    public function store(): void
    {
        Auth::requireAdmin();
        csrf_check();
        $data = $this->validate();
        try {
            Db::q(
                'INSERT INTO rooms (number, type, rate, status, description) VALUES (?, ?, ?, ?, ?)',
                [$data['number'], $data['type'], $data['rate'], $data['status'], $data['description']]
            );
            flash('success', 'Room created.');
            redirect('/rooms');
        } catch (PDOException $e) {
            flash('error', 'Could not save room (number must be unique).');
            redirect('/rooms/create');
        }
    }

    public function edit(array $p): void
    {
        Auth::requireAdmin();
        $room = Db::one('SELECT * FROM rooms WHERE id = ?', [(int) $p['id']]);
        if (!$room) { http_response_code(404); echo 'Not found'; return; }
        $title = 'Edit Room ' . $room['number'];
        view('rooms/form', compact('title', 'room'));
    }

    public function update(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        $data = $this->validate();
        Db::q(
            'UPDATE rooms SET number=?, type=?, rate=?, status=?, description=? WHERE id=?',
            [$data['number'], $data['type'], $data['rate'], $data['status'], $data['description'], (int) $p['id']]
        );
        flash('success', 'Room updated.');
        redirect('/rooms');
    }

    public function destroy(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        $hasBookings = (int) Db::scalar('SELECT COUNT(*) FROM bookings WHERE room_id = ?', [(int) $p['id']]);
        if ($hasBookings > 0) {
            flash('error', 'Cannot delete a room that has bookings.');
            redirect('/rooms');
        }
        Db::q('DELETE FROM rooms WHERE id = ?', [(int) $p['id']]);
        flash('success', 'Room deleted.');
        redirect('/rooms');
    }

    private function validate(): array
    {
        $number = trim((string) input('number'));
        $type = (string) input('type');
        $rate = (float) input('rate');
        $status = (string) input('status');
        $description = trim((string) input('description'));
        if ($number === '' || !in_array($type, ['single','double','suite','deluxe'], true)
            || !in_array($status, ['available','occupied','maintenance'], true) || $rate < 0) {
            flash('error', 'Please fill all fields correctly.');
            back();
        }
        return compact('number', 'type', 'rate', 'status', 'description');
    }
}
