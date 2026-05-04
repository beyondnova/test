<div class="topbar">
  <h2>Staff &amp; Users</h2>
  <a class="btn btn-primary" href="/staff/create">+ Add User</a>
</div>

<div class="card">
<?php if (!$staff): ?>
  <div class="empty">No users.</div>
<?php else: ?>
<table>
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($staff as $s): ?>
    <tr>
      <td><strong><?= e($s['name']) ?></strong></td>
      <td><?= e($s['email']) ?></td>
      <td><span class="badge <?= $s['role']==='admin'?'badge-info':'badge-muted' ?>"><?= e($s['role']) ?></span></td>
      <td><?= $s['active'] ? '<span class="badge badge-ok">active</span>' : '<span class="badge badge-bad">inactive</span>' ?></td>
      <td class="muted"><?= e(substr($s['created_at'], 0, 10)) ?></td>
      <td class="right">
        <div class="actions" style="justify-content:flex-end">
          <a class="btn btn-sm" href="/staff/<?= (int) $s['id'] ?>/edit">Edit</a>
          <?php if ((int) $s['id'] !== (int) ($_SESSION['uid'] ?? 0) && $s['active']): ?>
            <form method="post" action="/staff/<?= (int) $s['id'] ?>/delete" onsubmit="return confirm('Deactivate this user?')">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-danger">Deactivate</button>
            </form>
          <?php endif; ?>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>
