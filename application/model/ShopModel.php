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

        return false;
    }

    public static function getAllCategories() {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id, name FROM shop_categories;";
        $query = $database->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public static function createProductEntry($productName, $productDescription, $categoryID, $productInventoryAmount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO shop_products (name, description, category, inventory_amount)
                VALUES (:name, :desc, :categoryID, :amount) LIMIT 1;";
        $query = $database->prepare($sql);
        $result = $query->execute(array(
            ':name' => $productName,
            ':desc' => $productDescription,
            ':categoryID' => $categoryID,
            ':amount' => $productInventoryAmount
        ));

        if ($result) return true;
        
        return false;
    }
}