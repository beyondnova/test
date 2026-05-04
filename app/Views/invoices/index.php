<div class="topbar"><h2>Invoices</h2></div>

<div class="card">
  <form method="get" action="/invoices" style="margin-bottom:14px">
    <label style="display:inline-block;margin-right:8px">Filter</label>
    <select name="status" onchange="this.form.submit()" style="width:auto;display:inline-block">
      <option value="">All</option>
      <?php foreach (['pending','partial','paid','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

<?php if (!$invoices): ?>
  <div class="empty">No invoices yet.</div>
<?php else: ?>
<table>
  <thead><tr><th>Inv #</th><th>Client</th><th>Room</th><th class="right">Total</th><th class="right">Paid</th><th class="right">Balance</th><th>Status</th><th>Date</th></tr></thead>
  <tbody>
  <?php foreach ($invoices as $i): ?>
    <tr>
      <td><a href="/invoices/<?= (int) $i['id'] ?>"><strong>#<?= (int) $i['id'] ?></strong></a></td>
      <td><?= e($i['client_name']) ?></td>
      <td><?= e($i['room_number']) ?></td>
      <td class="right"><?= money((float) $i['total']) ?></td>
      <td class="right"><?= money((float) $i['paid_amount']) ?></td>
      <td class="right"><strong><?= money((float) $i['total'] - (float) $i['paid_amount']) ?></strong></td>
      <td>
        <?php $cls = $i['status']==='paid'?'badge-ok':($i['status']==='partial'?'badge-warn':($i['status']==='cancelled'?'badge-muted':'badge-bad')); ?>
        <span class="badge <?= $cls ?>"><?= e($i['status']) ?></span>
      </td>
      <td class="muted"><?= e(substr($i['created_at'], 0, 10)) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>
