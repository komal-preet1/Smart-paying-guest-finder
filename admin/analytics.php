<?php
// admin/analytics.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connect.php';
include __DIR__ . '/includes/header.php';


/* ---------- CITY LEVEL STATS ---------- */
$cityStats = [];
try {
    // LEFT JOIN so even cities with 0 properties are visible
    $stmt = $pdo->query("
        SELECT c.name AS city,
               COUNT(p.id) AS total_props,
               AVG(p.rent) AS avg_rent,
               AVG((p.rating_clean + p.rating_food + p.rating_safety) / 3) AS avg_rating
        FROM cities c
        LEFT JOIN properties p ON p.city_id = c.id
        GROUP BY c.id, c.name
        ORDER BY total_props DESC
    ");
    $cityStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $cityStats = [];
}


/* ---------- RENT BUCKET DISTRIBUTION ---------- */
$rentBuckets = [];
try {
    $stmt = $pdo->query("
        SELECT
          CASE
            WHEN rent < 5000 THEN '< 5000'
            WHEN rent BETWEEN 5000 AND 9999 THEN '5000 - 9999'
            WHEN rent BETWEEN 10000 AND 14999 THEN '10000 - 14999'
            ELSE '15000+'
          END AS range_label,
          COUNT(*) AS cnt
        FROM properties
        GROUP BY range_label
        ORDER BY MIN(rent)
    ");
    $rentBuckets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $rentBuckets = [];
}

/* ---------- CHATBOT USAGE (LAST 7 DAYS) ---------- */
$chatByDay = [];
try {
    $stmt = $pdo->query("
        SELECT DATE(created_at) AS chat_day,
               COUNT(*) AS total_msgs
        FROM chat_logs
        GROUP BY DATE(created_at)
        ORDER BY chat_day DESC
        LIMIT 7
    ");
    $chatByDay = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $chatByDay = [];
}

// Prepare data for JS
$cityLabels = [];
$cityCounts = [];
$cityAvgRent = [];
$cityAvgRating = [];

foreach ($cityStats as $row) {
    $cityLabels[] = $row['city'];
    $cityCounts[] = (int)$row['total_props'];
    $cityAvgRent[] = round((float)$row['avg_rent'], 2);
    $cityAvgRating[] = round((float)$row['avg_rating'], 2);
}

$rentLabels = [];
$rentCounts = [];
foreach ($rentBuckets as $row) {
    $rentLabels[] = $row['range_label'];
    $rentCounts[] = (int)$row['cnt'];
}

$chatLabels = [];
$chatCounts = [];
foreach (array_reverse($chatByDay) as $row) { // reverse for chronological order
    $chatLabels[] = $row['chat_day'];
    $chatCounts[] = (int)$row['total_msgs'];
}
?>

<div class="row mb-4">
  <div class="col-12">
    <h2>Analytics Dashboard</h2>
    <p class="text-muted">Overview of properties, rents and chatbot usage.</p>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Total Cities with PGs</h6>
        <p class="display-6 mb-0">
          <?php echo count($cityStats); ?>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Total Properties</h6>
        <p class="display-6 mb-0">
          <?php
          $totalProps = 0;
          foreach ($cityStats as $c) {
              $totalProps += (int)$c['total_props'];
          }
          echo $totalProps;
          ?>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Chat Messages (Last 7 days)</h6>
        <p class="display-6 mb-0">
          <?php
          $totalChats = 0;
          foreach ($chatByDay as $c) {
              $totalChats += (int)$c['total_msgs'];
          }
          echo $totalChats;
          ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-7 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Properties per City</h5>
        <canvas id="cityPropsChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-5 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Average Rent per City</h5>
        <canvas id="avgRentChart"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Rent Distribution</h5>
        <canvas id="rentBucketChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Chatbot Usage (Last 7 days)</h5>
        <?php if (!empty($chatLabels)): ?>
          <canvas id="chatUsageChart"></canvas>
        <?php else: ?>
          <p class="text-muted mb-0">No chatbot data yet. Start chatting with the PG Assistant to see stats here.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body table-responsive">
        <h5 class="card-title">City-wise Summary</h5>
        <table class="table table-striped align-middle mb-0">
          <thead>
          <tr>
            <th>City</th>
            <th>Total Properties</th>
            <th>Average Rent (₹)</th>
            <th>Average Rating (/5)</th>
          </tr>
          </thead>
          <tbody>
          <?php if (empty($cityStats)): ?>
            <tr>
              <td colspan="4" class="text-muted">No data available.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($cityStats as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['city']) ?></td>
                <td><?= (int)$row['total_props'] ?></td>
                <td><?= round((float)$row['avg_rent'], 2) ?></td>
                <td><?= round((float)$row['avg_rating'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const cityLabels = <?php echo json_encode($cityLabels); ?>;
  const cityCounts = <?php echo json_encode($cityCounts); ?>;
  const cityAvgRent = <?php echo json_encode($cityAvgRent); ?>;
  const rentLabels = <?php echo json_encode($rentLabels); ?>;
  const rentCounts = <?php echo json_encode($rentCounts); ?>;
  const chatLabels = <?php echo json_encode($chatLabels); ?>;
  const chatCounts = <?php echo json_encode($chatCounts); ?>;

  // Properties per city
  if (document.getElementById('cityPropsChart')) {
    new Chart(document.getElementById('cityPropsChart'), {
      type: 'bar',
      data: {
        labels: cityLabels,
        datasets: [{
          label: 'Properties',
          data: cityCounts
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision:0 }
          }
        }
      }
    });
  }

  // Average rent per city
  if (document.getElementById('avgRentChart')) {
    new Chart(document.getElementById('avgRentChart'), {
      type: 'bar',
      data: {
        labels: cityLabels,
        datasets: [{
          label: 'Average Rent (₹)',
          data: cityAvgRent
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  // Rent bucket distribution
  if (document.getElementById('rentBucketChart')) {
    new Chart(document.getElementById('rentBucketChart'), {
      type: 'pie',
      data: {
        labels: rentLabels,
        datasets: [{
          data: rentCounts
        }]
      },
      options: {
        responsive: true
      }
    });
  }

  // Chatbot usage line chart
  if (document.getElementById('chatUsageChart') && chatLabels.length > 0) {
    new Chart(document.getElementById('chatUsageChart'), {
      type: 'line',
      data: {
        labels: chatLabels,
        datasets: [{
          label: 'Messages',
          data: chatCounts,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision:0 }
          }
        }
      }
    });
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
