<?php
// Load settings helper
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../../helpers/settings_helper.php';
}
include 'views/layouts/header.php';
?>

  <main class="contact-page">

    <!-- HERO IMAGE + BREADCRUMB -->
    <section class="contact-hero">
      <div class="contact-hero-banner"></div>
      <div class="contact-hero-overlay">
        <div class="contact-hero-inner">
          <p class="contact-hero-label">Contact</p>
          <h1 class="contact-hero-title">Contact</h1>
          <nav class="contact-breadcrumb">
            <a href="index.php?page=home">Home</a>
            <span>›</span>
            <span>Contact</span>
          </nav>
        </div>
      </div>
    </section>

    <!-- MAIN CONTACT CONTENT -->
    <section class="contact-main-section">
      <div class="contact-container">

        <header class="contact-main-header text-center">
          <h2><?php echo htmlspecialchars(getSetting('contact.page_title', 'Get In Touch With Us')); ?></h2>
          <p>
            <?php echo htmlspecialchars(getSetting('contact.page_subtitle', "For more information about our products & services, feel free to drop us an email. Our staff will always be there to help you out. Don't hesitate!")); ?>
          </p>
        </header>

        <div class="contact-main-grid">

          <!-- LEFT COLUMN: INFO -->
          <div class="contact-info-column">
            <div class="contact-info-block">
              <div class="contact-info-icon">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div>
                <h3 class="contact-info-title">Address</h3>
                <p class="contact-info-text">
                  <?php echo nl2br(htmlspecialchars(getSetting('contact.address', '123 Street, City'))); ?>
                </p>
              </div>
            </div>

            <div class="contact-info-block">
              <div class="contact-info-icon">
                <i class="bi bi-telephone-fill"></i>
              </div>
              <div>
                <h3 class="contact-info-title">Phone</h3>
                <p class="contact-info-text mb-1">
                  <?php echo htmlspecialchars(getSetting('contact.phone', '+84 123 456 789')); ?>
                </p>
                <p class="contact-info-text mb-1">
                  Email: <?php echo htmlspecialchars(getSetting('contact.email', 'info@phonestore.com')); ?>
                </p>
              </div>
            </div>

            <div class="contact-info-block">
              <div class="contact-info-icon">
                <i class="bi bi-clock-fill"></i>
              </div>
              <div>
                <h3 class="contact-info-title">Working Time</h3>
                <p class="contact-info-text mb-1">
                  <?php echo nl2br(htmlspecialchars(getSetting('contact.working_hours', 'Mon-Fri: 9AM - 6PM'))); ?>
                </p>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN: FORM -->
          <div class="contact-form-column">
            <form id="contactForm" novalidate>
              <div class="mb-3">
                <label for="contact_name" class="form-label">Your name <span class="text-danger">*</span></label>
                <input
                  type="text"
                  class="form-control contact-input"
                  id="contact_name"
                  name="name"
                  placeholder="Enter your name"
                  required
                  minlength="2"
                />
              </div>

              <div class="mb-3">
                <label for="contact_email" class="form-label">Email address <span class="text-danger">*</span></label>
                <input
                  type="email"
                  class="form-control contact-input"
                  id="contact_email"
                  name="email"
                  placeholder="name@example.com"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="contact_subject" class="form-label">Subject</label>
                <input
                  type="text"
                  class="form-control contact-input"
                  id="contact_subject"
                  name="subject"
                  placeholder="This is optional"
                />
              </div>

              <div class="mb-4">
                <label for="contact_message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea
                  class="form-control contact-input contact-textarea"
                  id="contact_message"
                  name="message"
                  rows="4"
                  placeholder="Hi! I'd like to ask about..."
                  required
                  minlength="10"
                ></textarea>
              </div>

              <button type="submit" class="contact-submit-btn">
                <i class="bi bi-send me-2"></i> Submit
              </button>

            </form>
          </div>

        </div>
      </div>
    </section>

  </main>

  <?php include 'views/layouts/footer.php'; ?>

<!-- Script to send user contact info to ContactController -->
  <script src="assets/javascript/send_contact.js"></script>
