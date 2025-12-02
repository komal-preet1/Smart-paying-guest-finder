<?php
    require_once __DIR__ . '/includes/auth.php';
    require_once __DIR__ . '/includes/db_connect.php';
    // fetch users
    $stmt = $pdo->query('SELECT * FROM users ORDER BY id DESC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include __DIR__ . '/includes/header.php';
    ?>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Users <a href="edit_user.php" class="btn btn-sm btn-primary float-end">Add User</a></h5>
        <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Password</th><th>Gender</th><th>College Name</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td><?php echo $u['id']; ?></td>
              <td><?php echo htmlspecialchars($u['full_name']); ?></td>
              <td><?php echo htmlspecialchars($u['email']); ?></td>
              <td><?php echo htmlspecialchars($u['phone']); ?></td>
               <td><?php echo htmlspecialchars($u['password']); ?></td>
                <td><?php echo htmlspecialchars($u['gender']); ?></td>
                 <td><?php echo htmlspecialchars($u['college_name']); ?></td>
              <td>
                <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                <a href="users.php?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
    <?php
    // handle deletion (simple)
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        header('Location: users.php');
        exit;
    }
    include __DIR__ . '/includes/footer.php';
    ?>