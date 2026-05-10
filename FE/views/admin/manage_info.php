<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-gear"></i> Site Information</h2>
                <p>Manage your website settings and information</p>
            </div>
            
            <div class="content-card">
                <div class="container-xl">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs nav-tabs-alt" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#tab-general" class="nav-link active" data-bs-toggle="tab">
                                        <i class="ti ti-settings me-2"></i>General
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-header" class="nav-link" data-bs-toggle="tab">
                                        <i class="ti ti-layout-navbar me-2"></i>Header
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-home" class="nav-link" data-bs-toggle="tab">
                                        <i class="ti ti-home me-2"></i>Home Page
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-contact" class="nav-link" data-bs-toggle="tab">
                                        <i class="ti ti-phone me-2"></i>Contact
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-footer" class="nav-link" data-bs-toggle="tab">
                                        <i class="ti ti-layout-bottombar me-2"></i>Footer
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tab-shop" class="nav-link" data-bs-toggle="tab">
                                        <i class="ti ti-shopping-cart me-2"></i>Shop
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Tab General -->
                                <div class="tab-pane active show" id="tab-general">
                                    <form id="form-general" data-group="general">
                                        <div class="mb-3">
                                            <label class="form-label required">Website Name</label>
                                            <input type="text" class="form-control" name="site_name" id="general_site_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tagline / Slogan</label>
                                            <input type="text" class="form-control" name="site_tagline" id="general_site_tagline">
                                            <small class="form-hint">Short description or motto about the website</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Logo Image</label>
                                            <input type="file" class="form-control" id="general_site_logo_file" accept="image/*">
                                            <small class="form-hint">Upload new logo image (will replace existing logo.png)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Site Description</label>
                                            <textarea class="form-control" name="site_description" id="general_site_description" rows="2"></textarea>
                                            <small class="form-hint">Short description about your website</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>

                                <!-- Tab Header -->
                                <div class="tab-pane" id="tab-header">
                                    <form id="form-header" data-group="header">
                                        <div class="mb-3">
                                            <label class="form-label">Announcement Bar Text</label>
                                            <input type="text" class="form-control" name="announcement_bar_text" id="header_announcement_bar_text">
                                            <small class="form-hint">Text displayed in top announcement bar</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Enable Announcement Bar</label>
                                            <select class="form-select" name="announcement_bar_enabled" id="header_announcement_bar_enabled">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Header Phone Number</label>
                                            <input type="text" class="form-control" name="phone_number" id="header_phone_number">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Header Email</label>
                                            <input type="email" class="form-control" name="email" id="header_email">
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>

                                <!-- Tab Home -->
                                <div class="tab-pane" id="tab-home">
                                    <form id="form-home" data-group="home">
                                        <h4 class="mb-3">Hero Section</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Hero Title</label>
                                            <input type="text" class="form-control" name="hero_title" id="home_hero_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hero Subtitle</label>
                                            <input type="text" class="form-control" name="hero_subtitle" id="home_hero_subtitle">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hero Button Text</label>
                                            <input type="text" class="form-control" name="hero_button_text" id="home_hero_button_text">
                                        </div>
                                        
                                        <hr class="my-4">
                                        <h4 class="mb-3">Featured Section</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Featured Section Title</label>
                                            <input type="text" class="form-control" name="featured_section_title" id="home_featured_section_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Featured Section Subtitle</label>
                                            <input type="text" class="form-control" name="featured_section_subtitle" id="home_featured_section_subtitle">
                                        </div>

                                        <hr class="my-4">
                                        <h4 class="mb-3">Banner 1 (Frame 2)</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 1 Title</label>
                                            <input type="text" class="form-control" name="banner_1_title" id="home_banner_1_title">
                                            <small class="form-hint">Main title for second section</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 1 Subtitle</label>
                                            <input type="text" class="form-control" name="banner_1_subtitle" id="home_banner_1_subtitle">
                                            <small class="form-hint">Subtitle/label for second section</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 1 Image</label>
                                            <input type="file" class="form-control" id="home_banner_1_image_file" accept="image/*">
                                            <small class="form-hint">Upload new banner 1 image</small>
                                        </div>

                                        <hr class="my-4">
                                        <h4 class="mb-3">Banner 2 (Frame 3)</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 2 Title</label>
                                            <input type="text" class="form-control" name="banner_2_title" id="home_banner_2_title">
                                            <small class="form-hint">Main title for third section</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 2 Subtitle</label>
                                            <input type="text" class="form-control" name="banner_2_subtitle" id="home_banner_2_subtitle">
                                            <small class="form-hint">Subtitle/label for third section</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Banner 2 Image</label>
                                            <input type="file" class="form-control" id="home_banner_2_image_file" accept="image/*">
                                            <small class="form-hint">Upload new banner 2 image</small>
                                        </div>
                                        
                                        <hr class="my-4">
                                        <h4 class="mb-3">Why Choose Us Section</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Section Title</label>
                                            <input type="text" class="form-control" name="why_choose_title" id="home_why_choose_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Section Subtitle</label>
                                            <input type="text" class="form-control" name="why_choose_subtitle" id="home_why_choose_subtitle">
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Feature 1</h5>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon Class</label>
                                                    <input type="text" class="form-control" name="feature_1_icon" id="home_feature_1_icon">
                                                    <small class="form-hint">Tabler icon class (e.g., ti-truck)</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="feature_1_title" id="home_feature_1_title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" class="form-control" name="feature_1_description" id="home_feature_1_description">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Feature 2</h5>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon Class</label>
                                                    <input type="text" class="form-control" name="feature_2_icon" id="home_feature_2_icon">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="feature_2_title" id="home_feature_2_title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" class="form-control" name="feature_2_description" id="home_feature_2_description">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Feature 3</h5>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon Class</label>
                                                    <input type="text" class="form-control" name="feature_3_icon" id="home_feature_3_icon">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="feature_3_title" id="home_feature_3_title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" class="form-control" name="feature_3_description" id="home_feature_3_description">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5 class="mb-3">Feature 4</h5>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon Class</label>
                                                    <input type="text" class="form-control" name="feature_4_icon" id="home_feature_4_icon">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="feature_4_title" id="home_feature_4_title">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" class="form-control" name="feature_4_description" id="home_feature_4_description">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>

                                <!-- Tab Contact -->
                                <div class="tab-pane" id="tab-contact">
                                    <form id="form-contact" data-group="contact">
                                        <div class="mb-3">
                                            <label class="form-label">Page Title</label>
                                            <input type="text" class="form-control" name="page_title" id="contact_page_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Page Subtitle</label>
                                            <textarea class="form-control" name="page_subtitle" id="contact_page_subtitle" rows="2"></textarea>
                                        </div>
                                        <hr class="my-4">
                                        <h4 class="mb-3">Contact Information</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" name="phone" id="contact_phone">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="contact_email">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" id="contact_address" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Working Hours</label>
                                            <textarea class="form-control" name="working_hours" id="contact_working_hours" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Google Maps Embed Code</label>
                                            <textarea class="form-control" name="map_embed" id="contact_map_embed" rows="4"></textarea>
                                            <small class="form-hint">Iframe embed code from Google Maps</small>
                                        </div>
                                        <hr class="my-4">
                                        <h4 class="mb-3">Contact Form</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Form Title</label>
                                            <input type="text" class="form-control" name="form_title" id="contact_form_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Success Message</label>
                                            <textarea class="form-control" name="success_message" id="contact_success_message" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>

                                <!-- Tab Footer -->
                                <div class="tab-pane" id="tab-footer">
                                    <form id="form-footer" data-group="footer">
                                        <div class="mb-3">
                                            <label class="form-label">About Text</label>
                                            <textarea class="form-control" name="about_text" id="footer_about_text" rows="3"></textarea>
                                            <small class="form-hint">Short description about the company in footer</small>
                                        </div>
                                        <hr class="my-4">
                                        <h4 class="mb-3">Newsletter</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Newsletter Title</label>
                                            <input type="text" class="form-control" name="newsletter_title" id="footer_newsletter_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Newsletter Subtitle</label>
                                            <input type="text" class="form-control" name="newsletter_subtitle" id="footer_newsletter_subtitle">
                                        </div>
                                        <hr class="my-4">
                                        <h4 class="mb-3">Social Media Links</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Facebook URL</label>
                                            <input type="url" class="form-control" name="social_facebook" id="footer_social_facebook">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Instagram URL</label>
                                            <input type="url" class="form-control" name="social_instagram" id="footer_social_instagram">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Twitter URL</label>
                                            <input type="url" class="form-control" name="social_twitter" id="footer_social_twitter">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">YouTube URL</label>
                                            <input type="url" class="form-control" name="social_youtube" id="footer_social_youtube">
                                        </div>
                                        <hr class="my-4">
                                        <div class="mb-3">
                                            <label class="form-label">Copyright Text</label>
                                            <input type="text" class="form-control" name="copyright_text" id="footer_copyright_text">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Show Payment Methods</label>
                                            <select class="form-select" name="payment_methods_enabled" id="footer_payment_methods_enabled">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>

                                <!-- Tab Shop -->
                                <div class="tab-pane" id="tab-shop">
                                    <form id="form-shop" data-group="shop">
                                        <div class="mb-3">
                                            <label class="form-label">Page Title</label>
                                            <input type="text" class="form-control" name="page_title" id="shop_page_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Page Subtitle</label>
                                            <input type="text" class="form-control" name="page_subtitle" id="shop_page_subtitle">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Filter Title</label>
                                            <input type="text" class="form-control" name="filter_title" id="shop_filter_title">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sort Label</label>
                                            <input type="text" class="form-control" name="sort_label" id="shop_sort_label">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">No Products Message</label>
                                            <input type="text" class="form-control" name="no_products_message" id="shop_no_products_message">
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <script>
        // Set base path for AJAX calls
        window.BASE_PATH = '<?php echo dirname($_SERVER['PHP_SELF']) === '/' ? '' : dirname($_SERVER['PHP_SELF']); ?>';
    </script>
    <script src="assets/javascript/admin_manage_info.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>