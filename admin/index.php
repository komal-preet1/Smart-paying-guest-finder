<?php
    // admin/index.php - login page
    session_start();
    require_once __DIR__ . '/includes/db_connect.php';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $error = 'Enter username and password';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin) {
                // support both hashed and plain passwords
                if ((isset($admin['password']) && password_verify($password, $admin['password'])) || ($password === $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid credentials';
                }
            } else {
                $error = 'Invalid credentials';
            }
        }
    }
    include __DIR__ . '/includes/header.php';
    ?>
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3 text-center">Admin Login</h4>
            <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <form method="post" action="">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100">Login</button>
            </form>
            <div class="mt-3 text-muted small">Use your existing admin credentials.</div>
          </div>
        </div>
      </div>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>