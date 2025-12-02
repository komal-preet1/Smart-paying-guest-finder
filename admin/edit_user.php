<?php
    require_once __DIR__ . '/includes/auth.php';
    require_once __DIR__ . '/includes/db_connect.php';
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $college_name = $_POST['college_name'] ?? '';

        if ($id) {
            $pdo->prepare('UPDATE users SET full_name=?, email=?, phone=? ,gender=?,college_name=?,password=? WHERE id=?')
                ->execute([$full_name, $email, $phone, $gender, $college_name, $password, $id]);
        } else {
            $pdo->prepare('INSERT INTO users (full_name,email,phone,password,gender,college_name) VALUES (?,?,?,?,?,?)')
                ->execute([$full_name, $email, $phone, $password, $gender, $college_name]);
            $id = $pdo->lastInsertId();
        }
        header('Location: users.php');
        exit;
    }
    include __DIR__ . '/includes/header.php';
?>
    <div class="card">
      <div class="card-body">
        <h5><?php echo $id ? 'Edit' : 'Add'; ?> User</h5>
        <form method="post">
          <div class="mb-3"><label class="form-label">Name</label><input name="full_name" value="<?php echo $user['full_name'] ?? ''; ?>" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Email</label><input name="email" value="<?php echo $user['email'] ?? ''; ?>" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Phone</label><input name="phone" value="<?php echo $user['phone'] ?? ''; ?>" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Password</label><input name="password" value="<?php echo $user['password'] ?? ''; ?>" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Gender</label><input name="gender" value="<?php echo $user['gender'] ?? ''; ?>" class="form-control"></div>
          <div class="mb-3"><label class="form-label">College Name</label><input name="college_name" value="<?php echo $user['college_name'] ?? ''; ?>" class="form-control"></div>
          <button class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
