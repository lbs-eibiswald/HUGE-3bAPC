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
            'productImages' => ShopModel::getAllProductImages()
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

        // Check for user Role - Permissions
        if (!Session::get("user_account_type") == 7) {
            Session::add('feedback_negative', 'You don\'t have permissions to create a category');
            Redirect::to('shop/index');
            return;
        }

        // Create new Product
        $productID = (int) ShopModel::createProductEntry($productName, $productDescription, $categoryID, $productInventoryAmount);

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

    public function addToCart() {
        $userID = (int) Session::get('user_id');
        $productID = (int) Request::post('productID');
        $productAmount = (int) Request::post('productAmount');

        if (!ShopModel::checkIfProductExists($productID)) {
            Session::add('feedback_negative', 'The selected product does not exist. Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        }

        ShopModel::addProductToCart($productID, $productAmount);

        Session::add('feedback_positive', 'Product successfully added to cart.');
        Redirect::to('shop/index');
        return;
    }
}