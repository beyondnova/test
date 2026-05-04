<div class="topbar"><h2>My Profile</h2></div>

<div class="grid grid-2">
  <div class="card">
    <h3>Account Details</h3>
    <form method="post" action="/profile">
      <?= csrf_field() ?>
      <div class="row"><label>Full Name</label><input type="text" name="name" value="<?= e($user['name']) ?>" required></div>
      <div class="row"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required></div>
      <div class="row">
        <label>Role</label>
        <input type="text" value="<?= e(ucfirst($user['role'])) ?>" disabled>
      </div>
      <div class="muted" style="margin-bottom:14px">Member since <?= e($user['created_at']) ?></div>
      <button class="btn btn-primary">Save</button>
    </form>
  </div>

  <div class="card">
    <h3>Change Password</h3>
    <form method="post" action="/profile/password">
      <?= csrf_field() ?>
      <div class="row"><label>Current password</label><input type="password" name="current_password" required></div>
      <div class="row"><label>New password</label><input type="password" name="new_password" required minlength="6"></div>
      <div class="row"><label>Confirm new password</label><input type="password" name="confirm_password" required minlength="6"></div>
      <button class="btn btn-primary">Update Password</button>
    </form>
  </div>
</div>
