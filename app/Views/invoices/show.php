<?php $balance = (float) $invoice['total'] - (float) $invoice['paid_amount']; ?>
<div class="topbar">
  <h2>Invoice #<?= (int) $invoice['id'] ?></h2>
  <div class="actions">
    <a class="btn" href="/bookings/<?= (int) $invoice['booking_id'] ?>">View Booking</a>
    <button class="btn" onclick="window.print()">Print</button>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Bill To</h3>
    <table>
      <tr><th style="width:120px">Client</th><td><a href="/clients/<?= (int) $invoice['client_id'] ?>"><strong><?= e($invoice['client_name']) ?></strong></a></td></tr>
      <tr><th>Email</th><td><?= e($invoice['client_email']) ?></td></tr>
      <tr><th>Phone</th><td><?= e($invoice['client_phone']) ?></td></tr>
      <tr><th>Address</th><td><?= e($invoice['client_address']) ?></td></tr>
    </table>
    <h3 style="margin-top:18px">Stay</h3>
    <table>
      <tr><th style="width:120px">Room</th><td><?= e($invoice['room_number']) ?> — <?= e(ucfirst($invoice['room_type'])) ?></td></tr>
      <tr><th>Check-in</th><td><?= e($invoice['check_in']) ?></td></tr>
      <tr><th>Check-out</th><td><?= e($invoice['check_out']) ?></td></tr>
      <tr><th>Nights</th><td><?= (int) $invoice['nights'] ?></td></tr>
      <tr><th>Issued</th><td class="muted"><?= e($invoice['created_at']) ?></td></tr>
    </table>
  </div>

  <div class="card">
    <h3>Charges</h3>
    <table>
      <tr><th style="width:160px">Room charges</th><td class="right"><?= money((float) $invoice['room_charge']) ?></td></tr>
      <tr><th>Extra charges</th><td class="right"><?= money((float) $invoice['extra_charges']) ?></td></tr>
      <tr><th>Discount</th><td class="right" style="color:#16a34a">-<?= money((float) $invoice['discount']) ?></td></tr>
      <tr><th>Tax</th><td class="right"><?= money((float) $invoice['tax']) ?></td></tr>
      <tr><th><strong>Total</strong></th><td class="right"><strong style="font-size:18px"><?= money((float) $invoice['total']) ?></strong></td></tr>
      <tr><th>Paid</th><td class="right" style="color:#16a34a"><?= money((float) $invoice['paid_amount']) ?></td></tr>
      <tr><th><strong>Balance</strong></th><td class="right"><strong style="font-size:18px;color:<?= $balance>0?'#dc2626':'#16a34a' ?>"><?= money($balance) ?></strong></td></tr>
      <tr><th>Status</th><td class="right">
        <?php $cls = $invoice['status']==='paid'?'badge-ok':($invoice['status']==='partial'?'badge-warn':($invoice['status']==='cancelled'?'badge-muted':'badge-bad')); ?>
        <span class="badge <?= $cls ?>"><?= e($invoice['status']) ?></span>
      </td></tr>
    </table>
  </div>
</div>

<?php if ($invoice['status'] !== 'cancelled'): ?>
<div class="grid grid-2" style="margin-top:18px">
  <div class="card">
    <h3>Adjust Charges</h3>
    <form method="post" action="/invoices/<?= (int) $invoice['id'] ?>/update">
      <?= csrf_field() ?>
      <div class="grid grid-2">
        <div class="row"><label>Extra charges ($)</label><input type="number" step="0.01" min="0" name="extra_charges" value="<?= e((string) $invoice['extra_charges']) ?>"></div>
        <div class="row"><label>Discount ($)</label><input type="number" step="0.01" min="0" name="discount" value="<?= e((string) $invoice['discount']) ?>"></div>
        <div class="row"><label>Tax rate (%)</label><input type="number" step="0.01" min="0" max="100" name="tax_rate" value="<?= e((string) round((float) $invoice['tax'] / max(0.01, (float) $invoice['room_charge'] + (float) $invoice['extra_charges'] - (float) $invoice['discount']) * 100, 2)) ?>"></div>
      </div>
      <button class="btn btn-primary">Recalculate</button>
    </form>
  </div>

  <?php if ($balance > 0): ?>
  <div class="card">
    <h3>Record Payment</h3>
    <form method="post" action="/invoices/<?= (int) $invoice['id'] ?>/pay">
      <?= csrf_field() ?>
      <div class="grid grid-2">
        <div class="row"><label>Amount ($)</label><input type="number" step="0.01" min="0.01" max="<?= e((string) $balance) ?>" name="amount" value="<?= e((string) $balance) ?>" required></div>
        <div class="row">
          <label>Method</label>
          <select name="method">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="transfer">Bank Transfer</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>
      <div class="row"><label>Reference (optional)</label><input type="text" name="reference"></div>
      <button class="btn btn-success">Record Payment</button>
    </form>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="card" style="margin-top:18px">
  <h3>Payment History</h3>
  <?php if (!$payments): ?>
    <div class="empty">No payments recorded.</div>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Date</th><th class="right">Amount</th><th>Method</th><th>Reference</th><th>Recorded By</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($payments as $pm): ?>
      <tr>
        <td>#<?= (int) $pm['id'] ?></td>
        <td><?= e($pm['paid_at']) ?></td>
        <td class="right"><strong><?= money((float) $pm['amount']) ?></strong></td>
        <td><?= e(ucfirst($pm['method'])) ?></td>
        <td class="muted"><?= e($pm['reference']) ?></td>
        <td class="muted"><?= e($pm['user_name'] ?? '—') ?></td>
        <td class="right">
          <?php if (Auth::isAdmin()): ?>
            <form method="post" action="/invoices/<?= (int) $invoice['id'] ?>/payments/<?= (int) $pm['id'] ?>/delete" onsubmit="return confirm('Delete this payment?')">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
