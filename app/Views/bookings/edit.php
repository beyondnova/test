<div class="topbar">
  <h2>Edit Booking #<?= (int) $booking['id'] ?></h2>
  <a href="/bookings/<?= (int) $booking['id'] ?>" class="btn">Back</a>
</div>
<div class="card" style="max-width:760px">
<form method="post" action="/bookings/<?= (int) $booking['id'] ?>/update">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="row">
      <label>Client *</label>
      <select name="client_id" required>
        <?php foreach ($clients as $c): ?>
          <option value="<?= (int) $c['id'] ?>" <?= (int) $booking['client_id']===(int) $c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <label>Room *</label>
      <select name="room_id" required>
        <?php foreach ($rooms as $r): ?>
          <option value="<?= (int) $r['id'] ?>" <?= (int) $booking['room_id']===(int) $r['id']?'selected':'' ?>><?= e($r['number']) ?> — <?= e(ucfirst($r['type'])) ?> (<?= money((float) $r['rate']) ?>/night)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row"><label>Check-in *</label><input type="date" name="check_in" value="<?= e($booking['check_in']) ?>" required></div>
    <div class="row"><label>Check-out *</label><input type="date" name="check_out" value="<?= e($booking['check_out']) ?>" required></div>
  </div>
  <div class="row"><label>Notes</label><textarea name="notes" rows="3"><?= e($booking['notes']) ?></textarea></div>
  <div class="muted" style="margin-bottom:14px">Changing dates or room recalculates the invoice room charge automatically.</div>
  <button class="btn btn-primary">Save Changes</button>
  <a href="/bookings/<?= (int) $booking['id'] ?>" class="btn">Cancel</a>
</form>
</div>
