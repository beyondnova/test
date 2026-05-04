<div class="topbar">
  <h2><?= e($client['name']) ?></h2>
  <div class="actions">
    <a class="btn" href="/clients/<?= (int) $client['id'] ?>/edit">Edit</a>
    <a class="btn btn-primary" href="/bookings/create?client_id=<?= (int) $client['id'] ?>">+ New Booking</a>
    <?php if (Auth::isAdmin()): ?>
      <form method="post" action="/clients/<?= (int) $client['id'] ?>/delete" onsubmit="return confirm('Delete this client?')" style="display:inline">
        <?= csrf_field() ?>
        <button class="btn btn-danger">Delete</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid-3">
  <div class="stat"><div class="label">Bookings</div><div class="value"><?= (int) $totals['bookings'] ?></div></div>
  <div class="stat"><div class="label">Total Spent</div><div class="value"><?= money($totals['spent']) ?></div></div>
  <div class="stat"><div class="label">Outstanding</div><div class="value" style="color:#dc2626"><?= money($totals['owing']) ?></div></div>
</div>

<div class="grid grid-2" style="margin-top:18px">
  <div class="card">
    <h3>Profile</h3>
    <table>
      <tr><th style="width:130px">Email</th><td><?= e($client['email']) ?></td></tr>
      <tr><th>Phone</th><td><?= e($client['phone']) ?></td></tr>
      <tr><th>ID #</th><td><?= e($client['id_number']) ?></td></tr>
      <tr><th>Address</th><td><?= e($client['address']) ?></td></tr>
      <tr><th>Notes</th><td><?= e($client['notes']) ?></td></tr>
      <tr><th>Member since</th><td class="muted"><?= e($client['created_at']) ?></td></tr>
    </table>
  </div>

  <div class="card">
    <h3>Booking History</h3>
    <?php if (!$bookings): ?><div class="empty">No bookings yet.</div><?php else: ?>
    <table>
      <thead><tr><th>#</th><th>Room</th><th>Dates</th><th>Status</th><th class="right">Invoice</th></tr></thead>
      <tbody>
      <?php foreach ($bookings as $b): ?>
        <tr>
          <td><a href="/bookings/<?= (int) $b['id'] ?>">#<?= (int) $b['id'] ?></a></td>
          <td><?= e($b['room_number']) ?></td>
          <td><?= e($b['check_in']) ?> &rarr; <?= e($b['check_out']) ?></td>
          <td><span class="badge badge-muted"><?= e(str_replace('_',' ',$b['status'])) ?></span></td>
          <td class="right">
            <?php if ($b['invoice_id']): ?>
              <a href="/invoices/<?= (int) $b['invoice_id'] ?>"><?= money((float) $b['total']) ?></a>
              <?php if ($b['inv_status'] !== 'paid'): ?>
                <span class="badge badge-warn"><?= e($b['inv_status']) ?></span>
              <?php else: ?>
                <span class="badge badge-ok">paid</span>
              <?php endif; ?>
            <?php else: ?>
              <span class="muted">—</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
