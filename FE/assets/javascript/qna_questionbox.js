document.querySelectorAll('.qna-question-container').forEach(container => {
  container.addEventListener('click', () => {
    const answer = container.nextElementSibling;
    const icon = container.querySelector('.qna-toggle-icon');

    if (answer.style.display === 'block') {
      answer.style.display = 'none';
      icon.textContent = '+';
    } else {
      answer.style.display = 'block';
      icon.textContent = '−';
    }
  });
});
