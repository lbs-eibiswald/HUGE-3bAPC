<div class="container">
    <h1>Shop</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div class="shop-container">
            <?php if (Session::get("user_account_type") == 7) : ?>
                <button onclick="toggleVisibility('create-category-container')">Create new Category</button>
                <button onclick="toggleVisibility('create-product-container')">Create new Product</button>

            <!-- CREATE NEW CATEGORY -->
            <div class="create-category" id="create-category-container">
                <form action="<?php echo Config::get('URL'); ?>shop/createNewCategory" method="post">
                    <label>Category:</label>
                    <input type="text" name="categoryInput" placeholder="category name">
                    <button type="submit">Create category</button>
                </form>
            </div>

            <!-- CREATE NEW PRODUCT -->
            <div class="create-product" id="create-product-container">
                <p>Create a new product</p>
                <form></form>
            </div>

            <?php endif; ?>
        </div>

    </div>
</div>