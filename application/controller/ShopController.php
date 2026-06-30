<?php

class ShopController extends Controller {
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
        Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /overview/index in your app.
     * Shows a list of all users.
     */
    public function index() {
        $this->View->render('shop/index', array(
            'categories' => ShopModel::getAllCategories(),
            'products' => ShopModel::getAllProducts(),
            'productImages' => ShopModel::getAllProductImages(),
            'shoppingCart' => ShopModel::getAllShoppingCartProducts()
        ));
    }

    public function createNewCategory() {
        $categoryName = (string) Request::post('categoryInput');

        // Check for user Role - Permissions
        if (!Session::get("user_account_type") == 7) {
            Session::add('feedback_negative', 'You don\'t have permissions to create a category');
            Redirect::to('shop/index');
            return;
        }

        // Create new Category
        if (!ShopModel::createCategoryEntry($categoryName)) {
            Session::add('feedback_negative', 'Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        } 

        Redirect::to('shop/index');
        Session::add('feedback_positive', 'Category successfully created!');
    }
    
    public function createNewProduct() {
        $productName = (string) Request::post('productName');
        $productDescription  = (string) Request::post('productDescription');
        $categoryID  = (int) Request::post('categorySelection');
        $productInventoryAmount  = (int) Request::post('productAmount');
        $productPrice = (float) str_replace(',', '.', Request::post('productPrice'));

        // Check for user Role - Permissions
        if (!Session::get("user_account_type") == 7) {
            Session::add('feedback_negative', 'You don\'t have permissions to create a category');
            Redirect::to('shop/index');
            return;
        }

        // Create new Product
        $productID = (int) ShopModel::createProductEntry($productName, $productPrice, $productDescription, $categoryID, $productInventoryAmount);

        if ($productID == -1) {
            Session::add('feedback_negative', 'Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        }

        if (!ShopModel::verifyFileUpload()) {
            Session::add('feedback_negative', 'Something wen\'t wrong with one image. Please ensure the size is not too high.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::uploadImage($productID);

        Redirect::to('shop/index');
        Session::add('feedback_positive', 'Product successfully created!');
    }

    public function updateProduct() {
        if (Session::get("user_account_type") != 7) {
            Session::add('feedback_negative', 'You don\'t have permissions to edit a product.');
            Redirect::to('shop/index');
            return;
        }

        $productID   = (int) Request::post('productID');
        $productName = (string) Request::post('productName');
        $productDescription = (string) Request::post('productDescription');
        $categoryID  = (int) Request::post('categorySelection');
        $productInventoryAmount = (int) Request::post('productAmount');
        $productPrice = (float) str_replace(',', '.', Request::post('productPrice'));

        if (!ShopModel::checkIfProductExists($productID)) {
            Session::add('feedback_negative', 'Product not found.');
            Redirect::to('shop/index');
            return;
        }

        if (!ShopModel::updateProductEntry($productID, $productName, $productPrice, $productDescription, $categoryID, $productInventoryAmount)) {
            Session::add('feedback_negative', 'Something went wrong while updating the product.');
            Redirect::to('shop/index');
            return;
        }

        if (!ShopModel::verifyFileUpload()) {
            Session::add('feedback_negative', 'Something went wrong with one image.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::uploadImage($productID);

        Session::add('feedback_positive', 'Product successfully updated!');
        Redirect::to('shop/index');
    }

    public function addToCart() {
        $userID = (int) Session::get('user_id');
        $productID = (int) Request::post('productID');
        $productAmount = (int) Request::post('productAmount');

        if (!ShopModel::checkIfProductExists($productID)) {
            Session::add('feedback_negative', 'The selected product does not exist. Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        }

        if (!ShopModel::checkProductInventoryAmount($productID, $productAmount)) {
            Session::add('feedback_negative', 'The inventory amount is not high enough for this action.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::addProductToCart($productID, $productAmount);

        Session::add('feedback_positive', 'Product successfully added to cart.');
        Redirect::to('shop/index');
        return;
    }

    public function updateCartAmount() {
        $productID = (int) Request::post('productID');
        $productAmount = (int) Request::post('productAmount');

        if (!ShopModel::checkIfProductExists($productID)) {
            Session::add('feedback_negative', 'The selected product does not exist. Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        }

        if (!ShopModel::checkProductInventoryAmount($productID, $productAmount)) {
            Session::add('feedback_negative', 'The inventory amount is not high enough for this action.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::updateCartProductAmount($productID, $productAmount);

        Session::add('feedback_positive', 'Cart updated successfully.');
        Redirect::to('shop/index');
    }

    public function removeFromCart() {
        $productID = (int) Request::post('productID');

        if (!ShopModel::checkIfProductExists($productID)) {
            Session::add('feedback_negative', 'The selected product does not exist. Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::removeProductFromCart($productID);

        Session::add('feedback_positive', 'Product successfully added to cart.');
        Redirect::to('shop/index');
        return;
    }

    // ===== CHECKOUT PAGE =====
    public function checkout() {
        // restore form data from session
        $formData = $_SESSION['checkout_form_data'] ?? [];
        unset($_SESSION['checkout_form_data']);

        $this->View->render('shop/checkout', array(
            'shoppingCart' => ShopModel::getAllShoppingCartProducts(),
            'formData'     => $formData
        ));
    }

    // ===== PLACE ORDER =====
    // validate every form, clear cart and decrease product inventory amount
    public function placeOrder() {
        $formData = [
            'firstname'  => (string) Request::post('firstname'),
            'email'      => (string) Request::post('email'),
            'address'    => (string) Request::post('address'),
            'city'       => (string) Request::post('city'),
            'state'      => (string) Request::post('state'),
            'zip'        => (string) Request::post('zip'),
            'cardname'   => (string) Request::post('cardname'),
            'cardnumber' => (string) Request::post('cardnumber'),
            'expmonth'   => (string) Request::post('expmonth'),
            'expyear'    => (string) Request::post('expyear'),
            'cvv'        => (string) Request::post('cvv'),
        ];

        // serverside validation of form data
        $errors = ShopModel::validateCheckoutDetails(
            $formData['firstname'],
            $formData['email'],
            $formData['address'],
            $formData['city'],
            $formData['state'],
            $formData['zip'],
            $formData['cardname'],
            $formData['cardnumber'],
            $formData['expmonth'],
            $formData['expyear'],
            $formData['cvv']
        );

        if (!empty($errors)) {
            // if errors, save form data, return to checkout and show errors
            $_SESSION['checkout_form_data'] = $formData;
            foreach ($errors as $error) Session::add('feedback_negative', $error);

            Redirect::to('shop/checkout');
            return;
        }

        // Clear Cart and decrease product inventory amount
        if (!ShopModel::handleAffectedProducts()) {
            Session::add('feedback_negative', 'Something wen\'t wrong while accessing the affected products. Please try again later.');
            Redirect::to('shop/index');
            return;
        }

        // successfully placed order.
        Session::add('feedback_positive', 'Order placed successfully! Your will receive an verification and your invoice via email.');
        Redirect::to('shop/index');
    }
}