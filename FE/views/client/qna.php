<?php
// Load settings helper
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../../helpers/settings_helper.php';
}
require_once 'controllers/QnaController.php';
$qnaController = new QnaController();
$qnaList = $qnaController->getModel()->getAll(); // Fetch all Q&A
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars(getSetting('general.site_name', 'CellphoneS')); ?> - Q&A</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    .qna-page-section {
        padding: 60px 0;
        background-color: #f9f9f9;
        min-height: 80vh;
    }
    .faq-title {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 28px;
        color: #1a1c1c;
        border-left: 4px solid #e11b22;
        padding-left: 15px;
        margin-bottom: 30px;
    }
    .faq-item {
        background: #ffffff;
        border: 1px solid #e7bdb8;
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
    }
    .faq-question {
        padding: 20px;
        font-weight: 600;
        font-size: 16px;
        color: #1a1c1c;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
    }
    .faq-question i {
        color: #e11b22;
        transition: transform 0.3s ease;
    }
    .faq-question.active i {
        transform: rotate(180deg);
    }
    .faq-answer {
        padding: 0 20px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        color: #5d3f3c;
        font-size: 14px;
        line-height: 1.5;
        border-top: 1px solid transparent;
    }
    .faq-item.active .faq-answer {
        padding: 20px;
        max-height: 1000px; /* arbitrary large value */
        border-top: 1px solid #e7bdb8;
    }
    .support-box {
        background: #e2e2e2;
        border: 2px dashed #926e6b;
        border-radius: 12px;
        padding: 30px;
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .support-box-content h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1a1c1c;
        margin-bottom: 5px;
    }
    .support-box-content p {
        color: #5d3f3c;
        margin-bottom: 0;
        font-size: 14px;
    }
    .support-btn-group .btn-dark-msg {
        background: #1a1c1c;
        color: #ffffff;
        font-weight: 700;
        padding: 10px 20px;
        border: none;
        margin-right: 10px;
        text-transform: uppercase;
        font-size: 12px;
        border-radius: 4px;
    }
    .support-btn-group .btn-red-call {
        background: #b70013;
        color: #ffffff;
        font-weight: 700;
        padding: 10px 20px;
        border: none;
        text-transform: uppercase;
        font-size: 12px;
        border-radius: 4px;
    }
  </style>
  <!-- adding bootstrap icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

  <?php include 'views/layouts/header.php'; ?>

  <main class="qna-page-section">
    <div class="container" style="max-width: 900px;">
      <h1 class="faq-title">Frequently Asked Questions</h1>

      <div class="faq-list">
        <?php if (!empty($qnaList)): ?>
            <?php foreach ($qnaList as $qna): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <span><?= htmlspecialchars($qna['question']) ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?= nl2br(htmlspecialchars($qna['answer'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No Q&A items available at the moment.</p>
        <?php endif; ?>
      </div>

      <!-- Contact Support Box -->
      <div class="support-box">
          <div class="support-box-content">
              <h3>Don't see your problem listed?</h3>
              <p>Get a custom quote for specific hardware issues or legacy devices.</p>
          </div>
          <div class="support-btn-group">
              <button class="btn-dark-msg">Message Us</button>
              <button class="btn-red-call">Call Support</button>
          </div>
      </div>

    </div>
  </main>

  <?php include 'views/layouts/footer.php'; ?>

  <script>
      document.querySelectorAll('.faq-question').forEach(item => {
          item.addEventListener('click', () => {
              const parent = item.parentElement;
              // Close all other
              document.querySelectorAll('.faq-item').forEach(child => {
                  if (child !== parent) {
                      child.classList.remove('active');
                      child.querySelector('.faq-question').classList.remove('active');
                  }
              });
              // Toggle current
              parent.classList.toggle('active');
              item.classList.toggle('active');
          });
      });
  </script>

</body>
</html>
