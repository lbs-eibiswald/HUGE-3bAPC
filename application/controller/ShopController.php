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
        $this->View->render('shop/index');
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
}