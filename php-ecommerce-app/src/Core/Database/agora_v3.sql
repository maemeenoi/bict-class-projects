-- Create database
DROP DATABASE IF EXISTS agora_v3;

CREATE DATABASE agora_v3;

USE agora_v3;

-- Create tables
CREATE TABLE Region (
    region_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    region_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE Business (
    business_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    region_id SMALLINT NOT NULL,
    business_name VARCHAR(100) NOT NULL,
    location_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    business_logo VARCHAR(255),
    operation_hours TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES Region (region_id) ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE User (
    user_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    business_id SMALLINT,
    user_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM(
        'Business Admin',
        'Seller',
        'Buyer'
    ) NOT NULL,
    bio TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES Business (business_id) ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE Product (
    product_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    seller_id SMALLINT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    status ENUM(
        'available',
        'out_of_stock',
        'discontinued'
    ) DEFAULT 'available',
    stock_quantity INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES User (user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE Orders (
    order_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    buyer_id SMALLINT NOT NULL,
    business_id SMALLINT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM(
        'pending',
        'processing',
        'shipped',
        'delivered',
        'cancelled'
    ) DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    notes TEXT,
    FOREIGN KEY (buyer_id) REFERENCES User (user_id) ON DELETE RESTRICT,
    FOREIGN KEY (business_id) REFERENCES Business (business_id) ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE Order_Products (
    order_product_id SMALLINT PRIMARY KEY AUTO_INCREMENT,
    order_id SMALLINT NOT NULL,
    product_id SMALLINT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    FOREIGN KEY (order_id) REFERENCES Orders (order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Product (product_id) ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Insert sample data

-- 1. Regions
INSERT INTO
    Region (region_name, description)
VALUES (
        'North Island',
        'Northern region of New Zealand'
    ),
    (
        'South Island',
        'Southern region of New Zealand'
    );

-- 2. Businesses (2 in each region)
INSERT INTO
    Business (
        region_id,
        business_name,
        location_name,
        address,
        phone,
        email,
        operation_hours
    )
VALUES
    -- North Island Businesses
    (
        1,
        'Agora Wellington',
        'Wellington Central',
        '123 Lambton Quay, Wellington',
        '04-123-4567',
        'wellington@agora.nz',
        '9:00 AM - 5:00 PM'
    ),
    (
        1,
        'Agora Auckland',
        'Auckland CBD',
        '45 Queen Street, Auckland',
        '09-987-6543',
        'auckland@agora.nz',
        '9:00 AM - 5:00 PM'
    ),
    -- South Island Businesses
    (
        2,
        'Agora Christchurch',
        'Christchurch Central',
        '78 Colombo Street, Christchurch',
        '03-456-7890',
        'christchurch@agora.nz',
        '9:00 AM - 5:00 PM'
    ),
    (
        2,
        'Agora Dunedin',
        'Dunedin Central',
        '90 George Street, Dunedin',
        '03-234-5678',
        'dunedin@agora.nz',
        '9:00 AM - 5:00 PM'
    );

-- 3. Users (Business Admins, Sellers, and Buyers)
-- Note: Password hash for 'password' using PASSWORD_BCRYPT
INSERT INTO
    User (
        business_id,
        user_name,
        email,
        address,
        phone,
        password_hash,
        role,
        bio
    )
VALUES
    -- Business Admins (1 for each business)
    (
        1,
        'Wellington Admin',
        'admin.wlg@agora.nz',
        'Wellington',
        '021-111-1111',
        '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
        'Business Admin',
        'Wellington branch administrator'
    ),
    (
        2,
        'Auckland Admin',
        'admin.akl@agora.nz',
        'Auckland',
        '021-222-2222',
        '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
        'Business Admin',
        'Auckland branch administrator'
    ),
    (
        3,
        'Christchurch Admin',
        'admin.chc@agora.nz',
        'Christchurch',
        '021-333-3333',
        '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
        'Business Admin',
        'Christchurch branch administrator'
    ),
    (
        4,
        'Dunedin Admin',
        'admin.dud@agora.nz',
        'Dunedin',
        '021-444-4444',
        '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
        'Business Admin',
        'Dunedin branch administrator'
    ),
-- Sellers (5 for each region, distributed among businesses)
-- Wellington Sellers
(
    1,
    'Sarah Wilson',
    'sarah@seller.com',
    'Wellington',
    '022-111-1111',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Artisan crafts seller'
),
(
    1,
    'Mike Johnson',
    'mike@seller.com',
    'Lower Hutt',
    '022-222-2222',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Electronics specialist'
),
-- Auckland Sellers
(
    2,
    'Lisa Chen',
    'lisa@seller.com',
    'Auckland',
    '022-333-3333',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Fashion designer'
),
(
    2,
    'David Smith',
    'david@seller.com',
    'North Shore',
    '022-444-4444',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Organic food supplier'
),
(
    2,
    'Emma Brown',
    'emma@seller.com',
    'West Auckland',
    '022-555-5555',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Handmade jewelry'
),
-- Christchurch Sellers
(
    3,
    'James Wilson',
    'james@seller.com',
    'Christchurch',
    '023-111-1111',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Local produce supplier'
),
(
    3,
    'Sophie Taylor',
    'sophie@seller.com',
    'Riccarton',
    '023-222-2222',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Vintage collector'
),
-- Dunedin Sellers
(
    4,
    'Tom Anderson',
    'tom@seller.com',
    'Dunedin',
    '023-333-3333',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Book dealer'
),
(
    4,
    'Rachel White',
    'rachel@seller.com',
    'North Dunedin',
    '023-444-4444',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Art supplies'
),
(
    4,
    'Peter Lee',
    'peter@seller.com',
    'South Dunedin',
    '023-555-5555',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Seller',
    'Tech gadgets'
),
-- Buyers (5 for each region)
-- North Island Buyers
(
    1,
    'John Buyer',
    'john@buyer.com',
    'Wellington',
    '024-111-1111',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    1,
    'Mary Buyer',
    'mary@buyer.com',
    'Lower Hutt',
    '024-222-2222',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    2,
    'Steve Buyer',
    'steve@buyer.com',
    'Auckland',
    '024-333-3333',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    2,
    'Jenny Buyer',
    'jenny@buyer.com',
    'North Shore',
    '024-444-4444',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    2,
    'Kevin Buyer',
    'kevin@buyer.com',
    'West Auckland',
    '024-555-5555',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
-- South Island Buyers
(
    3,
    'Alice Buyer',
    'alice@buyer.com',
    'Christchurch',
    '025-111-1111',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    3,
    'Bob Buyer',
    'bob@buyer.com',
    'Riccarton',
    '025-222-2222',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    4,
    'Carol Buyer',
    'carol@buyer.com',
    'Dunedin',
    '025-333-3333',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    4,
    'Daniel Buyer',
    'daniel@buyer.com',
    'North Dunedin',
    '025-444-4444',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
),
(
    4,
    'Eva Buyer',
    'eva@buyer.com',
    'South Dunedin',
    '025-555-5555',
    '$2y$10$8K1p/a6N1/6wxUQCL6HXK.PbZXD7euI4yWSMvghzqx9uy4djy5IGi',
    'Buyer',
    NULL
);

-- Add sample products for sellers
INSERT INTO
    Product (
        seller_id,
        product_name,
        description,
        category,
        price,
        status,
        stock_quantity
    )
VALUES
    -- Wellington Sellers (ID: 5-6)
    (
        5,
        'Handmade Pottery Bowl',
        'Ceramic bowl with unique glazing pattern',
        'Crafts',
        45.00,
        'available',
        10
    ),
    (
        5,
        'Wool Wall Hanging',
        'Hand-woven wall decoration',
        'Home Decor',
        89.99,
        'available',
        5
    ),
    (
        5,
        'Ceramic Vase Set',
        'Set of 3 matching vases',
        'Crafts',
        120.00,
        'available',
        3
    ),
    (
        6,
        'Refurbished iPhone 11',
        'Fully tested and cleaned',
        'Electronics',
        599.99,
        'available',
        8
    ),
    (
        6,
        'Wireless Earbuds',
        'Bluetooth 5.0 compatible',
        'Electronics',
        79.99,
        'available',
        15
    ),
    (
        6,
        'Smart Watch',
        'Fitness and health tracking',
        'Electronics',
        149.99,
        'out_of_stock',
        0
    ),
-- Auckland Sellers (ID: 7-9)
(
    7,
    'Summer Dress',
    'Light cotton dress with floral pattern',
    'Fashion',
    79.99,
    'available',
    20
),
(
    7,
    'Linen Blazer',
    'Professional wear for women',
    'Fashion',
    129.99,
    'available',
    12
),
(
    7,
    'Designer Scarf',
    'Hand-painted silk scarf',
    'Accessories',
    45.00,
    'available',
    8
),
(
    8,
    'Organic Honey 500g',
    'Local NZ honey',
    'Food',
    15.99,
    'available',
    50
),
(
    8,
    'Fresh Farm Eggs',
    'Free-range organic eggs',
    'Food',
    8.99,
    'available',
    100
),
(
    8,
    'Organic Vegetable Box',
    'Weekly vegetable subscription',
    'Food',
    35.00,
    'available',
    25
),
(
    9,
    'Silver Necklace',
    'Handcrafted sterling silver',
    'Jewelry',
    89.99,
    'available',
    15
),
(
    9,
    'Pearl Earrings',
    'Freshwater pearls with silver',
    'Jewelry',
    65.00,
    'available',
    10
),
(
    9,
    'Charm Bracelet',
    'Customizable charm bracelet',
    'Jewelry',
    45.00,
    'out_of_stock',
    0
),
-- Christchurch Sellers (ID: 10-11)
(
    10,
    'Fresh Lamb Cuts',
    'Local Canterbury lamb',
    'Food',
    25.99,
    'available',
    30
),
(
    10,
    'Cheese Selection',
    'Local artisan cheeses',
    'Food',
    45.00,
    'available',
    20
),
(
    10,
    'Wine Box',
    'Selection of South Island wines',
    'Beverages',
    150.00,
    'available',
    10
),
(
    11,
    'Vintage Record Player',
    'Restored 1960s model',
    'Vintage',
    299.99,
    'available',
    2
),
(
    11,
    'Antique Tea Set',
    'Victorian era complete set',
    'Vintage',
    189.99,
    'available',
    3
),
(
    11,
    'Vintage Leather Bag',
    'Classic leather briefcase',
    'Accessories',
    159.99,
    'available',
    5
),
-- Dunedin Sellers (ID: 12-14)
(
    12,
    'Rare First Edition',
    'Collector''s item book',
    'Books',
    199.99,
    'available',
    1
),
(
    12,
    'Local History Collection',
    'Set of 5 history books',
    'Books',
    89.99,
    'available',
    8
),
(
    12,
    'Poetry Anthology',
    'Contemporary NZ poets',
    'Books',
    29.99,
    'available',
    15
),
(
    13,
    'Oil Paint Set',
    'Professional grade paints',
    'Art',
    120.00,
    'available',
    25
),
(
    13,
    'Canvas Pack',
    '5 high-quality canvases',
    'Art',
    45.00,
    'available',
    30
),
(
    13,
    'Artist Brushes',
    'Set of 12 brushes',
    'Art',
    35.00,
    'available',
    40
),
(
    14,
    'Gaming Mouse',
    'RGB gaming mouse',
    'Electronics',
    89.99,
    'available',
    20
),
(
    14,
    'Mechanical Keyboard',
    'Cherry MX switches',
    'Electronics',
    159.99,
    'available',
    15
),
(
    14,
    'Webcam',
    '1080p HD webcam',
    'Electronics',
    79.99,
    'out_of_stock',
    0
);

-- Add sample orders
INSERT INTO
    Orders (
        buyer_id,
        business_id,
        total_amount,
        status,
        shipping_address,
        notes
    )
VALUES
    -- North Island Orders
    (
        15,
        1,
        234.98,
        'delivered',
        '123 Lambton Quay, Wellington',
        'Please deliver after 5pm'
    ),
    (
        15,
        1,
        89.99,
        'processing',
        '123 Lambton Quay, Wellington',
        NULL
    ),
    (
        16,
        1,
        165.98,
        'pending',
        '45 High St, Lower Hutt',
        'Gift wrapping please'
    ),
    (
        17,
        2,
        299.97,
        'shipped',
        '67 Queen St, Auckland',
        NULL
    ),
    (
        17,
        2,
        45.00,
        'delivered',
        '67 Queen St, Auckland',
        'Leave at reception'
    ),
    (
        18,
        2,
        115.98,
        'cancelled',
        '89 Beach Rd, North Shore',
        'Changed mind'
    ),
-- South Island Orders
(
    20,
    3,
    195.99,
    'delivered',
    '12 Cathedral Square, Christchurch',
    NULL
),
(
    20,
    3,
    45.00,
    'processing',
    '12 Cathedral Square, Christchurch',
    'Fragile items'
),
(
    21,
    3,
    459.98,
    'pending',
    '34 Riccarton Rd, Christchurch',
    NULL
),
(
    22,
    4,
    199.99,
    'delivered',
    '56 George St, Dunedin',
    'Birthday gift'
),
(
    22,
    4,
    120.00,
    'shipped',
    '56 George St, Dunedin',
    NULL
);

-- Add order products
INSERT INTO
    Order_Products (
        order_id,
        product_id,
        quantity,
        unit_price
    )
VALUES (1, 1, 2, 45.00),
    (1, 2, 1, 89.99),
    (2, 3, 1, 89.99),
    (3, 4, 1, 120.00),
    (3, 5, 1, 45.98),
    (4, 7, 3, 79.99),
    (5, 9, 1, 45.00),
    (6, 10, 2, 15.99),
    (6, 11, 1, 83.99),
    (7, 16, 1, 150.99),
    (7, 17, 1, 45.00),
    (8, 18, 1, 45.00),
    (9, 19, 1, 299.99),
    (9, 20, 1, 159.99),
    (10, 22, 1, 199.99),
    (11, 25, 1, 120.00);