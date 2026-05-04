<?php

class CalendarController
{
    public function index(): void
    {
        Auth::require();
        $monthInput = (string) input('month', date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            $monthInput = date('Y-m');
        }
        $start = new DateTimeImmutable($monthInput . '-01');
        $end = $start->modify('first day of next month');
        $days = (int) $start->format('t');

        $rooms = Db::all('SELECT * FROM rooms ORDER BY number');
        $bookings = Db::all(
            "SELECT b.id, b.room_id, b.check_in, b.check_out, b.status, c.name AS client_name
             FROM bookings b
             JOIN clients c ON c.id = b.client_id
             WHERE b.status IN ('booked','checked_in','checked_out')
               AND date(b.check_in) < date(?)
               AND date(b.check_out) > date(?)",
            [$end->format('Y-m-d'), $start->format('Y-m-d')]
        );

        $occupancy = [];
        foreach ($rooms as $r) {
            $occupancy[$r['id']] = array_fill(1, $days, null);
        }
        foreach ($bookings as $b) {
            $roomId = (int) $b['room_id'];
            if (!isset($occupancy[$roomId])) continue;
            $bIn = max($start, new DateTimeImmutable($b['check_in']));
            $bOut = min($end, new DateTimeImmutable($b['check_out']));
            for ($d = $bIn; $d < $bOut; $d = $d->modify('+1 day')) {
                $day = (int) $d->format('j');
                $occupancy[$roomId][$day] = $b;
            }
        }

        $prev = $start->modify('-1 month')->format('Y-m');
        $next = $start->modify('+1 month')->format('Y-m');
        $title = 'Calendar — ' . $start->format('F Y');
        view('calendar/index', compact('title', 'rooms', 'occupancy', 'days', 'start', 'prev', 'next', 'monthInput'));
    }
}
