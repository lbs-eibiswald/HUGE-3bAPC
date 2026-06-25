function toggleVisibility(divID) {
    const div = document.getElementById(divID);
    div.style.display = (div.style.display === 'none' || div.style.display === '') ? 'flex' : 'none';
}

function toggleView() {
    const container = document.getElementById('view-container');
    const btn = document.getElementById('btn-toggle-view');
    const isShop = container.className === 'mode-shop';

    if (isShop) {
        container.className = 'mode-cart';
        btn.textContent = 'Back to Shop';
        document.querySelectorAll('.product[data-in-cart="false"]').forEach(p => p.style.display = 'none');
    } else {
        container.className = 'mode-shop';
        btn.textContent = 'View Shopping Cart';
        document.querySelectorAll('.product').forEach(p => p.style.display = '');
    }
}