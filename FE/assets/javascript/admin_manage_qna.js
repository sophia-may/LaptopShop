const qnaTableBody = document.querySelector("#qnaTable tbody");
const qnaForm = document.getElementById("qnaForm");
const qnaModal = new bootstrap.Modal(document.getElementById("qnaModal"));
const modalTitle = document.getElementById("modalTitle");
const addNewBtn = document.getElementById("addNewBtn");

// Fetch all Q&A items
async function fetchQna() {
  try {
    const res = await fetch('controllers/QnaController.php?action=list');
    const result = await res.json();
    if (result.success) {
      renderTable(result.data);
    } else {
      console.error(result.message);
    }
  } catch (err) {
    console.error("Failed to fetch Q&A data:", err);
  }
}

// Render table
function renderTable(qnaData) {
  qnaTableBody.innerHTML = "";
  qnaData.forEach((item, index) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${index + 1}</td>
      <td>${item.question}</td>
      <td>${item.answer}</td>
      <td>
        <button class="btn btn-sm btn-warning btn-edit" data-id="${item.id}">Edit</button>
        <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}">Delete</button>
      </td>
    `;
    qnaTableBody.appendChild(row);
  });
}

// Add new Q&A
addNewBtn.addEventListener("click", () => {
  qnaForm.reset();
  document.getElementById("qnaId").value = "";
  modalTitle.textContent = "Add Q&A";
});

// Submit form (Add/Edit)
qnaForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const id = document.getElementById("qnaId").value;
  const question = document.getElementById("qnaQuestion").value;
  const answer = document.getElementById("qnaAnswer").value;

  const action = id ? 'update' : 'create';
  const formData = new FormData();
  formData.append('question', question);
  formData.append('answer', answer);
  if (id) formData.append('id', id);

  try {
    const res = await fetch(`controllers/QnaController.php?action=${action}`, {
      method: 'POST',
      body: formData
    });
    const result = await res.json();

    if (result.success) {
      fetchQna(); // Refresh table
      qnaModal.hide();
    } else {
      alert(result.message || 'Failed to save Q&A');
    }
  } catch (err) {
    console.error("Failed to save Q&A:", err);
  }
});

// Edit/Delete buttons
qnaTableBody.addEventListener("click", async (e) => {
  const id = e.target.dataset.id;

  if (e.target.classList.contains("btn-edit")) {
    try {
      const res = await fetch(`controllers/QnaController.php?action=get&id=${id}`);
      const result = await res.json();
      if (result.success) {
        document.getElementById("qnaQuestion").value = result.data.question;
        document.getElementById("qnaAnswer").value = result.data.answer;
        document.getElementById("qnaId").value = result.data.id;
        modalTitle.textContent = "Edit Q&A";
        qnaModal.show();
      } else {
        alert(result.message || "Failed to fetch Q&A");
      }
    } catch (err) {
      console.error("Failed to fetch Q&A:", err);
    }
  }

  if (e.target.classList.contains("btn-delete")) {
    if (confirm("Are you sure you want to delete this Q&A?")) {
      try {
        const formData = new FormData();
        formData.append('id', id);

        const res = await fetch(`controllers/QnaController.php?action=delete`, {
          method: 'POST',
          body: formData
        });
        const result = await res.json();
        if (result.success) fetchQna();
        else alert(result.message || "Failed to delete Q&A");
      } catch (err) {
        console.error("Failed to delete Q&A:", err);
      }
    }
  }
});

// Initial fetch
fetchQna();
