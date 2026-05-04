<div class="topbar">
  <h2>Bookings</h2>
  <a href="/bookings/create" class="btn btn-primary">+ New Booking</a>
</div>

<div class="card">
  <form method="get" action="/bookings" style="margin-bottom:14px">
    <label style="display:inline-block;margin-right:8px">Filter by status</label>
    <select name="status" onchange="this.form.submit()" style="width:auto;display:inline-block">
      <option value="">All</option>
      <?php foreach (['booked','checked_in','checked_out','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

<?php if (!$bookings): ?>
  <div class="empty">No bookings found.</div>
<?php else: ?>
<table>
  <thead><tr><th>#</th><th>Client</th><th>Room</th><th>Check-in</th><th>Check-out</th><th class="right">Nights</th><th>Status</th><th>Invoice</th></tr></thead>
  <tbody>
  <?php foreach ($bookings as $b): ?>
    <tr>
      <td><a href="/bookings/<?= (int) $b['id'] ?>"><strong>#<?= (int) $b['id'] ?></strong></a></td>
      <td><a href="/clients/<?= (int) $b['client_id'] ?>"><?= e($b['client_name']) ?></a></td>
      <td><?= e($b['room_number']) ?></td>
      <td><?= e($b['check_in']) ?></td>
      <td><?= e($b['check_out']) ?></td>
      <td class="right"><?= (int) $b['nights'] ?></td>
      <td>
        <?php $cls = ['booked'=>'badge-info','checked_in'=>'badge-ok','checked_out'=>'badge-muted','cancelled'=>'badge-bad'][$b['status']] ?? 'badge-muted'; ?>
        <span class="badge <?= $cls ?>"><?= e(str_replace('_',' ',$b['status'])) ?></span>
      </td>
      <td>
        <?php if ($b['invoice_id']): ?>
          <a href="/invoices/<?= (int) $b['invoice_id'] ?>"><?= money((float) $b['inv_total']) ?></a>
          <?php $cls = $b['inv_status']==='paid'?'badge-ok':($b['inv_status']==='partial'?'badge-warn':($b['inv_status']==='cancelled'?'badge-muted':'badge-bad')); ?>
          <span class="badge <?= $cls ?>"><?= e($b['inv_status']) ?></span>
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
