<?php
// profile.php - User profile page

require_once 'config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login_signup');
    exit();
}

$db = new Database();
$pdo = $db->connect();

$userId = (int)$_SESSION['user_id'];

$profileError  = '';
$profileSuccess = '';
$passwordError  = '';
$passwordSuccess = '';

// Lấy thông tin user hiện tại
function getCurrentUser($pdo, $id) {
    $stmt = $pdo->prepare("SELECT id, email, fullname, password_hash FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if ($user) {
        $stmtAdmin = $pdo->prepare("SELECT 1 FROM admins WHERE user_id = ?");
        $stmtAdmin->execute([$id]);
        $user['is_admin'] = $stmtAdmin->fetchColumn() ? 1 : 0;
        $user['username'] = $user['email']; // Tương thích biến cũ
        $user['full_name'] = $user['fullname']; // Tương thích biến cũ
        $user['password'] = $user['password_hash']; // Tương thích biến cũ
    }
    return $user;
}

// Xử lý submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cập nhật thông tin profile
    if (isset($_POST['update_profile'])) {
        $username  = trim($_POST['username'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');

        if ($username === '') {
            $profileError = 'Username cannot be empty.';
        } else {
            // Kiểm tra trùng email (username đóng vai trò là email trong DB này)
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
            $stmt->execute([$username, $userId]);
            if ($stmt->fetch()) {
                $profileError = 'This username/email is already taken.';
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email = ?, fullname = ? WHERE id = ?");
                $stmt->execute([$username, $full_name === '' ? null : $full_name, $userId]);

                $_SESSION['username'] = $username;
                $profileSuccess = 'Profile updated successfully.';
            }
        }
    }

    // Đổi mật khẩu
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword === '' || $confirmPassword === '') {
            $passwordError = 'New password and confirmation are required.';
        } elseif ($newPassword !== $confirmPassword) {
            $passwordError = 'New password and confirmation do not match.';
        } else {
            $userRow = getCurrentUser($pdo, $userId);
            if (!$userRow) {
                $passwordError = 'User not found.';
            } else {
                $storedHash = $userRow['password'];

                // Hỗ trợ cả hash bcrypt lẫn plain text cũ:
                $validCurrent = false;
                if (str_starts_with($storedHash, '$2y$')) {
                    // Hash bcrypt
                    $validCurrent = password_verify($currentPassword, $storedHash);
                } else {
                    // Plain text (không khuyến khích, nhưng để hỗ trợ dữ liệu cũ)
                    $validCurrent = ($currentPassword === $storedHash);
                }

                if (!$validCurrent) {
                    $passwordError = 'Current password is incorrect.';
                } else {
                    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$newHash, $userId]);
                    $passwordSuccess = 'Password changed successfully.';
                }
            }
        }
    }
}

// Lấy lại user sau khi có thể đã cập nhật
$currentUser = getCurrentUser($pdo, $userId);
if (!$currentUser) {
    // Nếu vì lý do gì đó user không tồn tại nữa
    session_destroy();
    header('Location: index.php?page=login_signup');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PhoneStore - Profile</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<?php include 'views/layouts/header.php'; ?>

<main class="admin-page">
  <div class="admin-container">
    <h1 class="admin-title">My Profile</h1>
    <p class="admin-subtitle">View and update your account information.</p>

    <div class="row g-4">
      <!-- Thông tin cá nhân -->
      <div class="col-md-6">
        <div class="card card-body">
          <h2 class="admin-section-title">Account details</h2>

          <?php if ($profileError): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($profileError) ?></div>
          <?php elseif ($profileSuccess): ?>
            <div class="alert alert-success py-2"><?= htmlspecialchars($profileSuccess) ?></div>
          <?php endif; ?>

          <form method="post" action="index.php?page=profile">
            <input type="hidden" name="update_profile" value="1" />

            <div class="mb-3">
              <label class="form-label" for="username">Username</label>
              <input
                type="text"
                class="form-control"
                id="username"
                name="username"
                value="<?= htmlspecialchars($currentUser['username']) ?>"
                required
              >
            </div>

            <div class="mb-3">
              <label class="form-label" for="full_name">Full name</label>
              <input
                type="text"
                class="form-control"
                id="full_name"
                name="full_name"
                value="<?= htmlspecialchars($currentUser['full_name'] ?? '') ?>"
                placeholder="Your full name"
              >
            </div>

            <div class="mb-3">
              <label class="form-label">Role</label>
              <input
                type="text"
                class="form-control"
                value="<?= $currentUser['is_admin'] ? 'Administrator' : 'Customer' ?>"
                disabled
              >
            </div>

            <div class="form-actions mt-3">
              <button type="submit" class="btn-primary-save">Save changes</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Đổi mật khẩu -->
      <div class="col-md-6">
        <div class="card card-body">
          <h2 class="admin-section-title">Change password</h2>

          <?php if ($passwordError): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($passwordError) ?></div>
          <?php elseif ($passwordSuccess): ?>
            <div class="alert alert-success py-2"><?= htmlspecialchars($passwordSuccess) ?></div>
          <?php endif; ?>

          <form method="post" action="index.php?page=profile">
            <input type="hidden" name="change_password" value="1" />

            <div class="mb-3">
              <label class="form-label" for="current_password">Current password</label>
              <input
                type="password"
                class="form-control"
                id="current_password"
                name="current_password"
                required
              >
            </div>

            <div class="mb-3">
              <label class="form-label" for="new_password">New password</label>
              <input
                type="password"
                class="form-control"
                id="new_password"
                name="new_password"
                required
              >
            </div>

            <div class="mb-3">
              <label class="form-label" for="confirm_password">Confirm new password</label>
              <input
                type="password"
                class="form-control"
                id="confirm_password"
                name="confirm_password"
                required
              >
            </div>

            <div class="form-actions mt-3">
              <button type="submit" class="btn-primary-save">Update password</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include 'views/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
