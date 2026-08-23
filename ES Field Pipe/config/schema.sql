-- Database Schema for Esfield Pipe - DWC Pipe Manufacturing & eCommerce Platform
CREATE DATABASE IF NOT EXISTS `esfield_pipe` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `esfield_pipe`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `product_screenshots`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `support_inquiries`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor', 'customer') DEFAULT 'customer',
    `phone` VARCHAR(20) NULL,
    `company_name` VARCHAR(150) NULL,
    `gstin` VARCHAR(30) NULL,
    `address` TEXT NULL,
    `city` VARCHAR(100) NULL,
    `state` VARCHAR(100) NULL,
    `pincode` VARCHAR(20) NULL,
    `status` ENUM('active', 'blocked') DEFAULT 'active',
    `reset_token` VARCHAR(100) NULL,
    `reset_expires` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories Table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-solid fa-water-ladder',
    `display_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table (DWC Corrugated Pipes)
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `short_desc` VARCHAR(300) NULL,
    `description` LONGTEXT NULL,
    `inner_diameter_mm` INT NOT NULL,
    `outer_diameter_mm` INT NOT NULL,
    `standard_length_m` DECIMAL(5,2) DEFAULT 6.00,
    `stiffness_class` ENUM('SN4', 'SN8') DEFAULT 'SN8',
    `raw_material` VARCHAR(100) DEFAULT 'High-Density Polyethylene (HDPE PE-100)',
    `standard_compliance` VARCHAR(150) DEFAULT 'IS 16098 (Part 2) / EN 13476',
    `jointing_method` VARCHAR(100) DEFAULT 'Coupler with Elastomeric EPDM Rubber Ring',
    `application_type` VARCHAR(150) DEFAULT 'Underground Non-Pressure Gravity Drainage',
    `price_per_meter` DECIMAL(10,2) NOT NULL,
    `price_per_pipe` DECIMAL(10,2) NOT NULL,
    `stock_quantity` INT DEFAULT 500,
    `min_order_qty` INT DEFAULT 1,
    `featured` TINYINT(1) DEFAULT 0,
    `image` VARCHAR(255) NULL,
    `gallery_images` TEXT NULL,
    `spec_sheet_pdf` VARCHAR(255) NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `views_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart Table
CREATE TABLE `cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `session_id` VARCHAR(100) NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `pipe_length_m` DECIMAL(5,2) DEFAULT 6.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coupons Table
CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
    `discount_value` DECIMAL(10,2) NOT NULL,
    `min_order_amount` DECIMAL(10,2) DEFAULT 0.00,
    `max_discount` DECIMAL(10,2) NULL,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `usage_limit` INT DEFAULT 1000,
    `usage_count` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT NULL,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `company_name` VARCHAR(150) NULL,
    `gstin` VARCHAR(30) NULL,
    `shipping_address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `pincode` VARCHAR(20) NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `tax_amount` DECIMAL(12,2) NOT NULL,
    `discount_amount` DECIMAL(12,2) DEFAULT 0.00,
    `shipping_charge` DECIMAL(12,2) DEFAULT 0.00,
    `total_amount` DECIMAL(12,2) NOT NULL,
    `coupon_code` VARCHAR(50) NULL,
    `payment_method` ENUM('razorpay', 'stripe', 'bank_transfer', 'cod') DEFAULT 'bank_transfer',
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    `transaction_id` VARCHAR(100) NULL,
    `order_status` ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `tracking_number` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NULL,
    `product_name` VARCHAR(200) NOT NULL,
    `inner_diameter_mm` INT DEFAULT 0,
    `outer_diameter_mm` INT DEFAULT 0,
    `stiffness_class` VARCHAR(20) DEFAULT 'SN8',
    `unit_price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `pipe_length_m` DECIMAL(5,2) DEFAULT 6.00,
    `total_price` DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Support Inquiries & Quotes Table
CREATE TABLE `support_inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `company` VARCHAR(150) NULL,
    `subject` VARCHAR(200) NOT NULL,
    `inquiry_type` ENUM('quote', 'technical', 'support', 'bulk', 'general') DEFAULT 'quote',
    `pipe_requirement` TEXT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    `admin_reply` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FAQs Table
CREATE TABLE `faqs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(50) DEFAULT 'General',
    `question` VARCHAR(300) NOT NULL,
    `answer` TEXT NOT NULL,
    `display_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings Table (Key-Value)
CREATE TABLE `settings` (
    `key_name` VARCHAR(100) PRIMARY KEY,
    `key_value` LONGTEXT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Default Settings
INSERT INTO `settings` (`key_name`, `key_value`) VALUES
('site_name', 'Esfield Pipe Pvt. Ltd.'),
('site_tagline', 'Premium Double Wall Corrugated (DWC) HDPE Pipes & Infrastructure Solutions'),
('site_email', 'sales@esfieldpipe.com'),
('site_phone', '+91 98765 43210 / +91 11 2345 6789'),
('site_address', 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India'),
('site_currency', '₹'),
('site_gst_rate', '18'),
('gstin', '07AABCE9876F1Z4'),
('pan_number', 'AABCE9876F'),
('cin_number', 'U25209DL2018PTC334567'),
('razorpay_key_id', 'rzp_test_esfield_live_2026'),
('razorpay_key_secret', 'rzp_test_sec_991823712'),
('stripe_publishable_key', 'pk_test_51MzEsfieldDwcSamplePublishableKey'),
('stripe_secret_key', 'sk_test_51MzEsfieldDwcSampleSecretKey'),
('footer_about', 'Esfield Pipe is India\'s premier manufacturer of high-density polyethylene Double Wall Corrugated (DWC) pipes conforming to IS 16098 (Part 2) & EN 13476 standards. Delivering reliable non-pressure gravity flow solutions for Smart Cities, Highways, Sewerage, and Telecom networks.'),
('facebook_url', 'https://facebook.com/esfieldpipe'),
('linkedin_url', 'https://linkedin.com/company/esfieldpipe'),
('twitter_url', 'https://twitter.com/esfieldpipe');

-- Default Users
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `company_name`, `gstin`, `address`, `city`, `state`, `pincode`, `status`) VALUES
(1, 'Super Administrator', 'admin@esfieldpipe.com', '$2y$10$uMzjelAG9Uqxyr1MCVx0.edFKSVefItJa7yJBC5dQRbRewKGwXci.', 'admin', '+91 98765 00001', 'Esfield Pipe Headquarters', '07AABCE9876F1Z4', 'Plot 42-45 Industrial Mega Park', 'New Delhi', 'Delhi', '110001', 'active'),
(2, 'Operations Editor', 'editor@esfieldpipe.com', '$2y$10$9pGeaRCICo/nasu.Gmt/EuSaIFScVh0CDg5L5YFEiaBYEh5BYj19S', 'editor', '+91 98765 00002', 'Esfield Pipe Sales Dept', '07AABCE9876F1Z4', 'Plot 42-45 Industrial Mega Park', 'New Delhi', 'Delhi', '110001', 'active'),
(3, 'John Infrastructure Contractors', 'john@example.com', '$2y$10$TVhoK2WI3/tixIp2fQjAleh2GUB6I4kd42xuLkK3wHYT787.hHSG6', 'customer', '+91 98111 22233', 'Apex Civil Infrastructure Ltd', '27AAACA9999P1ZV', 'Sector 18, Commercial Hub', 'Mumbai', 'Maharashtra', '400051', 'active');

-- Default Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `display_order`, `status`) VALUES
(1, 'Underground Drainage & Sewerage', 'underground-drainage-sewerage', 'Structured wall DWC HDPE pipes engineered with smooth internal wall for high hydraulic flow and corrugated outer profile for heavy soil load resistance.', 'fa-solid fa-water-ladder', 1, 'active'),
(2, 'Telecom & Power Cable Ducting', 'telecom-cable-ducting', 'High crush resistance DWC ducting pipes with anti-rodent properties and nylon pull rope for effortless optical fiber and high-voltage power cable pulling.', 'fa-solid fa-bolt-lightning', 2, 'active'),
(3, 'Highway & Railway Culverts', 'highway-railway-culverts', 'SN8 heavy-duty corrugated pipes designed to endure dynamic axle load vibration and vehicular stress on expressways, railways, and airport runways.', 'fa-solid fa-road', 3, 'active'),
(4, 'Industrial Effluent & Chemical Waste', 'industrial-effluent-pipes', 'Chemically inert PE-100 structured pipes with superior abrasion resistance against acidic, alkaline, and high-temperature industrial effluents.', 'fa-solid fa-industry', 4, 'active'),
(5, 'Rainwater Harvesting & Stormwater', 'stormwater-rainwater-harvesting', 'Large diameter high-velocity stormwater collection pipes for smart city urban drainage, flood control, and groundwater recharge recharge chambers.', 'fa-solid fa-cloud-showers-heavy', 5, 'active'),
(6, 'Subsurface Agricultural & Airport Drainage', 'agricultural-subsurface-drainage', 'Perforated and non-perforated DWC corrugated piping systems for groundwater table regulation, athletic fields, and railway track sub-drainage.', 'fa-solid fa-seedling', 6, 'active');

-- Default DWC Pipe Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_desc`, `description`, `inner_diameter_mm`, `outer_diameter_mm`, `standard_length_m`, `stiffness_class`, `raw_material`, `standard_compliance`, `jointing_method`, `application_type`, `price_per_meter`, `price_per_pipe`, `stock_quantity`, `min_order_qty`, `featured`, `image`, `status`, `views_count`) VALUES
(1, 1, 'Esfield 100mm ID (120mm OD) DWC Corrugated Sewerage Pipe', 'esfield-100mm-dwc-sewerage-pipe', 'IS 16098 Part-2 compliant 100mm nominal internal diameter DWC HDPE pipe with SN8 stiffness rating.', 'The Esfield 100mm Inner Diameter DWC pipe is manufactured using 100% virgin grade PE-100 High-Density Polyethylene. Featuring a corrugated black exterior for superior ring stiffness and a smooth yellow/blue interior for laminar fluid flow and zero silt deposition.\n\nKey Advantages:\n- 50+ Years Service Life\n- 100% Watertight EPDM Ring Joints\n- Light weight for rapid trench installation\n- Non-reactive to H2S sewer gases and acidic soil conditions.', 100, 120, 6.00, 'SN8', 'PE-100 Virgin Grade HDPE', 'IS 16098 (Part 2) / EN 13476-3', 'Integrated Socket & Spigot with EPDM Seal', 'Municipal Sewerage & House Connection', 185.00, 1110.00, 1200, 1, 1, 'assets/images/dwc-pipe-100mm.svg', 'active', 340),

(2, 1, 'Esfield 150mm ID (175mm OD) DWC Underground Drainage Pipe', 'esfield-150mm-dwc-drainage-pipe', 'High efficiency 150mm ID DWC pipe ideal for municipal stormwater connections, lateral drains, and commercial facilities.', 'Engineered for optimal non-pressure gravity flow, the Esfield 150mm ID DWC pipe replaces brittle RCC pipes. It offers seamless flexibility to adjust with ground settlement without cracking or joint displacement.\n\nKey Specs:\n- Manning\'s Roughness Coefficient n = 0.009–0.010\n- High Impact Resistance (Charpy tested)\n- Resistant to root intrusion and biological growth.', 150, 175, 6.00, 'SN8', 'PE-100 Virgin Grade HDPE', 'IS 16098 (Part 2) / EN 13476-3', 'Snap-fit Coupler with Twin Elastomeric Seals', 'Underground Stormwater & Main Lateral Drainage', 320.00, 1920.00, 850, 1, 1, 'assets/images/dwc-pipe-150mm.svg', 'active', 512),

(3, 1, 'Esfield 200mm ID (235mm OD) Heavy-Duty DWC Sewer Pipe', 'esfield-200mm-dwc-sewer-pipe', 'Standard 200mm ID municipal sewer pipe with SN8 rating for urban underground drainage networks.', 'Esfield 200mm ID DWC pipe is widely specified across Smart City infrastructure and town municipal councils. Engineered to withstand heavy traffic loads even at shallow trench depths.\n\nBenefits:\n- Eliminates heavy lifting crane requirements during installation\n- Smooth inner wall prevents slime and sediment build-up\n- Resists scouring at fluid velocities up to 8 m/s.', 200, 235, 6.00, 'SN8', 'PE-100 Virgin Grade HDPE', 'IS 16098 (Part 2) / ISO 21138', 'Socket-Spigot with EPDM Lip Seal Ring', 'Town Sewer Trunk & Industrial Drainage', 540.00, 3240.00, 600, 1, 1, 'assets/images/dwc-pipe-200mm.svg', 'active', 780),

(4, 1, 'Esfield 250mm ID (290mm OD) DWC Corrugated Drainage Pipe', 'esfield-250mm-dwc-drainage-pipe', 'Premium 250mm internal diameter structured wall corrugated HDPE pipe for high volume stormwater discharge.', 'Designed for intensive stormwater drainage and industrial sewer headers. High ring stiffness SN8 rating allows installation under heavy roadway corridors and parking structures.', 250, 290, 6.00, 'SN8', 'PE-100 Virgin Grade HDPE', 'IS 16098 (Part 2) / EN 13476', 'Elastomeric Gasketed Coupler', 'Stormwater Outfall & Commercial Complex Drainage', 780.00, 4680.00, 450, 1, 0, 'assets/images/dwc-pipe-250mm.svg', 'active', 290),

(5, 1, 'Esfield 300mm ID (350mm OD) Large Bore DWC Sewerage Pipe', 'esfield-300mm-dwc-sewerage-pipe', '300mm ID trunk sewer pipe capable of handling high discharge velocity with zero leak joints.', 'Our flagship 300mm ID DWC pipe provides 40% higher hydraulic capacity compared to same diameter concrete pipes due to its ultra-low hydraulic friction coefficient.\n\nFeatures:\n- Ring stiffness >= 8 kN/m²\n- Joint deflection tolerance up to 3 degrees\n- Zero environmental contamination from exfiltration.', 300, 350, 6.00, 'SN8', 'PE-100 Virgin Grade HDPE', 'IS 16098 (Part 2) / EN 13476', 'Integral Bell & Spigot Joint', 'City Trunk Sewer & Highway Cross Drains', 1150.00, 6900.00, 350, 1, 1, 'assets/images/dwc-pipe-300mm.svg', 'active', 920),

(6, 3, 'Esfield 400mm ID (460mm OD) Highway Culvert DWC Pipe', 'esfield-400mm-highway-culvert-dwc-pipe', 'Heavy-duty 400mm ID SN8 corrugated pipe for national highway cross-culverts and railway embankments.', 'Designed specifically for NHAI, State PWD, and Railway infrastructure. Handles dynamic cyclic vehicle loading up to 45 tonnes axle weight with optimal soil-pipe interaction mechanics.\n\nKey Specs:\n- Meets AASHTO M294 & IS 16098 requirements\n- Withstands extreme temperature fluctuations (-40°C to +60°C)\n- Extreme abrasion resistance against sand, gravel and silt bedload.', 400, 460, 6.00, 'SN8', 'High Molecular Weight HDPE PE-100', 'IS 16098 (Part 2) / AASHTO M294', 'Reinforced Collar Coupler with Dual EPDM Rings', 'Highway Cross Drains, Flyover Abutments & Culverts', 1850.00, 11100.00, 220, 1, 1, 'assets/images/dwc-pipe-400mm.svg', 'active', 640),

(7, 3, 'Esfield 500mm ID (580mm OD) Ultra-Span Highway DWC Pipe', 'esfield-500mm-highway-dwc-pipe', 'High capacity 500mm ID DWC pipe for mega expressway drainage and airport runway stormwater management.', 'Provides massive stormwater conveyance volume while dramatically reducing construction time compared to cast-in-situ box culverts or NP3/NP4 concrete pipes.', 500, 580, 6.00, 'SN8', 'PE-100 Extra High Density Polyethylene', 'IS 16098 (Part 2) / EN 13476', 'Integral Reinforced Bell and Spigot with Locking Gasket', 'Expressway Culverts, Airport Runways, Major Outfalls', 2900.00, 17400.00, 180, 1, 1, 'assets/images/dwc-pipe-500mm.svg', 'active', 430),

(8, 3, 'Esfield 600mm ID (700mm OD) Mega Flow DWC Culvert Pipe', 'esfield-600mm-mega-flow-dwc-culvert-pipe', '600mm ID mega structured wall DWC pipe engineered for major municipal outfall canals and river crossings.', 'The ultimate solution for high volume gravity drainage networks. Outperforms concrete in structural flexibility, installation speed, and long-term chemical resistance.', 600, 700, 6.00, 'SN8', 'HDPE PE-100 Polyethylene', 'IS 16098 (Part 2) / ISO 21138', 'Integral Electrofusion / Gasketed Bell Joint', 'River Diversion, City Outfall Canal, Railway Culverts', 4200.00, 25200.00, 110, 1, 1, 'assets/images/dwc-pipe-600mm.svg', 'active', 810),

(9, 2, 'Esfield 50mm ID (63mm OD) DWC Telecom Cable Duct', 'esfield-50mm-dwc-telecom-duct', 'Compact 50mm ID corrugated ducting pipe with pre-installed nylon draw string for telecom OFC cables.', 'Specially engineered for fiber optic networks, Smart City surveillance cameras, and metro rail signaling cables. Features high crush strength, corrugated outer skin, and ultra-smooth low friction inner wall for long distance cable blowing.', 50, 63, 6.00, 'SN8', 'HDPE with Anti-Rodent Additives', 'BS EN 50086 / TEC Specs', 'Push-Fit Coupler with Snap Locking', 'Optical Fiber (OFC) Ducting & 5G Towers', 85.00, 510.00, 2500, 5, 1, 'assets/images/dwc-pipe-50mm.svg', 'active', 490),

(10, 2, 'Esfield 75mm ID (90mm OD) Power Cable Protection DWC Duct', 'esfield-75mm-dwc-power-cable-duct', '75mm ID DWC duct pipe designed for underground high-voltage power transmission cable protection.', 'Protects HT/LT power distribution cables from road traffic vibration, soil movement, and accidental excavation strikes. Fire retardant and non-conductive dielectric properties.', 75, 90, 6.00, 'SN8', 'HDPE with UV Stabilizer', 'IS 14930 (Part 2) / IEC 61386', 'Coupler with Neoprene Gasket', 'Underground Power Grid & Solar Park Cabling', 130.00, 780.00, 1800, 2, 0, 'assets/images/dwc-pipe-75mm.svg', 'active', 310),

(11, 4, 'Esfield 300mm ID Chemical Effluent Resistant DWC Pipe', 'esfield-300mm-chemical-effluent-dwc-pipe', 'Industrial grade chemical resistant DWC pipe with 100% virgin PE-100 resin for pharmaceutical and chemical CETP plants.', 'Engineered to convey high temperature acidic, caustic, and abrasive effluents in Common Effluent Treatment Plants (CETP) and chemical manufacturing zones without corrosion or leaching.', 300, 350, 6.00, 'SN8', 'PE-100 Virgin High-Density Resin', 'IS 16098 (Part 2) / ASTM F894', 'Chemical Grade Viton/EPDM Gasket Joint', 'CETP Effluent Lines, Chemical & Petrochemical Plants', 1350.00, 8100.00, 190, 1, 0, 'assets/images/dwc-pipe-300mm.svg', 'active', 215),

(12, 5, 'Esfield 200mm ID Perforated Subsurface DWC Recharge Pipe', 'esfield-200mm-perforated-dwc-recharge-pipe', 'Perforated corrugated DWC pipe with geotextile filtration compatibility for groundwater recharge and sports turf drainage.', 'Features precision slotted water intake perforations along the pipe valley corrugations. Rapidly collects groundwater runoff into rainwater harvesting pits without sand infiltration.', 200, 235, 6.00, 'SN4', 'Recyclable Virgin HDPE', 'IS 16098 (Part 2) / DIN 4262', 'Snap Coupler with Filter Geotextile Sleeve', 'Rainwater Harvesting Infiltration & Highway Sub-drainage', 490.00, 2940.00, 420, 1, 1, 'assets/images/dwc-pipe-200mm.svg', 'active', 375);

-- Default Coupons
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `start_date`, `end_date`, `usage_limit`, `usage_count`, `status`) VALUES
(1, 'DWC10', 'percentage', 10.00, 5000.00, 5000.00, '2026-01-01', '2027-12-31', 500, 12, 'active'),
(2, 'INFRA500', 'fixed', 500.00, 10000.00, 500.00, '2026-01-01', '2027-12-31', 200, 4, 'active'),
(3, 'BULK15', 'percentage', 15.00, 50000.00, 25000.00, '2026-01-01', '2027-12-31', 100, 2, 'active'),
(4, 'WELCOME200', 'fixed', 200.00, 2000.00, 200.00, '2026-01-01', '2027-12-31', 1000, 28, 'active');

-- Sample Orders for Dashboard & History Demonstration
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `company_name`, `gstin`, `shipping_address`, `city`, `state`, `pincode`, `subtotal`, `tax_amount`, `discount_amount`, `shipping_charge`, `total_amount`, `coupon_code`, `payment_method`, `payment_status`, `transaction_id`, `order_status`, `tracking_number`, `notes`, `created_at`) VALUES
(1, 'ORD-2026-8801', 3, 'John Infrastructure Contractors', 'john@example.com', '+91 98111 22233', 'Apex Civil Infrastructure Ltd', '27AAACA9999P1ZV', 'Plot 88, Metro Corridor Site, Andheri East', 'Mumbai', 'Maharashtra', '400069', 32400.00, 5832.00, 3240.00, 0.00, 34992.00, 'DWC10', 'razorpay', 'paid', 'pay_NzkX918239aBc', 'delivered', 'DTDC-992817263', 'Urgent delivery for Metro Line 7 drainage package.', DATE_SUB(NOW(), INTERVAL 12 DAY)),

(2, 'ORD-2026-8802', 3, 'John Infrastructure Contractors', 'john@example.com', '+91 98111 22233', 'Apex Civil Infrastructure Ltd', '27AAACA9999P1ZV', 'Plot 88, Metro Corridor Site, Andheri East', 'Mumbai', 'Maharashtra', '400069', 55500.00, 9990.00, 5000.00, 0.00, 60490.00, 'DWC10', 'bank_transfer', 'paid', 'NEFT-HDFC-9918231', 'shipped', 'VRL-77281920', 'Dispatched via 32ft Container trailer from Delhi Works.', DATE_SUB(NOW(), INTERVAL 4 DAY)),

(3, 'ORD-2026-8803', 3, 'John Infrastructure Contractors', 'john@example.com', '+91 98111 22233', 'Apex Civil Infrastructure Ltd', '27AAACA9999P1ZV', 'Plot 88, Metro Corridor Site, Andheri East', 'Mumbai', 'Maharashtra', '400069', 13800.00, 2484.00, 500.00, 0.00, 15784.00, 'INFRA500', 'stripe', 'paid', 'ch_3NzkL8812901aBc', 'processing', 'TRK-PENDING', 'Testing batch with factory inspection certificate requested.', DATE_SUB(NOW(), INTERVAL 1 DAY)),

(4, 'ORD-2026-8804', NULL, 'Vikram Sharma', 'vikram.smartcity@gmail.com', '+91 98222 33445', 'Sharma Earthmovers', '07BBBP8821M1Z1', 'Civil Lines Road, Near District Court', 'Jaipur', 'Rajasthan', '302006', 6900.00, 1242.00, 200.00, 500.00, 8442.00, 'WELCOME200', 'cod', 'pending', NULL, 'pending', NULL, 'Direct site delivery with unloader crane.', NOW());

-- Order Items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `inner_diameter_mm`, `outer_diameter_mm`, `stiffness_class`, `unit_price`, `quantity`, `pipe_length_m`, `total_price`) VALUES
(1, 1, 3, 'Esfield 200mm ID (235mm OD) Heavy-Duty DWC Sewer Pipe', 200, 235, 'SN8', 3240.00, 10, 6.00, 32400.00),
(2, 2, 6, 'Esfield 400mm ID (460mm OD) Highway Culvert DWC Pipe', 400, 460, 'SN8', 11100.00, 5, 6.00, 55500.00),
(3, 3, 5, 'Esfield 300mm ID (350mm OD) Large Bore DWC Sewerage Pipe', 300, 350, 'SN8', 6900.00, 2, 6.00, 13800.00),
(4, 4, 5, 'Esfield 300mm ID (350mm OD) Large Bore DWC Sewerage Pipe', 300, 350, 'SN8', 6900.00, 1, 6.00, 6900.00);

-- Sample Support Inquiries
INSERT INTO `support_inquiries` (`id`, `user_id`, `name`, `email`, `phone`, `company`, `subject`, `inquiry_type`, `pipe_requirement`, `message`, `status`, `admin_reply`) VALUES
(1, 3, 'John Infrastructure Contractors', 'john@example.com', '+91 98111 22233', 'Apex Civil Infrastructure Ltd', 'Bulk Quotation for 5000m of 300mm SN8 DWC Pipe', 'quote', '300mm ID SN8 DWC Pipe - 5000 Meters', 'We are bidding for the Smart City drainage phase 3 package. Need firm ex-factory pricing and delivery schedule for 5,000 meters including EPDM rubber gaskets.', 'resolved', 'Formal techno-commercial quotation #Q-2026-991 sent to your email with bulk 18% slab discount.'),
(2, NULL, 'Rajesh Kumar Verma', 'rajesh.verma@infraprojects.co.in', '+91 97777 88899', 'Verma Highway Constructions', 'Technical Specification Sheet for 600mm DWC Culvert Pipe', 'technical', '600mm ID DWC Pipe for Highway Culverts', 'Please share the ring stiffness test report and IS 16098 Part 2 third-party BIS lab certificate for 600mm pipe.', 'in_progress', 'Our chief quality engineer has dispatched the NABL accredited test reports to your email.'),
(3, NULL, 'Ananya Sen', 'ananya.s@telecomlink.net', '+91 98300 11223', 'National Telecom Infrastructure', '50mm & 75mm DWC Ducts for 5G OFC Corridor', 'bulk', '50mm (10,000m) and 75mm (5,000m) DWC Ducts', 'Looking for immediate dispatch of 50mm yellow stripe DWC ducts with pre-inserted nylon draw wire in 6-meter lengths.', 'new', NULL);

-- Default FAQs
INSERT INTO `faqs` (`id`, `category`, `question`, `answer`, `display_order`, `status`) VALUES
(1, 'Product & Technical', 'What is a DWC (Double Wall Corrugated) Pipe?', 'DWC pipe is an engineered high-density polyethylene (HDPE) pipe structured with two walls: a corrugated outer wall that provides high ring stiffness (SN4/SN8) to resist heavy earth and traffic loads, and a mirror-smooth inner wall that ensures maximum hydraulic flow velocity and zero sediment accumulation.', 1, 'active'),
(2, 'Comparison', 'How does DWC Pipe compare to traditional RCC (Reinforced Concrete) pipe?', 'DWC pipes are 90% lighter than RCC pipes, requiring no heavy cranes for handling or trench installation. They are completely resistant to acidic sewage gases (H2S), chemically inert, provide watertight elastomeric seal joints preventing root intrusion, and have an estimated service lifespan exceeding 50 to 100 years.', 2, 'active'),
(3, 'Standards & Quality', 'What standards do Esfield DWC pipes conform to?', 'All Esfield DWC pipes strictly comply with Bureau of Indian Standards (BIS) IS 16098 (Part 2): 2013 and International European Standard EN 13476-3 for structured wall piping systems.', 3, 'active'),
(4, 'Installation', 'What is the standard length of Esfield DWC pipes and jointing method?', 'Standard pipe lengths are 6.0 meters (custom lengths up to 12 meters available for mega projects). Jointing is achieved via push-fit integrated socket-spigot couplers fitted with EPDM elastomeric lip seal rubber rings for 100% leak-proof connection.', 4, 'active'),
(5, 'Sizing & Selection', 'What is the difference between SN4 and SN8 stiffness classes?', 'SN4 pipes have a nominal ring stiffness of >= 4 kN/m² and are suitable for non-traffic or moderate depth underground drainage. SN8 pipes have a ring stiffness of >= 8 kN/m² and are mandatory for heavy vehicular traffic roads, expressways, railway culverts, and deep trench installations.', 5, 'active'),
(6, 'Orders & Delivery', 'How do you handle freight, delivery, and bulk orders across India?', 'We maintain nationwide dispatch logistics via dedicated flatbed trailers and container trucks. Deliveries for standard catalog sizes are typically fulfilled within 24 to 72 hours of purchase confirmation.', 6, 'active'),
(7, 'Billing & Taxes', 'Are prices inclusive of GST? Can I claim B2B Input Tax Credit (ITC)?', 'Prices are listed ex-works with 18% GST calculated transparently at checkout. You can enter your company Name and valid GSTIN on the checkout page to receive a formal GST Tax Invoice with full Input Tax Credit eligibility.', 7, 'active');
