<?php
    require_once __DIR__ . '/includes/auth.php';
    require_once __DIR__ . '/includes/db_connect.php';
    // quick stats
    $users_count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $props_count = $pdo->query('SELECT COUNT(*) FROM properties')->fetchColumn();
    include __DIR__ . '/includes/header.php';
    ?>
    <div class="row">
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-body">
            <h5>Users</h5>
            <p class="display-6"><?php echo $users_count; ?></p>
            <a href="users.php" class="btn btn-sm btn-outline-primary">Manage Users</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-body">
            <h5>Properties</h5>
            <p class="display-6"><?php echo $props_count; ?></p>
            <a href="properties.php" class="btn btn-sm btn-outline-primary">Manage Properties</a>
          </div>
        </div>
      </div>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>