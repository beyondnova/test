<?php

class ExportController
{
    public function bookings(): void
    {
        Auth::require();
        $rows = Db::all(
            "SELECT b.id, c.name AS client, c.email, c.phone, r.number AS room, r.type,
                    b.check_in, b.check_out, b.nights, b.rate, b.status,
                    i.total, i.paid_amount, i.status AS invoice_status
             FROM bookings b
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             LEFT JOIN invoices i ON i.booking_id = b.id
             ORDER BY b.id DESC"
        );
        $this->stream('bookings-' . date('Y-m-d') . '.csv', [
            'ID','Client','Email','Phone','Room','Type','Check-in','Check-out','Nights','Rate','Status','Total','Paid','Invoice Status'
        ], $rows);
    }

    public function invoices(): void
    {
        Auth::require();
        $rows = Db::all(
            "SELECT i.id, c.name AS client, r.number AS room, b.check_in, b.check_out,
                    i.room_charge, i.extra_charges, i.discount, i.tax, i.total, i.paid_amount, i.status, i.created_at
             FROM invoices i
             JOIN bookings b ON b.id = i.booking_id
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             ORDER BY i.id DESC"
        );
        $this->stream('invoices-' . date('Y-m-d') . '.csv', [
            'Invoice','Client','Room','Check-in','Check-out','Room Charge','Extras','Discount','Tax','Total','Paid','Status','Created'
        ], $rows);
    }

    public function payments(): void
    {
        Auth::requireAdmin();
        $rows = Db::all(
            'SELECT pm.id, pm.invoice_id, c.name AS client, pm.amount, pm.method, pm.reference, pm.paid_at, u.name AS recorded_by
             FROM payments pm
             JOIN invoices i ON i.id = pm.invoice_id
             JOIN bookings b ON b.id = i.booking_id
             JOIN clients c ON c.id = b.client_id
             LEFT JOIN users u ON u.id = pm.recorded_by
             ORDER BY pm.id DESC'
        );
        $this->stream('payments-' . date('Y-m-d') . '.csv', [
            'Payment','Invoice','Client','Amount','Method','Reference','Paid At','Recorded By'
        ], $rows);
    }

    private function stream(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        foreach ($rows as $row) {
            fputcsv($out, array_values($row));
        }
        fclose($out);
        exit;
    }
}
