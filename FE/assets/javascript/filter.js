document.addEventListener('DOMContentLoaded', function() {
    // Đếm số lượng filter đang active
    function countActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        let count = 0;
        
        // Đếm brand filters
        const brands = urlParams.getAll('brand[]');
        count += brands.length;
        
        // Đếm category filter
        if (urlParams.get('category')) count++;
        
        // Đếm storage filters
        const storages = urlParams.getAll('storage[]');
        count += storages.length;
        
        // Đếm price filters
        if (urlParams.get('price_min') || urlParams.get('price_max')) count++;
        
        return count;
    }
    
    // Hiển thị badge số lượng filter
    const filterButton = document.querySelector('[data-bs-target="#filterSidebar"]');
    const activeCount = countActiveFilters();
    
    if (activeCount > 0 && filterButton) {
        filterButton.innerHTML = `
            <i class="bi bi-funnel-fill"></i> Filter 
            <span class="badge bg-primary rounded-pill">${activeCount}</span>
        `;
    }
    
    // Auto-close offcanvas sau khi apply filter (optional)
    const filterForm = document.getElementById('filterForm');
    const offcanvasElement = document.getElementById('filterSidebar');
    
    // Uncomment dòng này nếu muốn tự động đóng sidebar sau khi submit
    // filterForm?.addEventListener('submit', function() {
    //     const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    //     offcanvas?.hide();
    // });
});
