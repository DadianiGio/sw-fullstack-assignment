<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Connection;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = Connection::getInstance();

echo "Creating tables...\n";

// Categories
$pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
        id   INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Products
$pdo->exec("
    CREATE TABLE IF NOT EXISTS products (
        id          VARCHAR(100) PRIMARY KEY,
        name        VARCHAR(255) NOT NULL,
        in_stock    TINYINT(1)   NOT NULL DEFAULT 1,
        gallery     LONGTEXT,
        description LONGTEXT,
        category    VARCHAR(100),
        brand       VARCHAR(255),
        FOREIGN KEY (category) REFERENCES categories(name) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Attribute sets (e.g. "Size", "Color")
$pdo->exec("
    CREATE TABLE IF NOT EXISTS attribute_sets (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        product_id   VARCHAR(100) NOT NULL,
        attribute_id VARCHAR(100) NOT NULL,
        name         VARCHAR(100) NOT NULL,
        type         VARCHAR(50)  NOT NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Attribute items (e.g. "S", "M", "L")
$pdo->exec("
    CREATE TABLE IF NOT EXISTS attribute_items (
        id               INT AUTO_INCREMENT PRIMARY KEY,
        attribute_set_id INT          NOT NULL,
        item_id          VARCHAR(100) NOT NULL,
        display_value    VARCHAR(255) NOT NULL,
        value            VARCHAR(255) NOT NULL,
        FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Prices
$pdo->exec("
    CREATE TABLE IF NOT EXISTS prices (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        product_id      VARCHAR(100)   NOT NULL,
        amount          DECIMAL(10, 2) NOT NULL,
        currency_label  VARCHAR(10)    NOT NULL,
        currency_symbol VARCHAR(5)     NOT NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Orders
$pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        created_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Order items
$pdo->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id                  INT AUTO_INCREMENT PRIMARY KEY,
        order_id            INT          NOT NULL,
        product_id          VARCHAR(100) NOT NULL,
        quantity            INT          NOT NULL DEFAULT 1,
        selected_attributes LONGTEXT,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "Tables created.\nSeeding data...\n";

//Seed from the JSON
$json = <<<'JSON'
{
    "categories": ["all","clothes","tech"],
    "products": [
        {
            "id": "huarache-x-stussy-le",
            "name": "Nike Air Huarache Le",
            "inStock": true,
            "gallery": ["https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087","https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087","https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087","https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087","https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087"],
            "description": "<p>Great sneakers for everyday use!</p>",
            "category": "clothes",
            "brand": "Nike x Stussy",
            "attributes": [{"id":"Size","name":"Size","type":"text","items":[{"id":"40","displayValue":"40","value":"40"},{"id":"41","displayValue":"41","value":"41"},{"id":"42","displayValue":"42","value":"42"},{"id":"43","displayValue":"43","value":"43"}]}],
            "prices": [{"amount":144.69,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "jacket-canada-goosee",
            "name": "Jacket",
            "inStock": true,
            "gallery": ["https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg","https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg","https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg","https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg","https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg","https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png","https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png"],
            "description": "<p>Awesome winter jacket</p>",
            "category": "clothes",
            "brand": "Canada Goose",
            "attributes": [{"id":"Size","name":"Size","type":"text","items":[{"id":"Small","displayValue":"Small","value":"S"},{"id":"Medium","displayValue":"Medium","value":"M"},{"id":"Large","displayValue":"Large","value":"L"},{"id":"Extra Large","displayValue":"Extra Large","value":"XL"}]}],
            "prices": [{"amount":518.47,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "ps-5",
            "name": "PlayStation 5",
            "inStock": true,
            "gallery": ["https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg","https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg","https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg"],
            "description": "<p>A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha</p>",
            "category": "tech",
            "brand": "Sony",
            "attributes": [{"id":"Color","name":"Color","type":"swatch","items":[{"id":"Green","displayValue":"Green","value":"#44FF03"},{"id":"Cyan","displayValue":"Cyan","value":"#03FFF7"},{"id":"Blue","displayValue":"Blue","value":"#030BFF"},{"id":"Black","displayValue":"Black","value":"#000000"},{"id":"White","displayValue":"White","value":"#FFFFFF"}]},{"id":"Capacity","name":"Capacity","type":"text","items":[{"id":"512G","displayValue":"512G","value":"512G"},{"id":"1T","displayValue":"1T","value":"1T"}]}],
            "prices": [{"amount":844.02,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "xbox-series-s",
            "name": "Xbox Series S 512GB",
            "inStock": false,
            "gallery": ["https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg","https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg"],
            "description": "<p>Xbox Series S 512GB</p>",
            "category": "tech",
            "brand": "Microsoft",
            "attributes": [{"id":"Color","name":"Color","type":"swatch","items":[{"id":"Green","displayValue":"Green","value":"#44FF03"},{"id":"Cyan","displayValue":"Cyan","value":"#03FFF7"},{"id":"Blue","displayValue":"Blue","value":"#030BFF"},{"id":"Black","displayValue":"Black","value":"#000000"},{"id":"White","displayValue":"White","value":"#FFFFFF"}]},{"id":"Capacity","name":"Capacity","type":"text","items":[{"id":"512G","displayValue":"512G","value":"512G"},{"id":"1T","displayValue":"1T","value":"1T"}]}],
            "prices": [{"amount":333.99,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "apple-imac-2021",
            "name": "iMac 2021",
            "inStock": true,
            "gallery": ["https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000"],
            "description": "<p>The new iMac!</p>",
            "category": "tech",
            "brand": "Apple",
            "attributes": [{"id":"Capacity","name":"Capacity","type":"text","items":[{"id":"256GB","displayValue":"256GB","value":"256GB"},{"id":"512GB","displayValue":"512GB","value":"512GB"}]},{"id":"With USB 3 ports","name":"With USB 3 ports","type":"text","items":[{"id":"Yes","displayValue":"Yes","value":"Yes"},{"id":"No","displayValue":"No","value":"No"}]},{"id":"Touch ID in keyboard","name":"Touch ID in keyboard","type":"text","items":[{"id":"Yes","displayValue":"Yes","value":"Yes"},{"id":"No","displayValue":"No","value":"No"}]}],
            "prices": [{"amount":1688.03,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "apple-iphone-12-pro",
            "name": "iPhone 12 Pro",
            "inStock": true,
            "gallery": ["https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&hei=1112&fmt=jpeg&qlt=80&.v=1604021663000"],
            "description": "<p>This is iPhone 12. Nothing else to say.</p>",
            "category": "tech",
            "brand": "Apple",
            "attributes": [{"id":"Capacity","name":"Capacity","type":"text","items":[{"id":"512G","displayValue":"512G","value":"512G"},{"id":"1T","displayValue":"1T","value":"1T"}]},{"id":"Color","name":"Color","type":"swatch","items":[{"id":"Green","displayValue":"Green","value":"#44FF03"},{"id":"Cyan","displayValue":"Cyan","value":"#03FFF7"},{"id":"Blue","displayValue":"Blue","value":"#030BFF"},{"id":"Black","displayValue":"Black","value":"#000000"},{"id":"White","displayValue":"White","value":"#FFFFFF"}]}],
            "prices": [{"amount":1000.76,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "apple-airpods-pro",
            "name": "AirPods Pro",
            "inStock": false,
            "gallery": ["https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000"],
            "description": "<p>AirPods Pro — Active Noise Cancellation for immersive sound.</p>",
            "category": "tech",
            "brand": "Apple",
            "attributes": [],
            "prices": [{"amount":300.23,"currency":{"label":"USD","symbol":"$"}}]
        },
        {
            "id": "apple-airtag",
            "name": "AirTag",
            "inStock": true,
            "gallery": ["https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000"],
            "description": "<p>Lose your knack for losing things. AirTag is an easy way to keep track of your stuff.</p>",
            "category": "tech",
            "brand": "Apple",
            "attributes": [],
            "prices": [{"amount":120.57,"currency":{"label":"USD","symbol":"$"}}]
        }
    ]
}
JSON;

$data = json_decode($json, true);

// Insert categories
$catStmt = $pdo->prepare('INSERT IGNORE INTO categories (name) VALUES (:name)');
foreach ($data['categories'] as $cat) {
    $catStmt->execute([':name' => $cat]);
}

// Insert products, attributes, prices
$prodStmt = $pdo->prepare(
    'INSERT IGNORE INTO products (id, name, in_stock, gallery, description, category, brand)
     VALUES (:id, :name, :in_stock, :gallery, :description, :category, :brand)'
);

$setStmt = $pdo->prepare(
    'INSERT INTO attribute_sets (product_id, attribute_id, name, type)
     VALUES (:product_id, :attribute_id, :name, :type)'
);

$itemStmt = $pdo->prepare(
    'INSERT INTO attribute_items (attribute_set_id, item_id, display_value, value)
     VALUES (:set_id, :item_id, :display_value, :value)'
);

$priceStmt = $pdo->prepare(
    'INSERT INTO prices (product_id, amount, currency_label, currency_symbol)
     VALUES (:product_id, :amount, :currency_label, :currency_symbol)'
);

foreach ($data['products'] as $p) {
    // Product row
    $prodStmt->execute([
        ':id'          => $p['id'],
        ':name'        => $p['name'],
        ':in_stock'    => $p['inStock'] ? 1 : 0,
        ':gallery'     => json_encode($p['gallery']),
        ':description' => $p['description'],
        ':category'    => $p['category'],
        ':brand'       => $p['brand'],
    ]);

    // Attribute sets + items
    foreach ($p['attributes'] as $attr) {
        $setStmt->execute([
            ':product_id'   => $p['id'],
            ':attribute_id' => $attr['id'],
            ':name'         => $attr['name'],
            ':type'         => $attr['type'],
        ]);
        $setId = (int) $pdo->lastInsertId();

        foreach ($attr['items'] as $item) {
            $itemStmt->execute([
                ':set_id'        => $setId,
                ':item_id'       => $item['id'],
                ':display_value' => $item['displayValue'],
                ':value'         => $item['value'],
            ]);
        }
    }

    // Prices
    foreach ($p['prices'] as $price) {
        $priceStmt->execute([
            ':product_id'      => $p['id'],
            ':amount'          => $price['amount'],
            ':currency_label'  => $price['currency']['label'],
            ':currency_symbol' => $price['currency']['symbol'],
        ]);
    }
}

echo "Done! Database seeded successfully.\n";