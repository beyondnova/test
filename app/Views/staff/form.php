<?php $isEdit = !empty($member['id']); ?>
<div class="topbar"><h2><?= $isEdit ? 'Edit User' : 'New User' ?></h2></div>
<div class="card" style="max-width:560px">
<form method="post" action="<?= $isEdit ? '/staff/' . (int) $member['id'] : '/staff' ?>">
  <?= csrf_field() ?>
  <div class="row"><label>Full Name *</label><input type="text" name="name" value="<?= e($member['name']) ?>" required></div>
  <div class="row"><label>Email *</label><input type="email" name="email" value="<?= e($member['email']) ?>" required></div>
  <div class="row">
    <label>Password <?= $isEdit ? '(leave blank to keep current)' : '*' ?></label>
    <input type="password" name="password" <?= $isEdit ? '' : 'required' ?>>
  </div>
  <div class="grid grid-2">
    <div class="row">
      <label>Role</label>
      <select name="role">
        <option value="staff" <?= $member['role']==='staff'?'selected':'' ?>>Staff</option>
        <option value="admin" <?= $member['role']==='admin'?'selected':'' ?>>Admin</option>
      </select>
    </div>
    <div class="row">
      <label>Active</label>
      <select name="active">
        <option value="1" <?= $member['active']?'selected':'' ?>>Yes</option>
        <option value="0" <?= !$member['active']?'selected':'' ?>>No</option>
      </select>
    </div>
  </div>
  <button class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?></button>
  <a href="/staff" class="btn">Cancel</a>
</form>
</div>
