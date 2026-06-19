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
            'categories' => ShopModel::getAllCategories()
        ));
    }

    public function createNewCategory() {
        $categoryName = (string) Request::post('categoryInput');

        if (!Session::get("user_account_type") == 7) {
            Session::add('feedback_negative', 'You don\'t have permissions to create a category');
            Redirect::to('shop/index');
            return;
        }

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

        if (!ShopModel::createProductEntry($productName, $productDescription, $categoryID, $productInventoryAmount)) {
            Session::add('feedback_negative', 'Something wen\'t wrong.');
            Redirect::to('shop/index');
            return;
        } 

        Redirect::to('shop/index');
        Session::add('feedback_positive', 'Product successfully created!');
    }
}