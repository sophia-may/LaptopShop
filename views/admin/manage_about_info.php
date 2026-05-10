<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-info-circle"></i> About Page Content</h2>
                <p>Manage About page information and content</p>
            </div>

            <div class="content-card">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit About Page Content</h3>
                    </div>
                    <div class="card-body">
                            <form id="form-about" data-group="about">
                                <div class="mb-3">
                                    <label class="form-label">Page Title</label>
                                    <input type="text" class="form-control" name="page_title" id="about_page_title">
                                    <small class="form-hint">Main title displayed at the top of About page</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Hero Subtitle</label>
                                    <input type="text" class="form-control" name="hero_subtitle" id="about_hero_subtitle">
                                    <small class="form-hint">Subtitle below the main title</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Introduction Section</h4>

                                <div class="mb-3">
                                    <label class="form-label">Introduction Title</label>
                                    <input type="text" class="form-control" name="intro_title" id="about_intro_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Introduction Text</label>
                                    <textarea class="form-control" name="intro" id="about_intro" rows="4"></textarea>
                                    <small class="form-hint">General introduction about the company</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Mission & Vision</h4>

                                <div class="mb-3">
                                    <label class="form-label">Mission Title</label>
                                    <input type="text" class="form-control" name="mission_title" id="about_mission_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mission Statement</label>
                                    <textarea class="form-control" name="mission" id="about_mission" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Vision Title</label>
                                    <input type="text" class="form-control" name="vision_title" id="about_vision_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Vision Statement</label>
                                    <textarea class="form-control" name="vision" id="about_vision" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Values Title</label>
                                    <input type="text" class="form-control" name="values_title" id="about_values_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Core Values</label>
                                    <textarea class="form-control" name="values" id="about_values" rows="4"></textarea>
                                    <small class="form-hint">Company core values</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Statistics</h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Customers Count</label>
                                            <input type="number" class="form-control" name="stats_customers" id="about_stats_customers">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Customers Label</label>
                                            <input type="text" class="form-control" name="stats_customers_label" id="about_stats_customers_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Products Count</label>
                                            <input type="number" class="form-control" name="stats_products" id="about_stats_products">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Products Label</label>
                                            <input type="text" class="form-control" name="stats_products_label" id="about_stats_products_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Years Count</label>
                                            <input type="number" class="form-control" name="stats_years" id="about_stats_years">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Years Label</label>
                                            <input type="text" class="form-control" name="stats_years_label" id="about_stats_years_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Reviews Count</label>
                                            <input type="number" class="form-control" name="stats_reviews" id="about_stats_reviews">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Reviews Label</label>
                                            <input type="text" class="form-control" name="stats_reviews_label" id="about_stats_reviews_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>

<script src="assets/javascript/admin_manage_about.js"></script>

<?php include 'views/layouts/admin_footer.php'; ?>