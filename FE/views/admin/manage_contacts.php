<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

      <div class="content-header mb-4">
        <h2><i class="bi bi-envelope"></i> Manage Contacts</h2>
        <p>View and manage contact messages submitted by users.</p>
      </div>

      <div class="content-card">
        <!-- Filter Bar -->
        <div class="row mb-3 g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small text-muted">Search</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="contactSearch" placeholder="Name, email or subject...">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small text-muted">Status Filter</label>
            <select class="form-select" id="contactStatusFilter">
              <option value="">All Status</option>
              <option value="unread">Unread</option>
              <option value="read">Read</option>
              <option value="replied">Replied</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small text-muted">Per Page</label>
            <select class="form-select" id="contactPerPage">
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
          </div>
          <div class="col-md-3 text-end">
            <span class="badge bg-secondary" id="contactTotal">0 contacts</span>
          </div>
        </div>

      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap" id="contactTable">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="15%">Name</th>
              <th width="20%">Email</th>
              <th width="15%">Subject</th>
              <th width="15%">Created At</th>
              <th width="12%">Status</th>
              <th width="18%">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small" id="contactPaginationInfo">Showing 0 of 0</div>
        <nav>
          <ul class="pagination pagination-sm mb-0" id="contactPagination"></ul>
        </nav>
      </div>

      </div>
    </div>
  </div>

  <!-- View Contact Modal -->
  <div class="modal modal-blur fade" id="viewContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-envelope-open"></i> Contact Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><strong>From:</strong> <span id="contactViewName"></span></div>
          <div class="mb-2"><strong>Email:</strong> <span id="contactViewEmail"></span></div>
          <div class="mb-2"><strong>Date:</strong> <span id="contactViewDate"></span></div>
          <hr>
          <strong>Subject:</strong>
          <p id="contactSubject" class="fw-bold"></p>
          <strong>Message:</strong>
          <p id="contactMessage" class="bg-light p-3 rounded"></p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirm Modal -->
  <div class="modal fade" id="deleteContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center pt-4">
          <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
          <h5 class="mt-2">Delete Contact?</h5>
          <p class="text-muted small">This action cannot be undone.</p>
        </div>
        <div class="modal-footer justify-content-center border-0 pt-0">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="assets/javascript/admin_manage_contacts.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
