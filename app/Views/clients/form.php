<?php $isEdit = !empty($client['id']); ?>
<div class="topbar"><h2><?= $isEdit ? 'Edit Client' : 'New Client' ?></h2></div>
<div class="card" style="max-width:760px">
<form method="post" action="<?= $isEdit ? '/clients/' . (int) $client['id'] : '/clients' ?>">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="row"><label>Full Name *</label><input type="text" name="name" value="<?= e($client['name']) ?>" required></div>
    <div class="row"><label>Email</label><input type="email" name="email" value="<?= e($client['email']) ?>"></div>
    <div class="row"><label>Phone</label><input type="text" name="phone" value="<?= e($client['phone']) ?>"></div>
    <div class="row"><label>ID Number / Passport</label><input type="text" name="id_number" value="<?= e($client['id_number']) ?>"></div>
  </div>
  <div class="row"><label>Address</label><input type="text" name="address" value="<?= e($client['address']) ?>"></div>
  <div class="row"><label>Notes</label><textarea name="notes" rows="3"><?= e($client['notes']) ?></textarea></div>
  <button class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?></button>
  <a href="/clients" class="btn">Cancel</a>
</form>
</div>
