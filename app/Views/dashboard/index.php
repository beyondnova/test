<div class="topbar">
  <h2>Dashboard</h2>
  <div>
    <a href="/bookings/create" class="btn btn-primary">+ New Booking</a>
  </div>
</div>

<div class="grid grid-4">
  <div class="stat">
    <div class="label">Rooms Available</div>
    <div class="value"><?= $stats['rooms_available'] ?> / <?= $stats['rooms_total'] ?></div>
    <div class="sub"><?= $stats['rooms_occupied'] ?> occupied &middot; <?= $stats['rooms_maint'] ?> maintenance</div>
  </div>
  <div class="stat">
    <div class="label">Active Bookings</div>
    <div class="value"><?= $stats['bookings_active'] ?></div>
    <div class="sub"><?= $stats['bookings_today'] ?> checking in today</div>
  </div>
  <div class="stat">
    <div class="label">Total Revenue</div>
    <div class="value"><?= money($stats['revenue_total']) ?></div>
    <div class="sub">All payments collected</div>
  </div>
  <div class="stat">
    <div class="label">Outstanding</div>
    <div class="value" style="color:#dc2626"><?= money($stats['outstanding']) ?></div>
    <div class="sub"><?= $stats['invoices_unpaid'] ?> unpaid invoice(s)</div>
  </div>
</div>

<div class="grid grid-2" style="margin-top:18px">
  <div class="card">
    <h3>Recent Bookings</h3>
    <?php if (!$recentBookings): ?>
      <div class="empty">No bookings yet.</div>
    <?php else: ?>
    <table>
      <thead><tr><th>#</th><th>Client</th><th>Room</th><th>Dates</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($recentBookings as $b): ?>
        <tr>
          <td><a href="/bookings/<?= (int) $b['id'] ?>">#<?= (int) $b['id'] ?></a></td>
          <td><?= e($b['client_name']) ?></td>
          <td><?= e($b['room_number']) ?></td>
          <td><?= e($b['check_in']) ?> &rarr; <?= e($b['check_out']) ?></td>
          <td>
            <?php
              $b_status_classes = ['booked'=>'badge-info','checked_in'=>'badge-ok','checked_out'=>'badge-muted','cancelled'=>'badge-bad'];
              $cls = $b_status_classes[$b['status']] ?? 'badge-muted';
            ?>
            <span class="badge <?= $cls ?>"><?= e(str_replace('_',' ',$b['status'])) ?></span>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3>Unpaid Invoices</h3>
    <?php if (!$unpaidInvoices): ?>
      <div class="empty">All invoices settled. Nice!</div>
    <?php else: ?>
    <table>
      <thead><tr><th>Inv #</th><th>Client</th><th>Room</th><th class="right">Due</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($unpaidInvoices as $i): ?>
        <tr>
          <td><a href="/invoices/<?= (int) $i['id'] ?>">#<?= (int) $i['id'] ?></a></td>
          <td><?= e($i['client_name']) ?></td>
          <td><?= e($i['room_number']) ?></td>
          <td class="right"><?= money($i['total'] - $i['paid_amount']) ?></td>
          <td><span class="badge <?= $i['status']==='partial' ? 'badge-warn' : 'badge-bad' ?>"><?= e($i['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
