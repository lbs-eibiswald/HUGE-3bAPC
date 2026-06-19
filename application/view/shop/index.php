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
                    <input type="text" name="categoryInput" placeholder="Enter category name">
                    <button type="submit">Create category</button>
                </form>
            </div>

            <!-- CREATE NEW PRODUCT -->
            <div class="create-product" id="create-product-container">
                <form action="<?php echo Config::get('URL'); ?>shop/createNewProduct" method="post" enctype="multipart/form-data">
                    <br>
                    <label>Product Name:</label>
                    <input type="text" name="productName" placeholder="Enter product name">

                    <br><br>
                    <label>Product Description:</label>
                    <textarea type="text" name="productDescription" placeholder="Enter product description"></textarea>

                    <br><br><br>
                    <label>Select multiple product images</label>
                    <input type="file" name="fileUpload[]" id="fileUpload" multiple="multiple" accept=".png, .jpg, .jpeg, .webpb, .svg">
                    <!-- <input type="submit" value="Upload Image" name="submit"> -->
                    <br>

                    <br><br>
                    <label>Category:</label>

                    <select id="categorySelection" name="categorySelection">
                        <?php foreach ($this->categories as $category) { ?>
                            <option value="<?= $category->id; ?>">
                                <?= $category->name; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <br><br>
                    <label>Inventory amount</label>
                    <input type="number" name="productAmount" placeholder="Enter inventory amount">

                    <br><br>
                    <button type="submit">Create Product</button>
                </form>
            </div>

            <?php endif; ?>
        </div>

    </div>
</div>