
CREATE DATABASE IF NOT EXISTS vivek_carshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vivek_carshop;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Frontend users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    is_blocked TINYINT(1) DEFAULT 0,
    email_verified TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB;

-- Car brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Car models table (linked to brands)
CREATE TABLE IF NOT EXISTS car_models (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE,
    UNIQUE KEY unique_model (brand_id, slug),
    INDEX idx_brand (brand_id),
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Cities table for car location
CREATE TABLE IF NOT EXISTS cities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    state VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- Fuel types lookup
CREATE TABLE IF NOT EXISTS fuel_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Transmission types lookup
CREATE TABLE IF NOT EXISTS transmission_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Accessory categories
CREATE TABLE IF NOT EXISTS accessory_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    parent_id INT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES accessory_categories(id) ON DELETE SET NULL,
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB;

-- Cars table (main listing)
CREATE TABLE IF NOT EXISTS cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id INT UNSIGNED NOT NULL,
    model_id INT UNSIGNED NOT NULL,
    variant VARCHAR(150),
    year INT UNSIGNED NOT NULL,
    fuel_type_id INT UNSIGNED NOT NULL,
    transmission_id INT UNSIGNED NOT NULL,
    mileage VARCHAR(50),
    price DECIMAL(12,2) NOT NULL,
    city_id INT UNSIGNED NOT NULL,
    description TEXT,
    seller_name VARCHAR(100),
    seller_phone VARCHAR(20),
    seller_email VARCHAR(100),
    status ENUM('pending', 'approved', 'sold', 'rejected') DEFAULT 'pending',
    is_featured TINYINT(1) DEFAULT 0,
    view_count INT UNSIGNED DEFAULT 0,
    created_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES car_models(id) ON DELETE CASCADE,
    FOREIGN KEY (fuel_type_id) REFERENCES fuel_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (transmission_id) REFERENCES transmission_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_price (price),
    INDEX idx_year (year),
    INDEX idx_featured (is_featured),
    INDEX idx_created (created_at),
    FULLTEXT idx_search (variant, description)
) ENGINE=InnoDB;

-- Car images table
CREATE TABLE IF NOT EXISTS car_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_car (car_id)
) ENGINE=InnoDB;

-- Accessories table
CREATE TABLE IF NOT EXISTS accessories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    category_id INT UNSIGNED NULL,
    brand_id INT UNSIGNED NULL,
    model_id INT UNSIGNED NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    compatible_models TEXT,
    is_available TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    is_hot_sold TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 1,
    product_status VARCHAR(20) DEFAULT 'available',
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES accessory_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (model_id) REFERENCES car_models(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id),
    INDEX idx_price (price),
    INDEX idx_available (is_available)
) ENGINE=InnoDB;

-- Accessory images table
CREATE TABLE IF NOT EXISTS accessory_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accessory_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE CASCADE,
    INDEX idx_accessory (accessory_id)
) ENGINE=InnoDB;

-- Inquiries table (car & accessory inquiries via WhatsApp)
CREATE TABLE IF NOT EXISTS inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    type ENUM('car', 'accessory', 'contact') NOT NULL,
    car_id INT UNSIGNED NULL,
    accessory_id INT UNSIGNED NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_email VARCHAR(100),
    message TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL,
    FOREIGN KEY (accessory_id) REFERENCES accessories(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_car (car_id),
    INDEX idx_accessory (accessory_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Saved cars (user wishlist)
CREATE TABLE IF NOT EXISTS saved_cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, car_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- User product lists (cart / wishlist / compare persistence)
CREATE TABLE IF NOT EXISTS user_product_lists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    accessory_id INT UNSIGNED NOT NULL,
    list_type ENUM('cart','wishlist','compare') NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_product_type (user_id, accessory_id, list_type),
    INDEX list_type_index (user_id, list_type)
) ENGINE=InnoDB;

-- Settings table (WhatsApp, logo, SEO, banners)
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'text',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- Banners table for homepage
CREATE TABLE IF NOT EXISTS banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    image_path VARCHAR(255) NOT NULL,
    link_url VARCHAR(500),
    placement VARCHAR(100) NOT NULL DEFAULT 'home_main',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order (display_order),
    INDEX idx_placement (placement)
) ENGINE=InnoDB;

-- Insert default fuel types
INSERT INTO fuel_types (name, slug) VALUES
('Petrol', 'petrol'),
('Diesel', 'diesel'),
('Electric', 'electric'),
('CNG', 'cng'),
('Hybrid', 'hybrid');

-- Insert default transmission types
INSERT INTO transmission_types (name, slug) VALUES
('Manual', 'manual'),
('Automatic', 'automatic'),
('AMT', 'amt'),
('CVT', 'cvt'),
('DCT', 'dct');

-- Insert sample cities
INSERT INTO cities (name, slug, state) VALUES
('Mumbai', 'mumbai', 'Maharashtra'),
('Delhi', 'delhi', 'Delhi'),
('Bangalore', 'bangalore', 'Karnataka'),
('Chennai', 'chennai', 'Tamil Nadu'),
('Hyderabad', 'hyderabad', 'Telangana'),
('Pune', 'pune', 'Maharashtra'),
('Ahmedabad', 'ahmedabad', 'Gujarat'),
('Kolkata', 'kolkata', 'West Bengal'),
('Jaipur', 'jaipur', 'Rajasthan'),
('Lucknow', 'lucknow', 'Uttar Pradesh');

-- Insert sample brands
INSERT INTO brands (name, slug, display_order) VALUES
('Maruti Suzuki', 'maruti-suzuki', 1),
('Hyundai', 'hyundai', 2),
('Tata', 'tata', 3),
('Mahindra', 'mahindra', 4),
('Honda', 'honda', 5),
('Toyota', 'toyota', 6),
('Kia', 'kia', 7),
('MG', 'mg', 8);

-- Insert sample car models
INSERT INTO car_models (brand_id, name, slug) VALUES
(1, 'Swift', 'swift'),
(1, 'Baleno', 'baleno'),
(1, 'Dzire', 'dzire'),
(1, 'Ertiga', 'ertiga'),
(2, 'i20', 'i20'),
(2, 'Creta', 'creta'),
(2, 'Verna', 'verna'),
(3, 'Nexon', 'nexon'),
(3, 'Punch', 'punch'),
(3, 'Harrier', 'harrier'),
(4, 'Thar', 'thar'),
(4, 'Scorpio', 'scorpio'),
(4, 'XUV700', 'xuv700'),
(5, 'City', 'city'),
(5, 'Amaze', 'amaze'),
(6, 'Fortuner', 'fortuner'),
(6, 'Innova Crysta', 'innova-crysta'),
(7, 'Seltos', 'seltos'),
(7, 'Carens', 'carens');

-- Insert accessory categories
INSERT INTO accessory_categories (name, slug) VALUES
('Seat Covers', 'seat-covers'),
('Music Systems', 'music-systems'),
('Alloy Wheels', 'alloy-wheels'),
('Dash Covers', 'dash-covers'),
('Floor Mats', 'floor-mats'),
('Steering Covers', 'steering-covers'),
('Car Care', 'car-care'),
('LED Lights', 'led-lights');

-- Default settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('whatsapp_number', '919876543210', 'text'),
('whatsapp_message', 'Hello, I am interested in this car: {item_name}. Please share more details.', 'text'),
('site_logo', '', 'image'),
('site_name', 'VAutoSpare', 'text'),
('meta_title', 'VAutoSpare - Buy & Sell Cars | Car Marketplace', 'text'),
('meta_description', 'Find your perfect car. Browse new and used cars, compare prices, and connect with sellers via WhatsApp.', 'text'),
('meta_keywords', 'cars, buy car, sell car, used cars, car marketplace, car dealer', 'text');

-- Default admin user (password: admin123)
-- Password hash for 'admin123' - CHANGE AFTER FIRST LOGIN!
INSERT INTO admin_users (username, email, password, full_name, role) VALUES
('admin', 'admin@VAutoSpare.com', '$2y$10$3uQh/c2g7Yajrn996duNp.pErDO6tu9AE1K.oldTYOK3GwrFu4iM2', 'Administrator', 'super_admin');

-- Sample cars
INSERT INTO cars (brand_id, model_id, variant, year, fuel_type_id, transmission_id, mileage, price, city_id, description, seller_name, seller_phone, status, is_featured) VALUES
(1, 1, 'VXI', 2022, 1, 1, '18 kmpl', 650000, 1, 'Well maintained Maruti Swift. Single owner.', 'Rahul Motors', '9876543210', 'approved', 1),
(2, 5, 'Sportz', 2021, 1, 1, '19 kmpl', 720000, 2, 'Hyundai i20 Sportz. Warranty remaining.', 'Delhi Cars', '9876543211', 'approved', 1),
(3, 8, 'XZ+', 2023, 1, 2, '17 kmpl', 1150000, 3, 'Tata Nexon XZ+ Automatic. 5 star safety.', 'Bangalore Auto', '9876543212', 'approved', 1),
(4, 11, 'LX', 2022, 2, 1, '15 kmpl', 1450000, 4, 'Mahindra Thar 4x4. Adventure ready.', 'Chennai Wheels', '9876543213', 'approved', 1),
(1, 2, 'Alpha', 2020, 1, 1, '22 kmpl', 580000, 5, 'Maruti Baleno Alpha. Fuel efficient.', 'Hyderabad Cars', '9876543214', 'approved', 0),
(5, 14, 'VX', 2021, 1, 2, '18 kmpl', 1250000, 1, 'Honda City VX CVT. Premium sedan.', 'Mumbai Motors', '9876543215', 'approved', 1);

-- Sample accessories
INSERT INTO accessories (name, slug, category_id, price, description, compatible_models, is_available) VALUES
('Premium Leather Seat Covers', 'premium-leather-seat-covers', 1, 15000, 'Handcrafted leather seat covers.', 'Swift, Baleno, i20', 1),
('Touchscreen Music System', 'touchscreen-music-system', 2, 25000, '7 inch Android Auto/Apple CarPlay.', 'Most cars 2015+', 1),
('Alloy Wheels Set 16 inch', 'alloy-wheels-16', 3, 35000, 'Set of 4 premium alloy wheels.', 'Nexon, Creta', 1);

-- ============================================================
-- Dummy product / accessories seed data (with images)
-- Adds 10 sample products spread across all 8 categories.
-- ============================================================

INSERT INTO accessories (name, slug, category_id, brand_id, price, description, compatible_models, is_available, is_featured, is_hot_sold, is_new, product_status, display_order) VALUES
('Art Leather Seat Covers (Beige)', 'art-leather-seat-covers-beige', 1, 1, 7499, 'Premium art leather seat covers with foam padding. Custom stitched fit, airbag compatible and easy to clean. Comes as a full set with headrest covers.', 'Swift, Baleno, i20, Creta', 1, 1, 1, 0, 'available', 1),
('9 Inch Android Music System', 'android-music-system-9-inch', 2, 2, 12999, '9 inch HD touchscreen stereo with wireless Apple CarPlay & Android Auto, built-in GPS navigation, Bluetooth 5.0, steering wheel control support and reverse camera input.', 'Most cars 2015 and above', 1, 1, 0, 1, 'available', 2),
('Diamond Cut Alloy Wheels 16 Inch (Set of 4)', 'diamond-cut-alloy-wheels-16', 3, 6, 28500, 'Set of 4 machine finished diamond cut alloy wheels. Lightweight aluminium construction, corrosion resistant coating and perfect OEM fitment.', 'Nexon, Creta, Seltos', 1, 1, 0, 0, 'available', 3),
('Custom Fit Dashboard Cover (Charcoal)', 'custom-fit-dashboard-cover', 4, 3, 1499, 'Charcoal coloured custom fit dashboard cover. UV and heat resistant, anti-glare surface protects your dash from cracking and fading.', 'Nexon, Harrier, Thar', 1, 0, 0, 1, 'available', 4),
('5D Waterproof Floor Mats (Set of 5)', '5d-waterproof-floor-mats', 5, 4, 2299, 'Set of 5 deep dish 5D floor mats. 100 percent waterproof with raised edges, anti-skid backing and easy to wipe clean surface.', 'Thar, Scorpio, XUV700', 1, 1, 1, 1, 'available', 5),
('Microfiber Leather Steering Cover', 'microfiber-steering-cover', 6, 5, 649, 'Anti-slip microfiber leather steering cover. Breathable, sweat absorbing and fits all standard 38 cm steering wheels. Stitched by hand for a premium feel.', 'City, Amaze, i20', 1, 0, 1, 0, 'available', 6),
('Ceramic Coating Wax Polish Kit', 'ceramic-coating-wax-kit', 7, 6, 1899, '9H ceramic coating wax kit with microfiber towels and foam applicator. Gives 6 months of mirror shine and hydrophobic paint protection.', 'All cars', 1, 0, 0, 1, 'available', 7),
('LED Headlight Bulbs H4 6000K (Pair)', 'led-headlight-bulbs-h4', 8, 7, 2499, 'Pair of 6000K crystal white LED headlight bulbs. 12000 lumens brightness, IP68 waterproof, 50000 hour life and plug and play installation.', 'Seltos, Carens, i20', 1, 1, 1, 0, 'available', 8),
('Portable Digital Tyre Inflator 150PSI', 'portable-tyre-inflator-150psi', 7, 4, 3299, 'Digital tyre inflator with backlit gauge, auto cut-off at preset pressure, emergency LED light and 3 metre power cord. Inflates a tyre in under 3 minutes.', 'All cars', 1, 0, 0, 1, 'available', 9),
('Ambient RGB Interior LED Strip Kit', 'ambient-rgb-led-strip-kit', 8, 7, 1799, 'Bluetooth app controlled RGB ambient light strips with music sync mode. 16 million colours, multiple scene modes and flexible EL wire installation.', 'Seltos, Creta, Fortuner', 1, 0, 0, 1, 'available', 10);

-- Primary image for each dummy product (matched by slug so ids line up on any install)
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'art-leather-seat-covers-beige';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'android-music-system-9-inch';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'diamond-cut-alloy-wheels-16';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'custom-fit-dashboard-cover';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = '5d-waterproof-floor-mats';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'microfiber-steering-cover';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'ceramic-coating-wax-kit';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'led-headlight-bulbs-h4';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'portable-tyre-inflator-150psi';
INSERT INTO accessory_images (accessory_id, image_path, is_primary, display_order)
SELECT id, 'assets/images/placeholder-product.svg', 1, 0 FROM accessories WHERE slug = 'ambient-rgb-led-strip-kit';
