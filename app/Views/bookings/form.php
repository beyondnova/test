<div class="topbar"><h2>New Booking</h2></div>
<div class="card" style="max-width:760px">
<form method="post" action="/bookings">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="row">
      <label>Client *</label>
      <select name="client_id" required>
        <option value="">— select client —</option>
        <?php foreach ($clients as $c): ?>
          <option value="<?= (int) $c['id'] ?>" <?= $booking['client_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="muted" style="margin-top:4px"><a href="/clients/create">+ create new client</a></div>
    </div>
    <div class="row">
      <label>Room *</label>
      <select name="room_id" required>
        <option value="">— select room —</option>
        <?php foreach ($rooms as $r): ?>
          <option value="<?= (int) $r['id'] ?>"><?= e($r['number']) ?> — <?= e(ucfirst($r['type'])) ?> (<?= money((float) $r['rate']) ?>/night)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <label>Check-in *</label>
      <input type="date" name="check_in" value="<?= e($booking['check_in']) ?>" required>
    </div>
    <div class="row">
      <label>Check-out *</label>
      <input type="date" name="check_out" value="<?= e($booking['check_out']) ?>" required>
    </div>
  </div>
  <div class="row">
    <label>Notes</label>
    <textarea name="notes" rows="3"><?= e($booking['notes']) ?></textarea>
  </div>
  <div class="muted" style="margin-bottom:14px">An invoice with 10% tax will be created automatically.</div>
  <button class="btn btn-primary">Create Booking</button>
  <a href="/bookings" class="btn">Cancel</a>
</form>
</div>
