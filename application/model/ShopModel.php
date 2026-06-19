<?php

class ShopModel {
    public static function createCategoryEntry(string $categoryName) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO shop_categories (name) VALUES (:categoryname);";
        $query = $database->prepare($sql);
        $result = $query->execute(array(
            ':categoryname' => $categoryName
        ));

        if ($result) return true;
        Session::add('feedback_negative', 'Something wen\'t wrong while saving into the database');
        return false;
    }
}