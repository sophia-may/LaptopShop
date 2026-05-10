<?php
// Load settings helper
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../../helpers/settings_helper.php';
}
?>
<!-- CellphoneS-style Footer -->
<footer class="cps-footer">
  <div class="cps-footer-container">
    <div class="row">
      <!-- Column 1: Hotline -->
      <div class="col-md-3 mb-4">
        <h5 class="cps-footer-title">Tổng đài hỗ trợ miễn phí</h5>
        <ul class="cps-footer-links">
          <li>Gọi mua hàng: <strong><?php echo htmlspecialchars(getSetting('header.phone_number', '1800.0000')); ?></strong> (7h30 - 22h00)</li>
          <li>Gọi khiếu nại: <strong>1800.0001</strong> (8h00 - 21h30)</li>
          <li>Gọi bảo hành: <strong>1800.0002</strong> (8h00 - 21h00)</li>
        </ul>
        <h5 class="cps-footer-title mt-4">Phương thức thanh toán</h5>
        <div class="cps-payment-methods">
          <i class="bi bi-credit-card-fill fs-3 text-primary me-2"></i>
          <i class="bi bi-cash fs-3 text-success me-2"></i>
          <i class="bi bi-wallet2 fs-3 text-info me-2"></i>
          <i class="bi bi-bank fs-3 text-warning"></i>
        </div>
      </div>

      <!-- Column 2: Info -->
      <div class="col-md-3 mb-4">
        <h5 class="cps-footer-title">Thông tin chính sách</h5>
        <ul class="cps-footer-links">
          <li><a href="index.php?page=qna">Chính sách bảo hành</a></li>
          <li><a href="index.php?page=qna">Chính sách đổi trả</a></li>
          <li><a href="index.php?page=qna">Giao hàng & Thanh toán</a></li>
          <li><a href="index.php?page=qna">Hướng dẫn mua online</a></li>
          <li><a href="index.php?page=qna">Chính sách bảo mật</a></li>
        </ul>
      </div>

      <!-- Column 3: Services -->
      <div class="col-md-3 mb-4">
        <h5 class="cps-footer-title">Dịch vụ & Thông tin khác</h5>
        <ul class="cps-footer-links">
          <li><a href="index.php?page=about">Giới thiệu công ty</a></li>
          <li><a href="index.php?page=about">Tuyển dụng</a></li>
          <li><a href="index.php?page=post">Tin tức công nghệ</a></li>
          <li><a href="index.php?page=contact">Liên hệ hợp tác kinh doanh</a></li>
          <li><a href="index.php?page=contact">Khách hàng doanh nghiệp (B2B)</a></li>
        </ul>
      </div>

      <!-- Column 4: Connect -->
      <div class="col-md-3 mb-4">
        <h5 class="cps-footer-title">Kết nối với chúng tôi</h5>
        <div class="cps-social-links mb-4">
          <?php if ($fbLink = getSetting('footer.social_facebook')): ?>
            <a href="<?php echo htmlspecialchars($fbLink); ?>" target="_blank" class="text-primary"><i class="bi bi-facebook fs-3"></i></a>
          <?php endif; ?>
          <?php if ($igLink = getSetting('footer.social_instagram')): ?>
            <a href="<?php echo htmlspecialchars($igLink); ?>" target="_blank" class="text-danger"><i class="bi bi-instagram fs-3"></i></a>
          <?php endif; ?>
          <?php if ($twLink = getSetting('footer.social_twitter')): ?>
            <a href="<?php echo htmlspecialchars($twLink); ?>" target="_blank" class="text-info"><i class="bi bi-twitter fs-3"></i></a>
          <?php endif; ?>
        </div>
        
        <h5 class="cps-footer-title">Website thành viên</h5>
        <div class="cps-member-sites">
          <p class="mb-1 text-muted"><small>Hệ thống bảo hành sửa chữa</small></p>
          <strong>Điện Thoại Vui</strong>
        </div>
      </div>
    </div>
    
    <div class="cps-footer-bottom">
      <div class="row align-items-center">
        <div class="col-md-12 text-center text-muted">
          <p class="mb-1">© <?php echo date('Y'); ?> <?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?>. Mọi bản quyền thuộc về công ty.</p>
          <p class="mb-0 small"><?php echo nl2br(htmlspecialchars(getSetting('footer.about_text', 'Hệ thống bán lẻ điện thoại, máy tính, phụ kiện chính hãng.'))); ?></p>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script src="assets/javascript/header.js"></script>
<script src="assets/javascript/product_detail.js"></script>
<script src="assets/javascript/cart.js"></script>



