<?php
// Load settings helper
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../../helpers/settings_helper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?> - About</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <?php include 'views/layouts/header.php'; ?>

  <section class="about-section section-intro py-5">
    <div class="container-default d-flex align-items-center justify-content-start">
      <div class="about-content text-start col-md-8">
        <h2><?php echo htmlspecialchars(getSetting('about.page_title', 'About Us')); ?></h2>
        <?php if ($subtitle = getSetting('about.hero_subtitle')): ?>
        <p class="text-muted mb-3"><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
        <?php if ($introTitle = getSetting('about.intro_title')): ?>
        <h3 class="h4 mt-4"><?php echo htmlspecialchars($introTitle); ?></h3>
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars(getSetting('about.intro', 'Welcome to our store.'))); ?></p>
      </div>
    </div>
  </section>

  <section class="about-section section-why py-5">
    <div class="container-default d-flex align-items-center justify-content-end">
      <div class="about-content text-end col-md-8">
        <h2><?php echo htmlspecialchars(getSetting('about.mission_title', 'Our Mission')); ?></h2>
        <p><?php echo nl2br(htmlspecialchars(getSetting('about.mission', 'Our mission is to serve you better.'))); ?></p>
      </div>
    </div>
  </section>

  <section class="about-section section-placeholder py-5">
    <div class="container-default d-flex align-items-center justify-content-start">
      <div class="about-content text-start col-md-8">
        <h2><?php echo htmlspecialchars(getSetting('about.vision_title', 'Our Vision')); ?></h2>
        <p><?php echo nl2br(htmlspecialchars(getSetting('about.vision', 'To be the leading store in our field.'))); ?></p>
        
        <?php if ($valuesTitle = getSetting('about.values_title')): ?>
        <h3 class="h4 mt-4"><?php echo htmlspecialchars($valuesTitle); ?></h3>
        <?php endif; ?>
        <?php if ($values = getSetting('about.values')): ?>
        <p><?php echo nl2br(htmlspecialchars($values)); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php include 'views/layouts/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>