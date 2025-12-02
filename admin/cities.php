<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connect.php';
include __DIR__ . '/includes/header.php';

$success = '';
$error = '';

// Handle add city
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = "City name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO cities (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "City added successfully.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // duplicate
                $error = "This city already exists.";
            } else {
                $error = "Failed to add city. " . $e->getMessage();
            }
        }
    }
}

// Fetch all cities with number of properties
$cities = [];
try {
    $stmt = $pdo->query("
        SELECT c.id, c.name,
               COUNT(p.id) AS total_props
        FROM cities c
        LEFT JOIN properties p ON p.city_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Failed to load cities: " . $e->getMessage();
}
?>

<div class="row mb-4">
  <div class="col-12">
    <h2>Manage Cities</h2>
    <p class="text-muted">Add new cities and see how many PGs are mapped to each city.</p>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Add New City</h5>
        <?php if ($success): ?>
          <div class="alert alert-success py-2"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">City Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Pune" required>
          </div>
          <button type="submit" class="btn btn-primary">Add City</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Existing Cities</h5>
        <table class="table table-sm">
          <thead>
            <tr>
              <th>#</th>
              <th>City</th>
              <th>Properties</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($cities): ?>
              <?php foreach ($cities as $c): ?>
                <tr>
                  <td><?php echo (int)$c['id']; ?></td>
                  <td><?php echo htmlspecialchars($c['name']); ?></td>
                  <td><?php echo (int)$c['total_props']; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">No cities found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
