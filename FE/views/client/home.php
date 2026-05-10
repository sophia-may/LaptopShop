<?php 
require_once 'helpers/settings_helper.php';
include 'views/layouts/header.php'; 
?>

<main class="home-page-scroll" style="background-color: #f4f6f8; min-height: 100vh; padding-bottom: 20px; overflow-x: hidden;">
    <section class="home-hero container pt-4 pb-4">
        <div class="row gx-4">
            <aside class="col-lg-3 d-none d-lg-block left-col">
                <div class="shadow-bottom-50 flex flex-col overflow-x-hidden rounded-xl bg-white py-2 text-neutral-800 sidebar-card categories">
                    <?php 
                    $leftCats = [
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-mobile.svg', 'parts' => [['text'=>'Điện thoại', 'href'=>'index.php?page=shop'], ['text'=>'Tablet', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-laptop.svg', 'parts' => [['text'=>'Laptop', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-audio-2.svg', 'parts' => [['text'=>'Âm thanh', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-watch.svg', 'parts' => [['text'=>'Đồng hồ', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-home-appliances.svg', 'parts' => [['text'=>'Đồ gia dụng', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-accessories.svg', 'parts' => [['text'=>'Phụ kiện', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-pc.svg', 'parts' => [['text'=>'PC', 'href'=>'index.php?page=shop'], ['text'=>'Màn hình', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tv.svg', 'parts' => [['text'=>'Tivi', 'href'=>'index.php?page=shop']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-trade-in.svg', 'parts' => [['text'=>'Thu cũ đổi mới', 'href'=>'#']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-used-goods.svg', 'parts' => [['text'=>'Hàng cũ', 'href'=>'#']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-promotions.svg', 'parts' => [['text'=>'Khuyến mãi', 'href'=>'#']]],
                        ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tech-news.svg', 'parts' => [['text'=>'Tin công nghệ', 'href'=>'#']]],
                        ['src' => 'assets/img/qna.svg', 'parts' => [['text'=>'Hỏi đáp', 'href'=>'http://localhost/LaptopWeb/FE/index.php?page=qna']]]
                    ];
                    
                    foreach ($leftCats as $c): ?>
                    <div class="group flex h-10 cursor-pointer items-center px-3 hover:bg-neutral-100 d-flex align-items-center category-item" onclick="if(event.target.tagName !== 'A') window.location.href='index.php?page=shop'">
                        <img alt="Category" loading="lazy" width="28" height="28" decoding="async" class="mr-2" src="<?= $c['src'] ?>" />
                        <div class="d-flex align-items-center parts-wrapper w-100">
                            <span class="text-xs font-semibold text-truncate d-block w-100">
                                <?php 
                                $total = count($c['parts']);
                                foreach ($c['parts'] as $i => $p): ?>
                                    <a class="hover-text-primary text-dark text-decoration-none" href="<?= $p['href'] ?>"><?= $p['text'] ?></a><?= $i < $total - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
                            </span>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-secondary" style="font-size: 12px;"></i>
                    </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <main class="col-lg-6">
                <div class="hero-card mb-3 p-4">
                    <div class="hero-inner d-flex align-items-center">
                        <div class="hero-content flex-grow-1">
                            <div class="hot-tags d-flex gap-2 mb-2">
                                <span class="tag">MACBOOK NEO</span>
                                <span class="tag">GALAXY S26 ULTRA</span>
                                <span class="tag">OPPO FIND X9</span>
                            </div>
                            <h2 class="hero-title fw-bold text-dark">TECNO SPARK Go 3</h2>
                            <p class="hero-sub text-muted">Bền Mượt 4 Năm • 5000mAh • Sạc nhanh 15W</p>

                            <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                                <img src="assets/img/shop_banner.jpg" alt="TECNO" style="max-width: 300px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                                <div class="price-box">
                                    <div class="price-note text-muted small">Giá chỉ từ</div>
                                    <div class="price">3.49 Triệu</div>
                                    
                                </div>
                                
                                <a href="index.php?page=shop" class="btn btn-danger px-4 py-2 fw-bold" style="border-radius: 8px;">MUA NGAY</a>
                                
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row small-cards g-3">
                    <div class="col-md-4 col-4">
                        <div class="card small-card p-2 text-center h-100 border-0" style="background-image: url('assets/img/products/MacBook Air M2.jpg'); background-size: cover; background-position: center; border-radius: 12px; position: relative; min-height: 140px; display: flex; align-items: center; justify-content: center;">
                            <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.4); border-radius: 12px;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="fw-bold text-white" style="font-size: 13px;">MacBook Pro</div>
                                <small class="text-white-50" style="font-size: 11px;">Nay với M5</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="card small-card p-2 text-center h-100 border-0" style="background-image: url('assets/img/products/Galaxy A17.jpg'); background-size: cover; background-position: center; border-radius: 12px; position: relative; min-height: 140px; display: flex; align-items: center; justify-content: center;">
                            <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.4); border-radius: 12px;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="fw-bold text-white" style="font-size: 13px;">Galaxy A17 5G</div>
                                <small class="text-white-50" style="font-size: 11px;">Ưu đãi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-4">
                        <div class="card small-card p-2 text-center h-100 border-0" style="background-image: url('assets/img/products/Asus ROG Strix G15.jpg'); background-size: cover; background-position: center; border-radius: 12px; position: relative; min-height: 140px; display: flex; align-items: center; justify-content: center;">
                            <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.4); border-radius: 12px;"></div>
                            <div style="position: relative; z-index: 1;">
                                <div class="fw-bold text-white" style="font-size: 13px;">Mua Laptop Online</div>
                                <small class="text-white-50" style="font-size: 11px;">Giảm thêm 5 Triệu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <aside class="col-lg-3 d-none d-lg-block">
                <div class="right-card p-3">
                    <div class="user-panel mb-3 pb-3 border-bottom">
                        <?php if ($isLoggedIn): ?>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3"> 
                                <img src="assets/img/default-avatar.png" alt="user" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($username) ?>&background=random'"/> 
                            </div>
                            <div>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($username) ?></div>
                                <div class="text-muted small">S-Member</div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-center">
                            <p class="text-muted small mb-2">Đăng nhập để nhận ưu đãi</p>
                            <a href="index.php?page=login_signup" class="btn btn-sm btn-outline-danger w-100">Đăng nhập ngay</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <ul class="list-unstyled small-links mb-0" style="font-size: 13px;">
                        <li class="py-2"><i class="bi bi-gift me-2 text-danger"></i>Ưu đãi cho giáo dục</li>
                        <li class="py-2"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Thu cũ lên đời</li>
                        <li class="py-2"><i class="bi bi-shield-check me-2 text-success"></i>Bảo hành mở rộng</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <img src="assets/img/banner right.jpg" class="img-fluid rounded" alt="Banner right">
                </div>
            </aside>
        </div>

        <div class="promo-strip mt-4 p-3 text-center rounded-3 shadow-sm border border-danger border-opacity-10">
            <strong class="text-danger"><i class="bi bi-fire me-2"></i>Say Hi! S-STUDENT S-TEACHER — Trợ giá lên đến 5 Triệu</strong>
        </div>
    </section>
</main>

<?php include 'views/layouts/footer.php'; ?>
