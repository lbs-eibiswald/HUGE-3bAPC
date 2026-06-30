function toggleVisibility(divID) {
    const div = document.getElementById(divID);
    div.style.display = (div.style.display === 'none' || div.style.display === '') ? 'flex' : 'none';
}

function openCreateProduct() {
    const container = document.getElementById('create-product-container');
    const form = container.querySelector('form');

    form.action = form.dataset.createUrl;
    form.querySelector('[name="productName"]').value = '';
    form.querySelector('[name="productDescription"]').value = '';
    form.querySelector('[name="productPrice"]').value = '';
    form.querySelector('[name="productAmount"]').value = '';
    form.querySelector('[name="categorySelection"]').selectedIndex = 0;
    form.querySelector('#edit-product-id').value = '';
    form.querySelector('[type="submit"]').textContent = 'Create Product';

    container.style.display = (container.style.display === 'none' || container.style.display === '') ? 'flex' : 'none';
}

function editProduct(btn) {
    const container = document.getElementById('create-product-container');
    const form = container.querySelector('form');

    form.action = form.dataset.editUrl;
    form.querySelector('[name="productName"]').value = btn.dataset.name;
    form.querySelector('[name="productDescription"]').value = btn.dataset.description;
    form.querySelector('[name="productPrice"]').value = btn.dataset.price;
    form.querySelector('[name="productAmount"]').value = btn.dataset.amount;
    form.querySelector('[name="categorySelection"]').value = btn.dataset.categoryId;
    form.querySelector('#edit-product-id').value = btn.dataset.id;
    form.querySelector('[type="submit"]').textContent = 'Save Changes';

    container.style.display = 'flex';
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

function toggleCheckout() {
    document.getElementById('view-container').className = 'mode-checkout';
}

function backToCart() {
    document.getElementById('view-container').className = 'mode-cart';
    document.querySelectorAll('.product[data-in-cart="false"]').forEach(p => p.style.display = 'none');
}