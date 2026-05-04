<div class="topbar">
  <h2>Clients</h2>
  <a href="/clients/create" class="btn btn-primary">+ Add Client</a>
</div>

<div class="card">
  <form method="get" action="/clients" style="margin-bottom:14px">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name, email, phone, ID..." style="max-width:380px;display:inline-block">
    <button class="btn">Search</button>
    <?php if ($q !== ''): ?><a class="btn" href="/clients">Clear</a><?php endif; ?>
  </form>

<?php if (!$clients): ?>
  <div class="empty">No clients found.</div>
<?php else: ?>
<table>
  <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>ID #</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($clients as $c): ?>
    <tr>
      <td><a href="/clients/<?= (int) $c['id'] ?>"><strong><?= e($c['name']) ?></strong></a></td>
      <td><?= e($c['email']) ?></td>
      <td><?= e($c['phone']) ?></td>
      <td class="muted"><?= e($c['id_number']) ?></td>
      <td class="right">
        <div class="actions" style="justify-content:flex-end">
          <a class="btn btn-sm" href="/clients/<?= (int) $c['id'] ?>">View</a>
          <a class="btn btn-sm" href="/clients/<?= (int) $c['id'] ?>/edit">Edit</a>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>
