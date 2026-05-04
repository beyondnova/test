<?php

class InvoiceController
{
    public function index(): void
    {
        Auth::require();
        $status = (string) input('status', '');
        $where = '';
        $params = [];
        if ($status !== '') {
            $where = ' WHERE i.status = ?';
            $params[] = $status;
        }
        $invoices = Db::all(
            "SELECT i.*, c.name AS client_name, r.number AS room_number, b.id AS booking_id
             FROM invoices i
             JOIN bookings b ON b.id = i.booking_id
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             $where
             ORDER BY i.id DESC",
            $params
        );
        $title = 'Invoices';
        view('invoices/index', compact('title', 'invoices', 'status'));
    }

    public function show(array $p): void
    {
        Auth::require();
        $invoice = Db::one(
            'SELECT i.*, b.check_in, b.check_out, b.nights, b.client_id, c.name AS client_name,
                    c.email AS client_email, c.phone AS client_phone, c.address AS client_address,
                    r.number AS room_number, r.type AS room_type
             FROM invoices i
             JOIN bookings b ON b.id = i.booking_id
             JOIN clients c ON c.id = b.client_id
             JOIN rooms r ON r.id = b.room_id
             WHERE i.id = ?',
            [(int) $p['id']]
        );
        if (!$invoice) { http_response_code(404); echo 'Not found'; return; }
        $items = Db::all('SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id', [(int) $p['id']]);
        $payments = Db::all(
            'SELECT pm.*, u.name AS user_name FROM payments pm
             LEFT JOIN users u ON u.id = pm.recorded_by
             WHERE pm.invoice_id = ? ORDER BY pm.id DESC',
            [(int) $p['id']]
        );
        $taxRate = $this->currentTaxRate($invoice);
        $title = 'Invoice #' . $invoice['id'];
        view('invoices/show', compact('title', 'invoice', 'items', 'payments', 'taxRate'));
    }

    public function addItem(array $p): void
    {
        Auth::require();
        csrf_check();
        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [(int) $p['id']]);
        if (!$invoice) { flash('error', 'Not found.'); redirect('/invoices'); }
        if ($invoice['status'] === 'cancelled') { flash('error', 'Invoice is cancelled.'); back(); }

        $description = trim((string) input('description'));
        $qty = (float) input('quantity', 1);
        $price = (float) input('unit_price', 0);
        if ($description === '' || $qty <= 0 || $price < 0) {
            flash('error', 'Please provide a description, quantity, and price.');
            back();
        }
        $amount = round($qty * $price, 2);
        Db::q(
            'INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)',
            [$invoice['id'], $description, $qty, $price, $amount]
        );
        $this->recompute((int) $invoice['id']);
        flash('success', 'Line item added.');
        redirect('/invoices/' . (int) $invoice['id']);
    }

    public function deleteItem(array $p): void
    {
        Auth::require();
        csrf_check();
        $item = Db::one('SELECT * FROM invoice_items WHERE id = ? AND invoice_id = ?', [(int) $p['item_id'], (int) $p['id']]);
        if (!$item) { flash('error', 'Item not found.'); back(); }
        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [(int) $p['id']]);
        if ($invoice['status'] === 'cancelled') { flash('error', 'Invoice is cancelled.'); back(); }
        Db::q('DELETE FROM invoice_items WHERE id = ?', [$item['id']]);
        $this->recompute((int) $invoice['id']);
        flash('success', 'Line item removed.');
        back();
    }

    public function update(array $p): void
    {
        Auth::require();
        csrf_check();
        $discount = max(0, (float) input('discount', 0));
        $taxRate = max(0, min(100, (float) input('tax_rate', 10)));

        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [(int) $p['id']]);
        if (!$invoice) { flash('error', 'Not found.'); redirect('/invoices'); }
        if ($invoice['status'] === 'cancelled') {
            flash('error', 'Cannot edit a cancelled invoice.');
            back();
        }

        Db::q('UPDATE invoices SET discount=? WHERE id=?', [$discount, (int) $p['id']]);
        $this->recompute((int) $p['id'], $taxRate);
        flash('success', 'Invoice updated.');
        redirect('/invoices/' . (int) $p['id']);
    }

    private function recompute(int $invoiceId, ?float $taxRate = null): void
    {
        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [$invoiceId]);
        $extras = (float) (Db::scalar('SELECT COALESCE(SUM(amount), 0) FROM invoice_items WHERE invoice_id = ?', [$invoiceId]) ?? 0);
        if ($taxRate === null) {
            $taxRate = $this->currentTaxRate($invoice);
        }
        $base = max(0, (float) $invoice['room_charge'] + $extras - (float) $invoice['discount']);
        $tax = round($base * ($taxRate / 100), 2);
        $total = round($base + $tax, 2);
        $paid = (float) $invoice['paid_amount'];
        $status = $paid <= 0 ? 'pending' : ($paid >= $total - 0.001 ? 'paid' : 'partial');
        Db::q(
            'UPDATE invoices SET extra_charges=?, tax=?, total=?, status=? WHERE id=?',
            [$extras, $tax, $total, $status, $invoiceId]
        );
    }

    private function currentTaxRate(array $invoice): float
    {
        $base = (float) $invoice['room_charge'] + (float) $invoice['extra_charges'] - (float) $invoice['discount'];
        if ($base <= 0) return 10.0;
        return round((float) $invoice['tax'] / $base * 100, 2);
    }

    public function pay(array $p): void
    {
        Auth::require();
        csrf_check();
        $amount = (float) input('amount', 0);
        $method = (string) input('method', 'cash');
        $reference = trim((string) input('reference', ''));

        if ($amount <= 0) { flash('error', 'Payment amount must be greater than zero.'); back(); }
        if (!in_array($method, ['cash','card','transfer','other'], true)) { flash('error', 'Invalid method.'); back(); }

        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [(int) $p['id']]);
        if (!$invoice) { flash('error', 'Not found.'); redirect('/invoices'); }
        if ($invoice['status'] === 'cancelled') { flash('error', 'Invoice is cancelled.'); back(); }

        $balance = (float) $invoice['total'] - (float) $invoice['paid_amount'];
        if ($amount > $balance + 0.001) {
            flash('error', 'Amount exceeds remaining balance (' . money($balance) . ').');
            back();
        }

        Db::q(
            'INSERT INTO payments (invoice_id, amount, method, reference, recorded_by) VALUES (?, ?, ?, ?, ?)',
            [(int) $p['id'], $amount, $method, $reference ?: null, $_SESSION['uid'] ?? null]
        );
        $newPaid = (float) $invoice['paid_amount'] + $amount;
        $status = $newPaid >= (float) $invoice['total'] - 0.001 ? 'paid' : 'partial';
        Db::q('UPDATE invoices SET paid_amount=?, status=? WHERE id=?', [$newPaid, $status, (int) $p['id']]);

        flash('success', 'Payment recorded: ' . money($amount));
        redirect('/invoices/' . (int) $p['id']);
    }

    public function deletePayment(array $p): void
    {
        Auth::requireAdmin();
        csrf_check();
        $payment = Db::one('SELECT * FROM payments WHERE id = ?', [(int) $p['payment_id']]);
        if (!$payment) { flash('error', 'Payment not found.'); back(); }
        $invoice = Db::one('SELECT * FROM invoices WHERE id = ?', [$payment['invoice_id']]);
        Db::q('DELETE FROM payments WHERE id = ?', [(int) $p['payment_id']]);
        $newPaid = max(0, (float) $invoice['paid_amount'] - (float) $payment['amount']);
        $status = $newPaid <= 0 ? 'pending' : ($newPaid >= (float) $invoice['total'] - 0.001 ? 'paid' : 'partial');
        if ($invoice['status'] === 'cancelled') $status = 'cancelled';
        Db::q('UPDATE invoices SET paid_amount=?, status=? WHERE id=?', [$newPaid, $status, $invoice['id']]);
        flash('success', 'Payment removed.');
        back();
    }
}
