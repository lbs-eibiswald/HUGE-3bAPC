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

    public static function createProductEntry(string $name, float $price, string $description, int $categoryID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "INSERT INTO shop_products (name, price, description, category, inventory_amount)
                VALUES (:name, :price, :desc, :categoryID, :amount) LIMIT 1;";
        $query = $database->prepare($sql);
        $result = $query->execute(array(
            ':name' => $name,
            ':price' => $price,
            ':desc' => $description,
            ':categoryID' => $categoryID,
            ':amount' => $amount
        ));

        if ($result) {
            return $database->lastInsertId();
        };
        return -1;
    }

    public static function updateProductEntry(int $id, string $name, float $price, string $description, int $categoryID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE shop_products SET name = :name, price = :price, description = :desc, category = :categoryID, inventory_amount = :amount WHERE id = :id LIMIT 1;";
        $query = $database->prepare($sql);
        $result = $query->execute(array(
            ':name' => $name,
            ':price' => $price,
            ':desc' => $description,
            ':categoryID' => $categoryID,
            ':amount' => $amount,
            ':id' => $id
        ));

        return $result;
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

    public static function deleteProductImage(int $imageID, int $productID) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT name, product_id FROM shop_images WHERE id = :id AND product_id = :product_id LIMIT 1;";
        $query = $database->prepare($sql);
        $query->execute([':id' => $imageID, ':product_id' => $productID]);
        $image = $query->fetch();

        if (!$image) return false;

        $filePath = dirname(dirname(__DIR__)) . '/public/shopImages/' . $image->product_id . '/' . $image->name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $sql = "DELETE FROM shop_images WHERE id = :id LIMIT 1;";
        $query = $database->prepare($sql);
        return $query->execute([':id' => $imageID]);
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

        $row = $query->fetch();

        if (!$row || (int) $row->inventory_amount < $amount) {
            return false;
        }

        return true;
    }

    public static function addProductToCart(int $productID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        // Check if Product already exists in cart, if so: increase amount of $amount
        $sql = "SELECT * FROM shopping_cart WHERE product_id = :product_id AND owner_id = :owner_id LIMIT 1;";
        $query = $database->prepare($sql);
        $query->execute(array(
            ':product_id' => $productID,
            ':owner_id' => Session::get('user_id')
        ));

        if(!empty($query->rowCount())) {
            $sql = "UPDATE shopping_cart SET product_amount = product_amount + :amount WHERE product_id = :product_id AND owner_id = :owner_id LIMIT 1;";
            $query = $database->prepare($sql);

            $query->execute(array(
                ':amount' => $amount,
                ':product_id' => $productID,
                ':owner_id' => Session::get('user_id')
            ));
            
            return;
        }

        $sql = "INSERT INTO shopping_cart (product_id, product_amount, owner_id)
                VALUES (:id, :amount, :owner_id);";
        $query = $database->prepare($sql);

        $query->execute(array(
            ':id' => $productID,
            ':amount' => $amount,
            ':owner_id' => Session::get('user_id')
        ));
    }

    public static function updateCartProductAmount(int $productID, int $amount) {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE shopping_cart SET product_amount = :amount WHERE product_id = :product_id AND owner_id = :owner_id LIMIT 1;";
        $query = $database->prepare($sql);

        $query->execute(array(
            ':amount' => $amount,
            ':product_id' => $productID,
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

    /**
     * Validates billing address and payment details from the checkout form.
     * @return array List of error messages; empty array means everything is valid.
     */
    public static function validateCheckoutDetails(
        string $firstname,
        string $email,
        string $address,
        string $city,
        string $state,
        string $zip,
        string $cardname,
        string $cardnumber,
        string $expmonth,
        string $expyear,
        string $cvv
    ): array {
        $errors = [];

        // Billing Address
        if (empty(trim($firstname))) {
            $errors[] = 'Full name is required.';
        }

        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        if (empty(trim($address))) {
            $errors[] = 'Address is required.';
        }

        if (empty(trim($city))) {
            $errors[] = 'City is required.';
        }

        if (empty(trim($state))) {
            $errors[] = 'State is required.';
        }

        if (!preg_match('/^\d{4,5}$/', trim($zip))) {
            $errors[] = 'Zip code must be 4 or 5 digits.';
        }

        // Payment
        if (empty(trim($cardname))) {
            $errors[] = 'Name on card is required.';
        }

        $digitsOnly = preg_replace('/\s+/', '', $cardnumber);
        if (!preg_match('/^\d{16}$/', $digitsOnly)) {
            $errors[] = 'Credit card number must be exactly 16 digits.';
        }

        $validMonths = [
            'january','february','march','april','may','june',
            'july','august','september','october','november','december'
        ];
        $monthInput = strtolower(trim($expmonth));
        $monthIsNumber = ctype_digit($monthInput) && (int)$monthInput >= 1 && (int)$monthInput <= 12;
        $monthIsName   = in_array($monthInput, $validMonths);
        if (!$monthIsNumber && !$monthIsName) {
            $errors[] = 'Expiry month must be a valid month name or number (1–12).';
        }

        $currentYear = (int) date('Y');
        if (!ctype_digit(trim($expyear)) || (int)$expyear < $currentYear) {
            $errors[] = 'Expiry year must be ' . $currentYear . ' or later.';
        }

        if (!preg_match('/^\d{3,4}$/', trim($cvv))) {
            $errors[] = 'CVV must be 3 or 4 digits.';
        }

        return $errors;
    }

    public static function handleAffectedProducts() {
        $database = DatabaseFactory::getFactory()->getConnection();
        $userID = Session::get('user_id');
        $affectedProducts = self::getAllShoppingCartProducts();

        //decrease inventory amount
        $sql = "UPDATE shop_products SET inventory_amount = inventory_amount - :amount WHERE id = :product_id LIMIT 1;";
        $query = $database->prepare($sql);

        foreach ($affectedProducts as $product) {
            $res = $query->execute(array(
                ':amount' => $product->product_amount,
                ':product_id' => $product->product_id,
            ));
        }

        //delete cart Products
        $sql = "DELETE FROM shopping_cart WHERE product_id = :product_id AND owner_id = :user_id LIMIT 1;";
        $query = $database->prepare($sql);

        foreach ($affectedProducts as $product) {
            $res = $query->execute(array(
                ':product_id' => $product->product_id,
                ':user_id' => $userID
            ));
        }
        
        if (!$res) return false;

        return true;
    }
}