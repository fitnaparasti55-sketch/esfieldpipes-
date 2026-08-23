-- ========================================================
-- Esfield Pipe - Admin Panel & Settings Migration Script
-- Safe migration: preserves all existing products, categories, and orders.
-- ========================================================

USE `esfield_pipe`;

-- 1. Create Media Table if not exists
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` INT NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `media_type` VARCHAR(50) DEFAULT 'image',
    `alt_text` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insert or update default settings for dynamic website features
INSERT INTO `settings` (`key_name`, `key_value`) VALUES
-- Logos & Branding
('site_logo', 'assets/images/logo.svg'),
('site_logo_mobile', 'assets/images/logo.svg'),
('site_favicon', 'assets/images/logo.svg'),

-- Company Info & Contact
('site_name', 'Esfield Pipe Pvt. Ltd.'),
('site_tagline', 'Premium Double Wall Corrugated (DWC) HDPE Pipes & Infrastructure Solutions'),
('site_email', 'sales@esfieldpipe.com'),
('site_phone', '+91 98765 43210'),
('site_phone_alt', '+91 11 2345 6789'),
('site_whatsapp', '+91 98765 43210'),
('site_address', 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India'),
('site_url', 'http://localhost/ES%20Field%20Pipe/'),
('gstin', '07AABCE9876F1Z4'),
('pan_number', 'AABCE9876F'),
('cin_number', 'U25209DL2018PTC334567'),
('bis_info', 'BIS IS:16098 (Part-2) Certified Manufacturer'),
('footer_about', 'Esfield Pipe is India\'s premier manufacturer of high-density polyethylene Double Wall Corrugated (DWC) pipes conforming to IS 16098 (Part 2) & EN 13476 standards. Delivering reliable non-pressure gravity flow solutions for Smart Cities, Highways, Sewerage, and Telecom networks.'),
('facebook_url', 'https://facebook.com/esfieldpipe'),
('linkedin_url', 'https://linkedin.com/company/esfieldpipe'),
('twitter_url', 'https://twitter.com/esfieldpipe'),
('instagram_url', 'https://instagram.com/esfieldpipe'),
('youtube_url', 'https://youtube.com/@esfieldpipe'),

-- Theme & Appearance Customization (CSS Variables)
('theme_primary_color', '#ea580c'),
('theme_primary_hover', '#c2410c'),
('theme_secondary_color', '#0284c7'),
('theme_secondary_hover', '#0369a1'),
('theme_accent_color', '#06b6d4'),
('theme_bg_body', '#f8fafc'),
('theme_text_main', '#0f172a'),
('theme_header_bg', '#ffffff'),
('theme_topbar_bg', '#0f172a'),
('theme_footer_bg', '#0f172a'),
('theme_btn_color', '#ea580c'),
('theme_btn_hover_color', '#c2410c'),
('theme_border_radius', '8px'),
('theme_font_family', 'Inter, sans-serif'),

-- Homepage Hero Section
('home_hero_badge', 'BIS IS:16098 (Part-2) & EN 13476 Certified'),
('home_hero_heading', 'Engineered Strength. High-Flow DWC HDPE Piping Systems.'),
('home_hero_subheading', 'Manufactured with 100% virgin PE-100 polymers for underground gravity drainage, highway culverts, municipal sewerage, and telecom cable ducting with 50+ year design life.'),
('home_hero_image', 'assets/images/dwc-cross-section.svg'),
('home_hero_btn1_text', 'Explore Pipe Catalog'),
('home_hero_btn1_url', 'products.php'),
('home_hero_btn2_text', 'Sizing Calculator'),
('home_hero_btn2_url', 'pipe-calculator.php'),
('home_stat1_number', '50-1200'),
('home_stat1_label', 'mm Diameters'),
('home_stat2_number', 'SN8'),
('home_stat2_label', 'Ring Stiffness'),
('home_stat3_number', '50+ Yrs'),
('home_stat3_label', 'Service Lifespan'),

-- Homepage Company / About Section
('home_company_heading', 'Pioneering Heavy Infrastructure & Drainage Technology'),
('home_company_subheading', 'Precision Engineered Double Wall Corrugated HDPE Pipes'),
('home_company_desc', 'Esfield Pipe provides advanced structural wall piping designed to withstand severe dynamic axle loading, seismic movement, and chemical aggression in municipal and industrial projects.'),
('home_company_image', 'assets/images/dwc-cross-section.svg'),

-- Homepage Products Section
('home_products_heading', 'Featured Infrastructure Pipe Systems'),
('home_products_desc', 'Explore our BIS IS:16098 Part 2 certified structured wall DWC pipes available in standard 6.0m lengths with integrated socket couplers.'),
('home_products_count', '6'),

-- Homepage CTA Banner
('home_cta_heading', 'Ready to Upgrade Your Pipeline Infrastructure?'),
('home_cta_desc', 'Contact our engineering sales department for project-specific sizing, factory inspections, or bulk institutional quotation tenders.'),
('home_cta_btn_text', 'Request Bulk RFQ Quote'),
('home_cta_btn_url', 'contact.php'),

-- SEO Configuration
('meta_title', 'Esfield Pipe | DWC HDPE Pipes & Infrastructure Solutions'),
('meta_description', 'India\'s premier manufacturer of Double Wall Corrugated (DWC) HDPE pipes conforming to IS 16098 Part-2 and EN 13476 for sewerage, drainage, and telecom ducting.'),
('meta_keywords', 'DWC pipe, HDPE corrugated pipe, IS 16098 Part 2, structured wall pipe, sewerage pipe, culvert pipe, SN8 stiffness, telecom duct'),
('home_seo_title', 'Esfield Pipe - High Flow DWC HDPE Corrugated Pipes'),
('home_seo_description', 'Manufacturer of DWC HDPE pipes 50mm to 1200mm for municipal drainage, smart cities, highway culverts and cable ducting.'),
('og_image', 'assets/images/logo.svg'),
('robots_indexing', 'index, follow')
ON DUPLICATE KEY UPDATE `key_name` = `key_name`;

-- 3. Ensure users table has updated_at and reset fields if not already present
-- (Safe alters)
