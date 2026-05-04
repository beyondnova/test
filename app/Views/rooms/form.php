<?php $isEdit = !empty($room['id']); ?>
<div class="topbar"><h2><?= $isEdit ? 'Edit Room' : 'New Room' ?></h2></div>
<div class="card" style="max-width:640px">
<form method="post" action="<?= $isEdit ? '/rooms/' . (int) $room['id'] : '/rooms' ?>">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="row">
      <label>Room Number</label>
      <input type="text" name="number" value="<?= e($room['number']) ?>" required>
    </div>
    <div class="row">
      <label>Type</label>
      <select name="type">
        <?php foreach (['single','double','suite','deluxe'] as $t): ?>
          <option value="<?= $t ?>" <?= $room['type']===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <label>Rate per night ($)</label>
      <input type="number" step="0.01" min="0" name="rate" value="<?= e((string) $room['rate']) ?>" required>
    </div>
    <div class="row">
      <label>Status</label>
      <select name="status">
        <?php foreach (['available','occupied','maintenance'] as $s): ?>
          <option value="<?= $s ?>" <?= $room['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="row">
    <label>Description</label>
    <textarea name="description" rows="3"><?= e($room['description']) ?></textarea>
  </div>
  <button class="btn btn-primary"><?= $isEdit ? 'Update Room' : 'Create Room' ?></button>
  <a href="/rooms" class="btn">Cancel</a>
</form>
</div>
