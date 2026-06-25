<?php

class ShopModel {

    // ===== CATEGORY =====
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

    // ===== PRODUCT =====
    public static function getAllProducts() {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT 
                    p.*,
                    c.id AS category_id,
                    c.name AS category_name
                FROM shop_products p
                INNER JOIN shop_categories c
                    ON p.category = c.id;";

        $query = $database->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public static function getAllProductImages() {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id, name, product_id FROM shop_images;";
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

        if ($result) {
            return $database->lastInsertId();
        };
        return -1;
    }

    /**
     * Verify if the file meets every requirenment
     * @return boolean if the file is allowed to upload or not.
     */
    public static function verifyFileUpload() {
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg'];
        $maxFileSize = 5 * 1024 * 1024;

        if(!empty(array_filter($_FILES['fileUpload']['name']))) {

            // loop through every file to perform the checks
            foreach ($_FILES['fileUpload']['tmp_name'] as $key => $value) {
                $fileTempName = $_FILES['fileUpload']['tmp_name'][$key];
                $fileName = $_FILES['fileUpload']['name'][$key];
                $fileSize = $_FILES['fileUpload']['size'][$key];

                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $fileInfo->file($fileTempName);

                if(in_array($mime, $allowedFileTypes)) {
                    if ($fileSize > $maxFileSize) {
                        Session::add('feedback_negative', 'Error verifying file: The file {$fileName} is too large. Maximum size is: {$maxFileSize}');
                        return false;
                    }
                }
                else {
                    // If file extension not valid
                    Session::add('feedback_negative', 'Error verifying file: The file {$fileName} is not valid.');
                    return false;
                }
            }
        }

        return true;
    }

    public static function uploadImage(int $productID) {
        $database = DatabaseFactory::getFactory()->getConnection();

        if(!empty(array_filter($_FILES['fileUpload']['name']))) {

            // loop through every file to perform the checks
            foreach ($_FILES['fileUpload']['tmp_name'] as $key => $value) {
                $fileTempName = $_FILES['fileUpload']['tmp_name'][$key];
                $filename = preg_replace('/[^a-zA-Z0-9. -]/', '_', basename($_FILES['fileUpload']['name'][$key]));
                $targetDirectory = dirname(dirname(__DIR__)) . '/public/shopImages/' . $productID . '/';
                $targetFile = $targetDirectory  . time() . '_' . $filename;
                $fileSize = $_FILES['fileUpload']['size'][$key];

                if (!file_exists($targetDirectory)) {
                    mkdir($targetDirectory, 0777, true);
                }

                move_uploaded_file($fileTempName, $targetFile);

                // Database insertion
                // Save Image info into database
                $sql = "INSERT INTO shop_images (name, product_id, size, timestamp)
                        VALUES (:name, :product_id, :size, NOW())";
                $query = $database->prepare($sql);

                $query->execute(array(
                    ':name' => time() . '_' . $filename,
                    ':product_id' => $productID,
                    ':size' => $fileSize
                ));
            }
        }
    }

    public static function checkIfProductExists(int $productID) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM shop_products WHERE id = :id LIMIT 1;";
        $query = $database->prepare($sql);
        $query->execute(array(
            ':id' => $productID
        ));

        if (empty($query->fetch())) return false;
        
        return true;
    }

    public static function checkProductInventoryAmount(int $productID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT inventory_amount FROM shop_products WHERE id = :id LIMIT 1;";
        $query = $database->prepare($sql);
        $query->execute(array(
            ':id' => $productID
        ));

        if ($query->fetch() < $amount) {
            return false;
        }

        return true;
    }

    public static function addProductToCart(int $productID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO shopping_cart (product_id, product_amount, owner_id)
                VALUES (:id, :amount, :owner_id);";
        $query = $database->prepare($sql);

        $query->execute(array(
            ':id' => $productID,
            ':amount' => $amount,
            ':owner_id' => Session::get('user_id')
        ));
    }

    public static function removeProductFromCart(int $productID) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM shopping_cart WHERE product_id = :id AND owner_id = :owner_id LIMIT 1;";
        $query = $database->prepare($sql);

        $query->execute(array(
            ':id' => $productID,
            ':owner_id' => Session::get('user_id')
        ));

        if (!$query) return false;

        return true;
    }

    // SHOPPING CART
    public static function getAllShoppingCartProducts() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $userID = Session::get('user_id');

        $sql = "SELECT
                    sc.*,
                    p.*,
                    c.id AS category_id,
                    c.name AS category_name
                FROM shopping_cart sc
                INNER JOIN shop_products p
                    ON sc.product_id = p.id
                INNER JOIN shop_categories c
                    ON p.category = c.id
                WHERE sc.owner_id = :id;";
        
        $query = $database->prepare($sql);
        $query->execute(array(
            ':id' => $userID
        ));

        return $query->fetchAll();
    }
}