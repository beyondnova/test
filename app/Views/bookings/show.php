<div class="topbar">
  <h2>Booking #<?= (int) $booking['id'] ?></h2>
  <div class="actions">
    <?php if ($booking['status'] === 'booked'): ?>
      <form method="post" action="/bookings/<?= (int) $booking['id'] ?>/check-in" style="display:inline">
        <?= csrf_field() ?><button class="btn btn-success">Check In</button>
      </form>
    <?php endif; ?>
    <?php if ($booking['status'] === 'checked_in'): ?>
      <form method="post" action="/bookings/<?= (int) $booking['id'] ?>/check-out" style="display:inline">
        <?= csrf_field() ?><button class="btn btn-success">Check Out</button>
      </form>
    <?php endif; ?>
    <?php if (!in_array($booking['status'], ['checked_out','cancelled'], true)): ?>
      <form method="post" action="/bookings/<?= (int) $booking['id'] ?>/cancel" style="display:inline" onsubmit="return confirm('Cancel this booking?')">
        <?= csrf_field() ?><button class="btn btn-danger">Cancel</button>
      </form>
    <?php endif; ?>
    <?php if ($invoice): ?>
      <a class="btn btn-primary" href="/invoices/<?= (int) $invoice['id'] ?>">Open Invoice</a>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Booking Details</h3>
    <table>
      <tr><th style="width:160px">Status</th><td>
        <?php $cls = ['booked'=>'badge-info','checked_in'=>'badge-ok','checked_out'=>'badge-muted','cancelled'=>'badge-bad'][$booking['status']] ?? 'badge-muted'; ?>
        <span class="badge <?= $cls ?>"><?= e(str_replace('_',' ',$booking['status'])) ?></span>
      </td></tr>
      <tr><th>Room</th><td><strong><?= e($booking['room_number']) ?></strong> — <?= e(ucfirst($booking['room_type'])) ?></td></tr>
      <tr><th>Check-in</th><td><?= e($booking['check_in']) ?></td></tr>
      <tr><th>Check-out</th><td><?= e($booking['check_out']) ?></td></tr>
      <tr><th>Nights</th><td><?= (int) $booking['nights'] ?></td></tr>
      <tr><th>Rate</th><td><?= money((float) $booking['rate']) ?> / night</td></tr>
      <tr><th>Notes</th><td class="muted"><?= e($booking['notes']) ?></td></tr>
      <tr><th>Created</th><td class="muted"><?= e($booking['created_at']) ?> by <?= e($booking['created_by_name'] ?? '—') ?></td></tr>
    </table>
  </div>

  <div class="card">
    <h3>Client</h3>
    <table>
      <tr><th style="width:120px">Name</th><td><a href="/clients/<?= (int) $booking['client_id'] ?>"><strong><?= e($booking['client_name']) ?></strong></a></td></tr>
      <tr><th>Email</th><td><?= e($booking['client_email']) ?></td></tr>
      <tr><th>Phone</th><td><?= e($booking['client_phone']) ?></td></tr>
    </table>
    <?php if ($invoice): ?>
      <h3 style="margin-top:18px">Billing Summary</h3>
      <table>
        <tr><th style="width:140px">Total</th><td><strong><?= money((float) $invoice['total']) ?></strong></td></tr>
        <tr><th>Paid</th><td><?= money((float) $invoice['paid_amount']) ?></td></tr>
        <tr><th>Balance</th><td><strong><?= money((float) $invoice['total'] - (float) $invoice['paid_amount']) ?></strong></td></tr>
        <tr><th>Status</th><td>
          <?php $cls = $invoice['status']==='paid'?'badge-ok':($invoice['status']==='partial'?'badge-warn':($invoice['status']==='cancelled'?'badge-muted':'badge-bad')); ?>
          <span class="badge <?= $cls ?>"><?= e($invoice['status']) ?></span>
        </td></tr>
      </table>
    <?php endif; ?>
  </div>
</div>
