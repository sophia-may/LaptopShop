<?php
// views/admin/manage_profile.php
// Trang admin quản lý user

require_once 'config/db.php';

// Chỉ cho admin
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['is_admin']) ||
    (int)$_SESSION['is_admin'] !== 1
) {
    header('Location: index.php?page=home');
    exit();
}

$db  = new Database();
$pdo = $db->connect();

$flashMessage = '';
$flashType    = 'success';

$loggedInAdminId = (int)$_SESSION['user_id'];

/* ===== Helper: xác định user bị ban qua full_name prefix ===== */
function isBannedUserRow(array $row): bool {
    if (!isset($row['full_name']) || $row['full_name'] === null) return false;
    return str_starts_with($row['full_name'], '[BANNED]');
}

function stripBannedPrefix(?string $name): ?string {
    if ($name === null) return null;
    $prefix = '[BANNED] ';
    if (str_starts_with($name, $prefix)) {
        return substr($name, strlen($prefix));
    }
    return $name;
}

/* ===== Xử lý GET action: delete / ban / unban ===== */
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $userId = (int)$_GET['id'];

    if ($userId === $loggedInAdminId && in_array($action, ['delete', 'ban'], true)) {
        $flashMessage = 'You cannot delete or ban your own account.';
        $flashType    = 'danger';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            $flashMessage = 'User not found.';
            $flashType    = 'danger';
        } else {
            if ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $flashMessage = 'User deleted successfully.';
                $flashType    = 'success';

            } elseif ($action === 'ban') {
                if (isBannedUserRow($userRow)) {
                    $flashMessage = 'This user is already banned.';
                    $flashType    = 'info';
                } else {
                    // Ban: đổi password random + thêm prefix [BANNED] vào full_name
                    $randomPassword = bin2hex(random_bytes(16));
                    $hash           = password_hash($randomPassword, PASSWORD_BCRYPT);
                    $newName        = '[BANNED] ' . ($userRow['full_name'] ?? $userRow['username']);

                    $stmt = $pdo->prepare("UPDATE users SET password = ?, full_name = ? WHERE id = ?");
                    $stmt->execute([$hash, $newName, $userId]);

                    $flashMessage = 'User has been banned (account locked).';
                    $flashType    = 'warning';
                }

            } elseif ($action === 'unban') {
                if (!isBannedUserRow($userRow)) {
                    $flashMessage = 'This user is not banned.';
                    $flashType    = 'info';
                } else {
                    $cleanName = stripBannedPrefix($userRow['full_name']);
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                    $stmt->execute([$cleanName, $userId]);

                    $flashMessage = 'User has been unbanned. Set a new password if needed.';
                    $flashType    = 'success';
                }
            }
        }
    }
}

/* ===== Xử lý POST: edit user ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $editId    = (int)($_POST['user_id'] ?? 0);
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $is_admin  = isset($_POST['is_admin']) ? 1 : 0;
    $new_pass  = $_POST['new_password'] ?? '';

    if ($editId <= 0 || $username === '') {
        $flashMessage = 'Invalid user data.';
        $flashType    = 'danger';
    } else {
        // Check trùng username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
        $stmt->execute([$username, $editId]);
        if ($stmt->fetch()) {
            $flashMessage = 'Username is already taken by another account.';
            $flashType    = 'danger';
        } else {
            if ($new_pass !== '') {
                $hash   = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt   = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, is_admin = ?, password = ? WHERE id = ?");
                $params = [$username, $full_name === '' ? null : $full_name, $is_admin, $hash, $editId];
            } else {
                $stmt   = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, is_admin = ? WHERE id = ?");
                $params = [$username, $full_name === '' ? null : $full_name, $is_admin, $editId];
            }

            $stmt->execute($params);

            // Nếu là chính admin đang đăng nhập
            if ($editId === $loggedInAdminId) {
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = $is_admin;
            }

            $flashMessage = 'User information updated successfully.';
            $flashType    = 'success';
        }
    }
}

/* ===== Lấy danh sách users (Pagination) ===== */
require_once 'models/UserModel.php';
$userModel = new UserModel();

$page = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
$limit = 10;
$filters = [
    'search' => $_GET['search'] ?? '',
    'role' => $_GET['role'] ?? ''
];

$userData = $userModel->getPaginated($page, $limit, $filters);
$users = $userData['items'];
$totalPages = $userData['total_pages'];
$totalUsers = $userData['total'];
?>
<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

        <div class="content-header mb-4">
            <h2><i class="bi bi-people"></i> Manage Users</h2>
            <p>View all registered accounts, update their information, ban/unban or delete members to keep your store secure.</p>
        </div>

        <?php if ($flashMessage): ?>
        <div class="alert alert-<?= htmlspecialchars($flashType) ?>"><?= htmlspecialchars($flashMessage) ?></div>
        <?php endif; ?>

        <div class="content-card">
      <table class="table admin-table align-middle">
        <thead>
          <tr>
            <th style="width:5%;">ID</th>
            <th style="width:25%;">Full name</th>
            <th style="width:10%;">Role</th>
            <th style="width:10%;">Status</th>
            <th style="width:30%;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u):
            // $isAdmin  = (int)$u['is_admin'] === 1;
            $isBanned = isBannedUserRow($u);
        ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>

            <td><?= htmlspecialchars($u['full_name'] ?? '') ?></td>
            <td>
              <?php ?>
                <span class="badge bg-primary">Admin</span>
              <?php?>
                <span class="badge bg-secondary">Customer</span>
            </td>
            <td>

              <?php  ?>
                <span class="badge bg-success">Active</span>
              <?php?>
            </td>
            <td>
              <!-- Nút mở modal edit -->
              <button
                class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#editUserModal<?= (int)$u['id'] ?>">
                Edit
              </button>

              <?php  ?>
                <a href="index.php?page=manage_profile&action=unban&id=<?= (int)$u['id'] ?>"
                   class="btn btn-sm btn-warning"
                   onclick="return confirm('Unban this user?');">
                  Unban
                </a>
              <?php?>
                <?php if ((int)$u['id'] !== $loggedInAdminId): ?>
                  <a href="index.php?page=manage_profile&action=ban&id=<?= (int)$u['id'] ?>"
                     class="btn btn-sm btn-outline-warning"
                     onclick="return confirm('Ban this user? This will lock their account.');">
                    Ban
                  </a>
                <?php  ?>
              <?php endif; ?>

              <?php if ((int)$u['id'] !== $loggedInAdminId): ?>
                <a href="index.php?page=manage_profile&action=delete&id=<?= (int)$u['id'] ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this user permanently?');">
                  Delete
                </a>
              <?php endif; ?>
            </td>
          </tr>

          <!-- Modal Edit User -->
          <div class="modal modal-blur fade" id="editUserModal<?= (int)$u['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="post" action="index.php?page=manage_profile">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit user #<?= (int)$u['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="update_user" value="1" />
                    <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>" />

                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input
                        type="text"
                        class="form-control"
                        name="username"
                        value="<?= htmlspecialchars($u['username'] ?? '') ?>"
                        required
                      >
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Full name</label>
                      <input
                        type="text"
                        class="form-control"
                        name="full_name"
                        value="<?= htmlspecialchars(stripBannedPrefix($u['full_name'] ?? '')) ?>"
                        placeholder="Optional"
                      >
                    </div>

                    <div class="mb-3 form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="is_admin_<?= (int)$u['id'] ?>"
                        name="is_admin"
                        <?= ($u['is_admin'] ?? false) ? 'checked' : '' ?>
                      >
                      <label class="form-check-label" for="is_admin_<?= (int)$u['id'] ?>">
                        Administrator account
                      </label>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">New password (optional)</label>
                      <input
                        type="password"
                        class="form-control"
                        name="new_password"
                        placeholder="Leave blank to keep current password"
                      >
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="User pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=manage_profile&user_page=1">&laquo;&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=manage_profile&user_page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=manage_profile&user_page=<?= $totalPages ?>">&raquo;&raquo;</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'views/layouts/admin_footer.php'; ?>
