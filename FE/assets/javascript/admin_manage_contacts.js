// admin_manage_contacts.js — Full CRUD with pagination, search, status filter

let currentPage = 1;
let currentLimit = 10;
let deleteTargetId = null;

const tableBody   = document.querySelector("#contactTable tbody");
const viewModal   = new bootstrap.Modal(document.getElementById("viewContactModal"));
const deleteModal  = new bootstrap.Modal(document.getElementById("deleteContactModal"));

// ======================== INIT ========================
document.addEventListener("DOMContentLoaded", () => {
    fetchContacts();

    // Search with debounce
    let searchTimer;
    document.getElementById("contactSearch").addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { currentPage = 1; fetchContacts(); }, 400);
    });

    // Status filter
    document.getElementById("contactStatusFilter").addEventListener("change", () => {
        currentPage = 1; fetchContacts();
    });

    // Per page
    document.getElementById("contactPerPage").addEventListener("change", (e) => {
        currentLimit = parseInt(e.target.value);
        currentPage = 1;
        fetchContacts();
    });

    // Confirm delete
    document.getElementById("confirmDeleteBtn").addEventListener("click", async () => {
        if (!deleteTargetId) return;
        const fd = new FormData();
        fd.append("id", deleteTargetId);
        try {
            const res = await fetch("controllers/ContactController.php?action=delete", { method: "POST", body: fd });
            const r = await res.json();
            if (r.success) fetchContacts();
            else alert(r.message);
        } catch (e) { console.error(e); }
        deleteModal.hide();
        deleteTargetId = null;
    });
});

// ======================== FETCH ========================
async function fetchContacts() {
    const search = document.getElementById("contactSearch").value.trim();
    const status = document.getElementById("contactStatusFilter").value;

    const params = new URLSearchParams({
        action: 'list',
        page: currentPage,
        limit: currentLimit,
    });
    if (search) params.set('search', search);
    if (status) params.set('status', status);

    try {
        const res = await fetch(`controllers/ContactController.php?${params}`);
        const result = await res.json();

        if (result.success) {
            renderTable(result.data.items, result.data);
            renderPagination(result.data);
            document.getElementById("contactTotal").textContent = `${result.data.total} contacts`;
            document.getElementById("contactPaginationInfo").textContent =
                `Showing ${result.data.items.length} of ${result.data.total} (Page ${result.data.page}/${result.data.total_pages || 1})`;
        }
    } catch (err) {
        console.error("Failed to fetch contacts:", err);
    }
}

// ======================== RENDER TABLE ========================
function renderTable(items, meta) {
    tableBody.innerHTML = "";

    if (!items || items.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No contacts found.</td></tr>';
        return;
    }

    const startNum = (meta.page - 1) * meta.limit;

    items.forEach((item, i) => {
        const statusBadge = {
            unread:  '<span class="badge bg-warning text-dark">Unread</span>',
            read:    '<span class="badge bg-info">Read</span>',
            replied: '<span class="badge bg-success">Replied</span>',
        };

        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${startNum + i + 1}</td>
            <td>${escapeHtml(item.customer_name)}</td>
            <td><small>${escapeHtml(item.customer_email)}</small></td>
            <td><small class="text-truncate d-inline-block" style="max-width:150px">${escapeHtml(item.subject || '(no subject)')}</small></td>
            <td><small>${formatDate(item.created_at)}</small></td>
            <td>
                <select class="form-select form-select-sm contact-status" data-id="${item.id}" style="width:110px">
                    <option value="unread"  ${item.status === "unread" ? "selected" : ""}>Unread</option>
                    <option value="read"    ${item.status === "read" ? "selected" : ""}>Read</option>
                    <option value="replied" ${item.status === "replied" ? "selected" : ""}>Replied</option>
                </select>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary btn-view me-1" data-id="${item.id}" title="View">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${item.id}" title="Delete">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// ======================== RENDER PAGINATION ========================
function renderPagination(meta) {
    const container = document.getElementById("contactPagination");
    container.innerHTML = "";
    if (meta.total_pages <= 1) return;

    // Prev
    const prev = document.createElement("li");
    prev.className = `page-item ${meta.page <= 1 ? 'disabled' : ''}`;
    prev.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
    prev.addEventListener("click", (e) => { e.preventDefault(); if (meta.page > 1) { currentPage = meta.page - 1; fetchContacts(); } });
    container.appendChild(prev);

    // Pages
    const start = Math.max(1, meta.page - 2);
    const end = Math.min(meta.total_pages, meta.page + 2);
    for (let p = start; p <= end; p++) {
        const li = document.createElement("li");
        li.className = `page-item ${p === meta.page ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#">${p}</a>`;
        li.addEventListener("click", (e) => { e.preventDefault(); currentPage = p; fetchContacts(); });
        container.appendChild(li);
    }

    // Next
    const next = document.createElement("li");
    next.className = `page-item ${meta.page >= meta.total_pages ? 'disabled' : ''}`;
    next.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
    next.addEventListener("click", (e) => { e.preventDefault(); if (meta.page < meta.total_pages) { currentPage = meta.page + 1; fetchContacts(); } });
    container.appendChild(next);
}

// ======================== TABLE EVENT DELEGATION ========================
tableBody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const id = btn.dataset.id;

    // View
    if (btn.classList.contains("btn-view")) {
        try {
            const res = await fetch(`controllers/ContactController.php?action=get&id=${id}`);
            const r = await res.json();
            if (r.success) {
                document.getElementById("contactViewName").textContent  = r.data.customer_name;
                document.getElementById("contactViewEmail").textContent = r.data.customer_email;
                document.getElementById("contactViewDate").textContent  = formatDate(r.data.created_at);
                document.getElementById("contactSubject").textContent   = r.data.subject || '(no subject)';
                document.getElementById("contactMessage").textContent   = r.data.message;
                viewModal.show();
            }
        } catch (err) { console.error(err); }
    }

    // Delete
    if (btn.classList.contains("btn-delete")) {
        deleteTargetId = id;
        deleteModal.show();
    }
});

// Status change
tableBody.addEventListener("change", async (e) => {
    if (e.target.classList.contains("contact-status")) {
        const id = e.target.dataset.id;
        const status = e.target.value;
        const fd = new FormData();
        fd.append("id", id);
        fd.append("status", status);
        try {
            const res = await fetch("controllers/ContactController.php?action=updateStatus", { method: "POST", body: fd });
            const r = await res.json();
            if (!r.success) alert(r.message || "Failed to update");
        } catch (err) { console.error(err); }
    }
});

// ======================== HELPERS ========================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('vi-VN') + ' ' + d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
}
