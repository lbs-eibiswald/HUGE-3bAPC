<div class="container">
    <h1>Shop</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div class="shop-container">
            <?php if (Session::get("user_account_type") == 7) : ?>
                <div id="admin-actions">
                    <button onclick="toggleVisibility('create-category-container')">Create new Category</button>
                    <button onclick="toggleVisibility('create-product-container')">Create new Product</button>
                </div>

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
                    <input type="text" name="productName" placeholder="Enter product name" required>

                    <br><br>
                    <label>Product Description:</label>
                    <textarea type="text" name="productDescription" placeholder="Enter product description" required></textarea>

                    <br>
                    <label>Product Price:</label>
                    <input type="number" step="0.01" name="productPrice" placeholder="14.99" required>

                    <br><br><br>
                    <label>Select multiple product images</label>
                    <input type="file" name="fileUpload[]" id="fileUpload" multiple="multiple" accept=".png, .jpg, .jpeg, .webpb, .svg">
                    <br>

                    <br><br>
                    <label>Category:</label>

                    <select id="categorySelection" name="categorySelection" required>
                        <?php foreach ($this->categories as $category) { ?>
                            <option value="<?= $category->id; ?>">
                                <?= $category->name; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <br><br>
                    <label>Inventory amount</label>
                    <input type="number" name="productAmount" placeholder="Enter inventory amount">
                    <p class="hint">Warning: If you don't enter an amount, the product will not be available.</p>

                    <br><br>
                    <button type="submit">Create Product</button>
                </form>
            </div>

            <?php endif; ?>

            <!-- SHOW PRODUCTS -->
            <div id="view-container" class="mode-shop">

                <button id="btn-toggle-view" onclick="toggleView()">View Shopping Cart</button>
                <a href="<?php echo Config::get('URL'); ?>shop/checkout" class="cart-only button">Checkout</a>
            
                <?php
                    // Build a lookup set of product IDs and amounts currently in the cart
                    $cartProductIds = [];
                    $cartAmounts = [];
                    foreach ($this->shoppingCart as $cartItem) {
                        $cartProductIds[$cartItem->product_id] = true;
                        $cartAmounts[$cartItem->product_id] = $cartItem->product_amount;
                    }
                ?>

                <?php foreach ($this->products as $product) {
                    $inCart = isset($cartProductIds[$product->id]); // check if this product is in the cart
                ?>
                    <!-- data-in-cart lets JS filter products when switching to cart view -->
                    <div class="product" data-in-cart="<?= $inCart ? 'true' : 'false' ?>">
                        <?php
                            $hasImage = false;

                            foreach ($this->productImages as $image) {
                                if ($image->product_id != $product->id) continue;

                                $hasImage = true;
                                $imagePath = Config::get('URL') . 'shopImages/' . $image->product_id . '/' . $image->name; ?>

                                <div class="image-container">
                                    <img class="product-image" src="<?php echo $imagePath; ?>">
                                </div>

                                <?php
                                break;
                            }

                            // Show placeholder image
                            if (!$hasImage) { ?>
                                <div class="image-container">
                                    <img class="product-image" src="<?php echo Config::get('URL'); ?>shopImages/placeholder/placeholder.png">
                                </div>
                        <?php } ?>

                        <div class="detail-container">
                            <p class="product-name"><?php echo $product->name; ?></p>
                            <p class="product-description"><?php echo $product->description; ?></p>
                            <p class="product-price">Price: <?php echo $product->price; ?></p>
                            <p class="product-category">Category: <?php echo $product->category_name; ?></p>

                            <!-- shop-only: hidden in cart mode via CSS -->
                            <div class="shop-only">
                                <?php if (!empty($product->inventory_amount)) { ?>
                                    <p>Inventory: <?php echo $product->inventory_amount; ?></p>
                                    <form action="<?php echo Config::get('URL'); ?>shop/addToCart" method="post">
                                        <input type="hidden" name="productID" value="<?php echo $product->id; ?>">
                                        <input type="number" class="product-amount-input" name="productAmount" min="1" max="<?php echo $product->inventory_amount; ?>" value="1">
                                        <button type="submit" class="button">Place into cart</button>
                                    </form>
                                <?php } else { ?>
                                    <p class="no-inventory-text">This product is currently unavailable</p>
                                <?php } ?>
                            </div>

                            <!-- cart-only: hidden in shop mode via CSS -->
                            <div class="cart-only">
                                <form action="<?php echo Config::get('URL'); ?>shop/updateCartAmount" method="post">
                                    <input type="hidden" name="productID" value="<?php echo $product->id; ?>">
                                    <input type="number" name="productAmount" value="<?php echo $cartAmounts[$product->id] ?? 1; ?>" min="1" max="<?php echo $product->inventory_amount; ?>">
                                    <button type="submit" class="button">Update amount</button>
                                </form>

                                <form action="<?php echo Config::get('URL'); ?>shop/removeFromCart" method="post">
                                    <input type="hidden" name="productID" value="<?php echo $product->id; ?>">
                                    <button type="submit" class="button">Remove from cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if (empty($this->products)) { ?>
                    <p class="shop-only">No products available.</p>
                <?php } ?>

                <?php if (empty($this->shoppingCart)) { ?>
                    <p class="cart-only">No products in your shopping cart.</p>
                <?php } ?>



            </div>
        </div>

    </div>
</div>
