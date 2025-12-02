<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db_connect.php';
include __DIR__ . '/includes/header.php';

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS chatbot_faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(100) NOT NULL,
    question TEXT NULL,
    answer TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['keyword'], $_POST['answer'])) {
    $keyword = trim($_POST['keyword']);
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer']);

    if ($keyword !== '' && $answer !== '') {
        $stmt = $pdo->prepare("INSERT INTO chatbot_faq (keyword, question, answer) VALUES (?, ?, ?)");
        $stmt->execute([$keyword, $question, $answer]);
        $msg = "FAQ added successfully.";
    } else {
        $msg = "Keyword and Answer are required.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM chatbot_faq WHERE id = ?");
    $stmt->execute([$id]);
    $msg = "FAQ deleted.";
}

// Fetch all FAQs
$faqs = $pdo->query("SELECT * FROM chatbot_faq ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
  <div class="col-12">
    <h2>Chatbot Trainer (FAQ)</h2>
    <p class="text-muted">Add custom questions and answers that the chatbot can use when talking to users.</p>
    <?php if (!empty($msg)): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Add New FAQ</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Keyword (English)</label>
            <input type="text" name="keyword" class="form-control" placeholder="e.g. refund, rules, timing" required>
            <small class="text-muted">When the user's message (after translation to English) contains this keyword, this FAQ can be used.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Question (optional)</label>
            <input type="text" name="question" class="form-control" placeholder="Example user question">
          </div>

          <div class="mb-3">
            <label class="form-label">Answer</label>
            <textarea name="answer" class="form-control" rows="3" placeholder="Chatbot reply text" required></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Save FAQ</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Existing FAQs</h5>
        <?php if (empty($faqs)): ?>
          <p class="text-muted mb-0">No FAQs added yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Keyword</th>
                  <th>Answer</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($faqs as $f): ?>
                <tr>
                  <td><?php echo (int)$f['id']; ?></td>
                  <td><?php echo htmlspecialchars($f['keyword']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($f['answer'])); ?></td>
                  <td>
                    <a href="?delete=<?php echo (int)$f['id']; ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this FAQ?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
