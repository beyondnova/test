<?php $flash = take_flash(); ?>
<div class="login-wrap">
  <div class="login-card">
    <h1>Hotel Manager</h1>
    <div class="hint">Sign in to manage your property.</div>
    <?php foreach ($flash as $t => $m): ?>
      <div class="flash flash-<?= e($t) ?>"><?= e($m) ?></div>
    <?php endforeach; ?>
    <form method="post" action="/login">
      <?= csrf_field() ?>
      <div class="row">
        <label>Email</label>
        <input type="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
      </div>
      <div class="row">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn btn-primary" style="width:100%">Sign in</button>
    </form>
    <div class="seed">
      <strong>Demo accounts</strong><br>
      Admin: admin@hotel.test / admin123<br>
      Staff: staff@hotel.test / staff123
    </div>
  </div>
</div>
