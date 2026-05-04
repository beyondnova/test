<div class="topbar">
  <h2>Room Availability — <?= e($start->format('F Y')) ?></h2>
  <div class="actions">
    <a class="btn" href="/calendar?month=<?= e($prev) ?>">&larr; Prev</a>
    <form method="get" action="/calendar" style="display:inline">
      <input type="month" name="month" value="<?= e($monthInput) ?>" onchange="this.form.submit()" style="width:auto;display:inline-block">
    </form>
    <a class="btn" href="/calendar?month=<?= e($next) ?>">Next &rarr;</a>
  </div>
</div>

<div class="card" style="overflow:auto">
<style>
.cal { border-collapse: collapse; width: 100%; font-size: 12px; }
.cal th, .cal td { border:1px solid #e2e8f0; padding:0; text-align:center; height:34px; min-width:28px; }
.cal th { background:#f1f5f9; font-weight:600; }
.cal th.room { text-align:left; padding:6px 10px; min-width:100px; position:sticky; left:0; background:#f1f5f9; z-index:2; }
.cal td.room { text-align:left; padding:6px 10px; font-weight:600; background:#fff; position:sticky; left:0; z-index:1; }
.cal td.weekend { background:#f8fafc; }
.cal td.book { background:#3b82f6; color:#fff; }
.cal td.checked_in { background:#16a34a; color:#fff; }
.cal td.checked_out { background:#94a3b8; color:#fff; }
.cal td.book a, .cal td.checked_in a, .cal td.checked_out a { color:#fff; text-decoration:none; display:block; padding:6px 0; }
.cal td.maint { background:#fef3c7; }
.legend { display:flex; gap:14px; margin-bottom:14px; font-size:12px; flex-wrap:wrap }
.legend span { display:inline-flex; align-items:center; gap:6px }
.legend i { display:inline-block; width:14px; height:14px; border-radius:3px; }
</style>

<div class="legend">
  <span><i style="background:#3b82f6"></i> Booked</span>
  <span><i style="background:#16a34a"></i> Checked-in</span>
  <span><i style="background:#94a3b8"></i> Checked-out</span>
  <span><i style="background:#fef3c7"></i> Maintenance</span>
  <span><i style="background:#fff;border:1px solid #cbd5e1"></i> Available</span>
</div>

<table class="cal">
  <thead>
    <tr>
      <th class="room">Room</th>
      <?php for ($d = 1; $d <= $days; $d++):
        $dt = $start->modify('+' . ($d - 1) . ' day');
        $weekend = in_array((int) $dt->format('w'), [0, 6], true);
      ?>
        <th class="<?= $weekend ? 'weekend' : '' ?>"><?= $d ?><br><span style="font-weight:400;color:#64748b"><?= $dt->format('D')[0] ?></span></th>
      <?php endfor; ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rooms as $r): ?>
    <tr>
      <td class="room"><a href="/rooms"><?= e($r['number']) ?></a><br><span class="muted" style="font-weight:400;font-size:11px"><?= e(ucfirst($r['type'])) ?></span></td>
      <?php for ($d = 1; $d <= $days; $d++):
        $b = $occupancy[$r['id']][$d] ?? null;
        $cls = '';
        if ($b) $cls = $b['status'];
        elseif ($r['status'] === 'maintenance') $cls = 'maint';
        $title = $b ? $b['client_name'] . ' — ' . $b['check_in'] . ' to ' . $b['check_out'] : '';
      ?>
        <td class="<?= e($cls) ?>" title="<?= e($title) ?>">
          <?php if ($b): ?>
            <a href="/bookings/<?= (int) $b['id'] ?>">&middot;</a>
          <?php endif; ?>
        </td>
      <?php endfor; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
