<div class="topbar">
  <h2>Rooms</h2>
  <?php if (Auth::isAdmin()): ?>
    <a href="/rooms/create" class="btn btn-primary">+ Add Room</a>
  <?php endif; ?>
</div>

<div class="card">
<?php if (!$rooms): ?>
  <div class="empty">No rooms yet.</div>
<?php else: ?>
<table>
  <thead><tr><th>Number</th><th>Type</th><th class="right">Rate / Night</th><th>Status</th><th>Description</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rooms as $r): ?>
    <tr>
      <td><strong><?= e($r['number']) ?></strong></td>
      <td><?= e(ucfirst($r['type'])) ?></td>
      <td class="right"><?= money((float) $r['rate']) ?></td>
      <td>
        <?php $cls = ['available'=>'badge-ok','occupied'=>'badge-info','maintenance'=>'badge-warn'][$r['status']] ?? 'badge-muted'; ?>
        <span class="badge <?= $cls ?>"><?= e($r['status']) ?></span>
      </td>
      <td class="muted"><?= e($r['description']) ?></td>
      <td class="right">
        <?php if (Auth::isAdmin()): ?>
          <div class="actions" style="justify-content:flex-end">
            <a class="btn btn-sm" href="/rooms/<?= (int) $r['id'] ?>/edit">Edit</a>
            <form method="post" action="/rooms/<?= (int) $r['id'] ?>/delete" onsubmit="return confirm('Delete this room?')">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </div>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>
