<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connect.php';

/* ✅ HANDLE DELETE FIRST (prevents header error) */
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // delete amenities mapping first
    $pdo->prepare("DELETE FROM properties_amenities WHERE property_id = ?")
        ->execute([$delete_id]);

    // delete property (property_images auto deletes if FK CASCADE exists)
    $pdo->prepare("DELETE FROM properties WHERE id = ?")
        ->execute([$delete_id]);

    header("Location: properties.php");
    exit;
}

/* ✅ FETCH PROPERTIES */
$stmt = $pdo->query(
    "SELECT p.*, c.name AS city_name
     FROM properties p
     LEFT JOIN cities c ON p.city_id = c.id
     ORDER BY p.id DESC"
);
$props = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">
      Properties
      <a href="edit_property.php" class="btn btn-sm btn-primary float-end">Add Property</a>
    </h5>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>City</th>
            <th>Image</th>
            <th>Name</th>
            <th>Address</th>
            <th>Gender</th>
            <th>Rent</th>
            <th>Clean</th>
            <th>Food</th>
            <th>Safety</th>
            <th>Amenities</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
        <?php if (!empty($props)): ?>
        <?php foreach ($props as $p): ?>

          <?php
          $pid = (int)$p['id'];

          /* ✅ FETCH FIRST IMAGE */
          $imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ? LIMIT 1");
          $imgStmt->execute([$pid]);
          $img = $imgStmt->fetch(PDO::FETCH_ASSOC);

          $thumb = $img
            ? "../" . $img['image_path']
            : "../assets/images/default.jpg";

          /* ✅ COUNT AMENITIES */
          $amStmt = $pdo->prepare("SELECT COUNT(*) FROM properties_amenities WHERE property_id = ?");
          $amStmt->execute([$pid]);
          $amCount = (int)$amStmt->fetchColumn();
          ?>

          <tr>
            <td><?= $pid ?></td>
            <td><?= htmlspecialchars($p['city_name'] ?? '') ?></td>

            <td>
              <img src="<?= htmlspecialchars($thumb) ?>"
                   style="width:80px;height:60px;object-fit:cover;border-radius:5px;">
            </td>

            <td><?= htmlspecialchars($p['name']) ?></td>
            <td style="max-width:260px;white-space: normal;">
                <?= htmlspecialchars($p['address']) ?>
            </td>
            <td><?= htmlspecialchars($p['gender']) ?></td>
            <td>₹ <?= htmlspecialchars($p['rent']) ?></td>
            <td><?= htmlspecialchars($p['rating_clean']) ?></td>
            <td><?= htmlspecialchars($p['rating_food']) ?></td>
            <td><?= htmlspecialchars($p['rating_safety']) ?></td>

            <td>
              <?= $amCount > 0 ? $amCount . " selected" : "None" ?>
            </td>

            <td>
              <a href="edit_property.php?id=<?= $pid ?>"
                 class="btn btn-sm btn-outline-secondary">Edit</a>

              <a href="properties.php?delete=<?= $pid ?>"
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Are you sure you want to delete this property?');">
                 Delete
              </a>
            </td>
          </tr>

        <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="12" class="text-center">No properties found</td>
          </tr>
        <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
