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

    document.getElementById('existing-images-section').style.display = 'none';
    document.getElementById('existing-images-list').innerHTML = '';

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

    const images = JSON.parse(btn.dataset.images || '[]');
    renderExistingImages(images, form.dataset.imageBaseUrl, btn.dataset.id, form.dataset.deleteImageUrl);

    container.style.display = 'flex';
}

function renderExistingImages(images, baseUrl, productId, deleteUrl) {
    const section = document.getElementById('existing-images-section');
    const list = document.getElementById('existing-images-list');
    list.innerHTML = '';

    if (!images || images.length === 0) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';

    images.forEach(img => {
        const item = document.createElement('div');
        item.style.cssText = 'display:inline-flex; flex-direction:column; align-items:center; gap:4px; margin-right:8px;';

        const image = document.createElement('img');
        image.src = baseUrl + productId + '/' + img.name;
        image.style.cssText = 'max-width:80px; max-height:60px; object-fit:cover;';

        const form = document.createElement('form');
        form.action = deleteUrl;
        form.method = 'post';

        const inputImgId = document.createElement('input');
        inputImgId.type = 'hidden';
        inputImgId.name = 'imageID';
        inputImgId.value = img.id;

        const inputProdId = document.createElement('input');
        inputProdId.type = 'hidden';
        inputProdId.name = 'productID';
        inputProdId.value = productId;

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'submit';
        deleteBtn.textContent = 'Delete';

        form.appendChild(inputImgId);
        form.appendChild(inputProdId);
        form.appendChild(deleteBtn);

        item.appendChild(image);
        item.appendChild(form);
        list.appendChild(item);
    });
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