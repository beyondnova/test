<div class="topbar"><h2>Reports</h2></div>

<div class="grid grid-2">
  <div class="card">
    <h3>Monthly Revenue</h3>
    <?php if (!$monthly): ?><div class="empty">No payments recorded yet.</div><?php else: ?>
    <table>
      <thead><tr><th>Month</th><th class="right">Payments</th><th class="right">Total</th></tr></thead>
      <tbody>
      <?php foreach ($monthly as $m): ?>
        <tr>
          <td><?= e($m['month']) ?></td>
          <td class="right"><?= (int) $m['count'] ?></td>
          <td class="right"><strong><?= money((float) $m['total']) ?></strong></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3>By Room Type</h3>
    <table>
      <thead><tr><th>Type</th><th class="right">Bookings</th><th class="right">Revenue</th></tr></thead>
      <tbody>
      <?php foreach ($byRoomType as $r): ?>
        <tr>
          <td><?= e(ucfirst($r['type'])) ?></td>
          <td class="right"><?= (int) $r['bookings'] ?></td>
          <td class="right"><?= money((float) $r['revenue']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="margin-top:18px">
  <h3>Top Clients</h3>
  <?php if (!$topClients): ?><div class="empty">No clients yet.</div><?php else: ?>
  <table>
    <thead><tr><th>Client</th><th>Email</th><th class="right">Bookings</th><th class="right">Spent</th></tr></thead>
    <tbody>
    <?php foreach ($topClients as $c): ?>
      <tr>
        <td><?= e($c['name']) ?></td>
        <td><?= e($c['email']) ?></td>
        <td class="right"><?= (int) $c['bookings'] ?></td>
        <td class="right"><strong><?= money((float) $c['spent']) ?></strong></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
