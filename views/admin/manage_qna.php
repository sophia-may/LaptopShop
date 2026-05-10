<?php include 'views/layouts/admin_header.php'; ?>

  <div class="admin-layout">
    <?php include 'views/layouts/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="admin-content">
      <div class="content-header">
        <h2><i class="bi bi-question-circle"></i> Manage Q&A</h2>
        <p>Add, remove, or edit questions and answers shown on the Q&A page.</p>
      </div>

      <div class="content-card">
        <div class="d-flex justify-content-end mb-3">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qnaModal" id="addNewBtn">
            Add New Q&A
          </button>
        </div>

      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap" id="qnaTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Question</th>
              <th>Answer</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      </div>
    </main>
  </div>

  <!-- Modal for Add/Edit Q&A -->
  <div class="modal modal-blur fade" id="qnaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="qnaForm">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Add Q&A</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="qnaId">
            <div class="mb-3">
              <label for="qnaQuestion" class="form-label">Question</label>
              <input type="text" id="qnaQuestion" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="qnaAnswer" class="form-label">Answer</label>
              <textarea id="qnaAnswer" class="form-control" rows="4" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta10/dist/js/tabler.min.js"></script>
  <script src="assets/javascript/admin_manage_qna.js"></script>
  <?php include 'views/layouts/admin_footer.php'; ?>
