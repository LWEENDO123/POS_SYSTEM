-- ============================================================
--  POS SYSTEM - DUMMY DATA FOR TESTING
-- ============================================================
--  HOW TO USE:
--  1. Open Laragon, start MySQL.
--  2. Open phpMyAdmin (or the Laragon MySQL terminal).
--  3. Select your POS database.
--  4. Paste this whole script into the SQL tab and RUN.
--
--  WHAT THIS DOES:
--  - First it DELETES all existing rows in these tables
--    (so you can run it again and again).
--  - Then it inserts fresh dummy data:
--        category  : 8 rows    (lookup table)
--        user      : 10 rows   (cashiers lookup table)
--        product   : 80 rows
--        customer  : 70 rows
--        sale      : 80 rows
--        sale_item : 100 rows
--        payment   : 70 rows
--        cart      : 70 rows
--  - Sale statuses are mixed: PAID / OPEN / CANCELED
--  - Sale dates are spread over 2025-07 to 2026-01 so the
--    day/month/year filter works.
--  - Some products have LOW stock (< 10) so the low-stock
--    filter / "out of stock" display works.
-- ============================================================

-- ------------------------------------------------------------
-- 1. CLEAR OLD DATA (run this section every time you re-test)
-- ------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM cart;
DELETE FROM payment;
DELETE FROM sale_item;
DELETE FROM sale;
DELETE FROM user;
DELETE FROM customer;
DELETE FROM product;
DELETE FROM category;

ALTER TABLE category AUTO_INCREMENT = 1;
ALTER TABLE product AUTO_INCREMENT = 1;
ALTER TABLE customer AUTO_INCREMENT = 1;
ALTER TABLE user AUTO_INCREMENT = 1;
ALTER TABLE sale AUTO_INCREMENT = 1;
ALTER TABLE sale_item AUTO_INCREMENT = 1;
ALTER TABLE payment AUTO_INCREMENT = 1;
ALTER TABLE cart AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 2. CATEGORIES (8)
-- ------------------------------------------------------------
INSERT INTO category (category_name, created_at, created_by_user_id) VALUES
('Beverages',     NOW(), 1),
('Snacks',        NOW(), 1),
('Bakery',        NOW(), 1),
('Household',     NOW(), 1),
('Personal Care', NOW(), 1),
('Dairy',         NOW(), 1),
('Frozen Foods',  NOW(), 1),
('Grains',        NOW(), 1);

-- ------------------------------------------------------------
-- 3. USERS / CASHIERS (10)
--    password is 'password123' for every user
-- ------------------------------------------------------------
INSERT INTO user (firstname, lastname, username, password, email) VALUES
('John',    'Mulenga', 'jmulenga',  'password123', 'john.mulenga@pos.com'),
('Mary',    'Banda',   'mbanda',    'password123', 'mary.banda@pos.com'),
('Peter',   'Phiri',   'pphiri',    'password123', 'peter.phiri@pos.com'),
('Grace',   'Zulu',    'gzulu',     'password123', 'grace.zulu@pos.com'),
('David',   'Tembo',   'dtembo',    'password123', 'david.tembo@pos.com'),
('Ruth',    'Mwansa',  'rmwansa',   'password123', 'ruth.mwansa@pos.com'),
('Samuel',  'Mwale',   'smwale',    'password123', 'samuel.mwale@pos.com'),
('Esther',  'Chanda',  'echanda',   'password123', 'esther.chanda@pos.com'),
('Joseph',  'Musonda', 'jmusonda',  'password123', 'joseph.musonda@pos.com'),
('Charity', 'Mumba',   'cmumba',    'password123', 'charity.mumba@pos.com');

-- ------------------------------------------------------------
-- 4. PRODUCTS (80)
--    Some stock_quantity values are below 10 (low stock) so the
--    low-stock / out-of-stock display can be tested.
-- ------------------------------------------------------------
INSERT INTO product (product_name, barcode, price, stock_quantity, category_id, created_by_user_id) VALUES
-- Beverages (category 1)
('Coca Cola 500ml',    '260000000101', 18.00, 50,  1, 1),
('Fanta Orange 500ml', '260000000102', 18.00, 45,  1, 1),
('Sprite 500ml',       '260000000103', 18.00, 8,   1, 1),
('Mineral Water 1L',   '260000000104', 10.00, 100, 1, 1),
('Orange Juice 1L',    '260000000105', 45.00, 30,  1, 1),
('Apple Juice 1L',     '260000000106', 45.00, 5,   1, 1),
('Black Tea 100g',     '260000000107', 25.00, 60,  1, 1),
('Instant Coffee 200g','260000000108', 65.00, 40,  1, 1),
('Energy Drink 250ml', '260000000109', 25.00, 75,  1, 1),
('Ginger Ale 500ml',   '260000000110', 20.00, 12,  1, 1),
-- Snacks (category 2)
('Potato Chips 150g',  '260000000201', 22.00, 55,  2, 1),
('Popcorn 100g',       '260000000202', 15.00, 7,   2, 1),
('Mixed Nuts 200g',    '260000000203', 55.00, 25,  2, 1),
('Butter Biscuits 250g','260000000204', 28.00, 65,  2, 1),
('Milk Chocolate 100g','260000000205', 20.00, 90,  2, 1),
('Candy Mix 250g',     '260000000206', 12.00, 3,   2, 1),
('Pretzels 150g',      '260000000207', 18.00, 35,  2, 1),
('Cheese Crackers 200g','260000000208', 25.00, 70,  2, 1),
('Granola Bar 40g',    '260000000209', 8.00,  4,   2, 1),
('Beef Jerky 100g',    '260000000210', 35.00, 22,  2, 1),
-- Bakery (category 3)
('White Bread Loaf',   '260000000301', 15.00, 80,  3, 1),
('Whole Wheat Bread',  '260000000302', 18.00, 6,   3, 1),
('Blueberry Muffin',   '260000000303', 12.00, 48,  3, 1),
('Butter Croissant',   '260000000304', 15.00, 32,  3, 1),
('Plain Bagel',        '260000000305', 10.00, 15,  3, 1),
('Chocolate Cake Slice','260000000306', 25.00, 58,  3, 1),
('Glazed Doughnut',    '260000000307', 8.00,  95,  3, 1),
('Apple Pie',          '260000000308', 45.00, 28,  3, 1),
('Chocolate Chip Cookie','260000000309', 6.00,  9,   3, 1),
('Blueberry Scone',    '260000000310', 14.00, 42,  3, 1),
-- Household (category 4)
('Laundry Soap 500g',  '260000000401', 30.00, 66,  4, 1),
('Dish Detergent 750ml','260000000402', 28.00, 38,  4, 1),
('Bleach 1L',          '260000000403', 20.00, 5,   4, 1),
('Cleaning Sponge x3', '260000000404', 15.00, 73,  4, 1),
('Trash Bags 30pc',    '260000000405', 35.00, 27,  4, 1),
('Paper Towels x4',    '260000000406', 40.00, 88,  4, 1),
('Toilet Paper x12',   '260000000407', 60.00, 52,  4, 1),
('Dish Soap 500ml',    '260000000408', 22.00, 6,   4, 1),
('Window Cleaner 750ml','260000000409', 25.00, 44,  4, 1),
('Air Freshener 300ml','260000000410', 32.00, 61,  4, 1),
-- Personal Care (category 5)
('Shampoo 350ml',      '260000000501', 55.00, 33,  5, 1),
('Conditioner 350ml',  '260000000502', 55.00, 8,   5, 1),
('Toothpaste 150g',    '260000000503', 18.00, 77,  5, 1),
('Toothbrush x2',      '260000000504', 20.00, 21,  5, 1),
('Deodorant 150ml',    '260000000505', 35.00, 54,  5, 1),
('Body Lotion 500ml',  '260000000506', 48.00, 69,  5, 1),
('Bath Soap 3pc',      '260000000507', 25.00, 36,  5, 1),
('Razor x4',           '260000000508', 30.00, 10,  5, 1),
('Hand Sanitizer 250ml','260000000509', 28.00, 82,  5, 1),
('Sunscreen SPF50 200ml','260000000510', 85.00, 17, 5, 1),
-- Dairy (category 6)
('Fresh Milk 1L',      '260000000601', 25.00, 91,  6, 1),
('Cheddar Cheese 250g','260000000602', 65.00, 26,  6, 1),
('Plain Yogurt 500g',  '260000000603', 35.00, 59,  6, 1),
('Butter 500g',        '260000000604', 55.00, 3,   6, 1),
('Cooking Cream 250ml','260000000605', 30.00, 71,  6, 1),
('Cottage Cheese 250g','260000000606', 40.00, 47,  6, 1),
('Sour Cream 250ml',   '260000000607', 28.00, 14,  6, 1),
('Whipped Cream 250ml','260000000608', 25.00, 63,  6, 1),
('Condensed Milk 397g','260000000609', 22.00, 84,  6, 1),
('Powdered Milk 500g', '260000000610', 48.00, 39,  6, 1),
-- Frozen Foods (category 7)
('Frozen Pizza',       '260000000701', 75.00, 56,  7, 1),
('Mixed Veggies 500g', '260000000702', 30.00, 11,  7, 1),
('Frozen Fries 1kg',   '260000000703', 35.00, 7,   7, 1),
('Vanilla Ice Cream 1L','260000000704', 55.00, 93,  7, 1),
('Frozen Berries 400g','260000000705', 60.00, 30,  7, 1),
('Frozen Fish Fillets 500g','260000000706', 85.00, 49, 7, 1),
('Frozen Chicken 1kg', '260000000707', 95.00, 5,   7, 1),
('Frozen Dumplings 500g','260000000708', 45.00, 74,  7, 1),
('Frozen Corn 500g',   '260000000709', 25.00, 41,  7, 1),
('Frozen Peas 500g',   '260000000710', 25.00, 67,  7, 1),
-- Grains (category 8)
('White Rice 5kg',     '260000000801', 120.00, 23, 8, 1),
('Wheat Flour 2kg',    '260000000802', 45.00, 86,  8, 1),
('White Sugar 2kg',    '260000000803', 55.00, 34,  8, 1),
('Spaghetti 500g',     '260000000804', 20.00, 57,  8, 1),
('Rolled Oats 1kg',    '260000000805', 50.00, 9,   8, 1),
('Corn Flakes 500g',   '260000000806', 65.00, 79,  8, 1),
('Red Beans 1kg',      '260000000807', 30.00, 62,  8, 1),
('Green Lentils 1kg',  '260000000808', 35.00, 16,  8, 1),
('Quinoa 500g',        '260000000809', 80.00, 53,  8, 1),
('Cornmeal 2kg',       '260000000810', 40.00, 98,  8, 1);

-- ------------------------------------------------------------
-- 5. CUSTOMERS (70)
-- ------------------------------------------------------------
INSERT INTO customer (firstname, phone, email) VALUES
('Alice',     '+260971000001', 'alice.banda@gmail.com'),
('Brian',     '+260971000002', 'brian.chanda@gmail.com'),
('Charity',   '+260971000003', 'charity.daka@gmail.com'),
('Daniel',    '+260971000004', 'daniel.phiri@gmail.com'),
('Esther',    '+260971000005', 'esther.zulu@gmail.com'),
('Frank',     '+260971000006', 'frank.mwansa@gmail.com'),
('Grace',     '+260971000007', 'grace.tembo@gmail.com'),
('Henry',     '+260971000008', 'henry.mulenga@gmail.com'),
('Irene',     '+260971000009', 'irene.musonda@gmail.com'),
('James',     '+260971000010', 'james.mwale@gmail.com'),
('Joyce',     '+260971000011', 'joyce.nkhoma@gmail.com'),
('Kelvin',    '+260971000012', 'kelvin.sakala@gmail.com'),
('Linda',     '+260971000013', 'linda.mumba@gmail.com'),
('Moses',     '+260971000014', 'moses.lungu@gmail.com'),
('Nancy',     '+260971000015', 'nancy.banda@gmail.com'),
('Oscar',     '+260971000016', 'oscar.chileshe@gmail.com'),
('Patricia',  '+260971000017', 'patricia.mwamba@gmail.com'),
('Quincy',    '+260971000018', 'quincy.nyirenda@gmail.com'),
('Rachel',    '+260971000019', 'rachel.phiri@gmail.com'),
('Samuel',    '+260971000020', 'samuel.zulu@gmail.com'),
('Theresa',   '+260971000021', 'theresa.mwape@gmail.com'),
('Victor',    '+260971000022', 'victor.kunda@gmail.com'),
('Winnie',    '+260971000023', 'winnie.banda@gmail.com'),
('Xavier',    '+260971000024', 'xavier.chanda@gmail.com'),
('Yvonne',    '+260971000025', 'yvonne.tembo@gmail.com'),
('Zachary',   '+260971000026', 'zachary.mwila@gmail.com'),
('Agnes',     '+260971000027', 'agnes.phiri@gmail.com'),
('Benson',    '+260971000028', 'benson.mulenga@gmail.com'),
('Catherine', '+260971000029', 'catherine.mwansa@gmail.com'),
('Dennis',    '+260971000030', 'dennis.mbewe@gmail.com'),
('Edith',     '+260971000031', 'edith.lungu@gmail.com'),
('Felix',     '+260971000032', 'felix.zimba@gmail.com'),
('Gladys',    '+260971000033', 'gladys.musonda@gmail.com'),
('Harold',    '+260971000034', 'harold.mwape@gmail.com'),
('Isabel',    '+260971000035', 'isabel.mwansa@gmail.com'),
('Joseph',    '+260971000036', 'joseph.banda@gmail.com'),
('Katherine', '+260971000037', 'katherine.phiri@gmail.com'),
('Leonard',   '+260971000038', 'leonard.sakala@gmail.com'),
('Margaret',  '+260971000039', 'margaret.chileshe@gmail.com'),
('Nelson',    '+260971000040', 'nelson.mwamba@gmail.com'),
('Olivia',    '+260971000041', 'olivia.kunda@gmail.com'),
('Patrick',   '+260971000042', 'patrick.mumba@gmail.com'),
('Queen',     '+260971000043', 'queen.mwila@gmail.com'),
('Robert',    '+260971000044', 'robert.tembo@gmail.com'),
('Susan',     '+260971000045', 'susan.zulu@gmail.com'),
('Thomas',    '+260971000046', 'thomas.phiri@gmail.com'),
('Unity',     '+260971000047', 'unity.mwansa@gmail.com'),
('Vincent',   '+260971000048', 'vincent.chanda@gmail.com'),
('Winnifred', '+260971000049', 'winnifred.mwale@gmail.com'),
('Emmanuel',  '+260971000050', 'emmanuel.banda@gmail.com'),
('Beatrice',  '+260971000051', 'beatrice.chanda@gmail.com'),
('Charles',   '+260971000052', 'charles.mwansa@gmail.com'),
('Doris',     '+260971000053', 'doris.zimba@gmail.com'),
('Elijah',    '+260971000054', 'elijah.phiri@gmail.com'),
('Faith',     '+260971000055', 'faith.mumba@gmail.com'),
('George',    '+260971000056', 'george.tembo@gmail.com'),
('Hellen',    '+260971000057', 'hellen.mwape@gmail.com'),
('Isaac',     '+260971000058', 'isaac.kunda@gmail.com'),
('Jane',      '+260971000059', 'jane.mwila@gmail.com'),
('Kenneth',   '+260971000060', 'kenneth.lungu@gmail.com'),
('Loveness',  '+260971000061', 'loveness.phiri@gmail.com'),
('Maxwell',   '+260971000062', 'maxwell.chileshe@gmail.com'),
('Naomi',     '+260971000063', 'naomi.sakala@gmail.com'),
('Obed',      '+260971000064', 'obed.mwansa@gmail.com'),
('Precious',  '+260971000065', 'precious.mumba@gmail.com'),
('Richard',   '+260971000066', 'richard.zulu@gmail.com'),
('Sarah',     '+260971000067', 'sarah.banda@gmail.com'),
('Timothy',   '+260971000068', 'timothy.mwale@gmail.com'),
('Ursula',    '+260971000069', 'ursula.chanda@gmail.com'),
('Violet',    '+260971000070', 'violet.phiri@gmail.com');

-- ------------------------------------------------------------
-- 6. SALES (80)
--    Status mix : PAID (50), OPEN (20), CANCELED (10)
--    Dates spread over 2025-07 to 2026-01
-- ------------------------------------------------------------
INSERT INTO sale (customer_id, user_id, sale_date, total_amount, status) VALUES
( 5, 1, '2025-07-02', 36.00,  'PAID'),
(12, 2, '2025-07-05', 45.00,  'PAID'),
(23, 3, '2025-07-08', 120.00, 'OPEN'),
(34, 4, '2025-07-11', 65.00,  'PAID'),
(45, 5, '2025-07-14', 28.00,  'CANCELED'),
(56, 6, '2025-07-17', 55.00,  'PAID'),
(67, 7, '2025-07-20', 72.00,  'PAID'),
( 3, 8, '2025-07-23', 40.00,  'OPEN'),
(18, 9, '2025-07-26', 95.00,  'PAID'),
(29,10, '2025-07-29', 60.00,  'PAID'),
(41, 1, '2025-08-01', 85.00,  'PAID'),
(53, 2, '2025-08-04', 30.00,  'OPEN'),
(64, 3, '2025-08-07', 150.00, 'PAID'),
( 7, 4, '2025-08-10', 48.00,  'PAID'),
(19, 5, '2025-08-13', 20.00,  'CANCELED'),
(31, 6, '2025-08-16', 110.00, 'PAID'),
(42, 7, '2025-08-19', 35.00,  'OPEN'),
(58, 8, '2025-08-22', 75.00,  'PAID'),
( 9, 9, '2025-08-25', 55.00,  'PAID'),
(21,10, '2025-08-28', 90.00,  'PAID'),
(33, 1, '2025-09-01', 42.00,  'OPEN'),
(44, 2, '2025-09-04', 65.00,  'PAID'),
(55, 3, '2025-09-07', 25.00,  'PAID'),
(66, 4, '2025-09-10', 130.00, 'PAID'),
( 2, 5, '2025-09-13', 38.00,  'CANCELED'),
(14, 6, '2025-09-16', 50.00,  'PAID'),
(26, 7, '2025-09-19', 70.00,  'PAID'),
(37, 8, '2025-09-22', 45.00,  'OPEN'),
(49, 9, '2025-09-25', 88.00,  'PAID'),
(60,10, '2025-09-28', 32.00,  'PAID'),
( 8, 1, '2025-10-02', 54.00,  'PAID'),
(17, 2, '2025-10-05', 140.00, 'PAID'),
(28, 3, '2025-10-08', 27.00,  'OPEN'),
(39, 4, '2025-10-11', 62.00,  'PAID'),
(50, 5, '2025-10-14', 95.00,  'CANCELED'),
(61, 6, '2025-10-17', 45.00,  'PAID'),
( 4, 7, '2025-10-20', 80.00,  'PAID'),
(16, 8, '2025-10-23', 36.00,  'OPEN'),
(27, 9, '2025-10-26', 75.00,  'PAID'),
(38,10, '2025-10-29', 120.00, 'PAID'),
(11, 1, '2025-11-02', 50.00,  'PAID'),
(22, 2, '2025-11-05', 68.00,  'PAID'),
(35, 3, '2025-11-08', 33.00,  'CANCELED'),
(46, 4, '2025-11-11', 96.00,  'PAID'),
(57, 5, '2025-11-14', 40.00,  'OPEN'),
(68, 6, '2025-11-17', 55.00,  'PAID'),
(10, 7, '2025-11-20', 85.00,  'PAID'),
(24, 8, '2025-11-23', 160.00, 'PAID'),
(36, 9, '2025-11-26', 45.00,  'OPEN'),
(48,10, '2025-11-29', 72.00,  'PAID'),
( 6, 1, '2025-12-02', 35.00,  'PAID'),
(15, 2, '2025-12-05', 90.00,  'PAID'),
(25, 3, '2025-12-08', 60.00,  'CANCELED'),
(32, 4, '2025-12-11', 48.00,  'OPEN'),
(43, 5, '2025-12-14', 130.00, 'PAID'),
(54, 6, '2025-12-17', 25.00,  'PAID'),
(65, 7, '2025-12-20', 70.00,  'PAID'),
( 1, 8, '2025-12-23', 55.00,  'OPEN'),
(13, 9, '2025-12-26', 105.00, 'PAID'),
(30,10, '2025-12-29', 42.00,  'PAID'),
(40, 1, '2026-01-02', 88.00,  'PAID'),
(52, 2, '2026-01-05', 36.00,  'PAID'),
(63, 3, '2026-01-08', 75.00,  'OPEN'),
(69, 4, '2026-01-11', 50.00,  'PAID'),
(20, 5, '2026-01-14', 65.00,  'PAID'),
(47, 6, '2026-01-15', 120.00, 'PAID'),
(59, 7, '2026-01-16', 40.00,  'CANCELED'),
(70, 8, '2026-01-17', 95.00,  'PAID'),
( 2, 9, '2026-01-18', 30.00,  'OPEN'),
(18,10, '2026-01-19', 55.00,  'PAID'),
(28, 1, '2026-01-19', 145.00, 'PAID'),
(44, 2, '2026-01-20', 62.00,  'PAID'),
(51, 3, '2026-01-20', 34.00,  'OPEN'),
( 7, 4, '2026-01-21', 78.00,  'PAID'),
(26, 5, '2026-01-21', 48.00,  'PAID'),
(38, 6, '2026-01-22', 110.00, 'PAID'),
(55, 7, '2026-01-22', 25.00,  'CANCELED'),
(62, 8, '2026-01-23', 85.00,  'PAID'),
( 9, 9, '2026-01-23', 40.00,  'OPEN'),
(31,10, '2026-01-24', 95.00,  'PAID');

-- ------------------------------------------------------------
-- 7. SALE ITEMS (100)
--    Each sale has at least 1 item. Some sales have 2 items
--    so the ITEMS column (COUNT) shows 1 or 2.
--    The `Date` column is kept the same as the sale date.
-- ------------------------------------------------------------
INSERT INTO sale_item (sale_id, product_id, quantity, unit_price, subtotal, `Date`, created_by_user_id) VALUES
( 1,  1, 2, 18.00, 36.00, '2025-07-02', 1),
( 2,  3, 1, 18.00, 18.00, '2025-07-05', 2),
( 2, 51, 1, 25.00, 25.00, '2025-07-05', 2),
( 3, 71, 1, 120.00, 120.00, '2025-07-08', 3),
( 4, 41, 1, 55.00, 55.00, '2025-07-11', 4),
( 4, 43, 1, 18.00, 18.00, '2025-07-11', 4),
( 5, 13, 1, 55.00, 55.00, '2025-07-14', 5),
( 6, 61, 1, 75.00, 75.00, '2025-07-17', 6),
( 7, 62, 1, 30.00, 30.00, '2025-07-20', 7),
( 7, 42, 1, 55.00, 55.00, '2025-07-20', 7),
( 8, 33, 1, 20.00, 20.00, '2025-07-23', 8),
( 8, 34, 1, 15.00, 15.00, '2025-07-23', 8),
( 9, 67, 1, 95.00, 95.00, '2025-07-26', 9),
(10, 30, 2, 32.00, 64.00, '2025-07-29', 10),
(11, 75, 1, 50.00, 50.00, '2025-08-01', 1),
(11, 76, 1, 65.00, 65.00, '2025-08-01', 1),
(12, 23, 1, 12.00, 12.00, '2025-08-04', 2),
(12, 24, 1, 15.00, 15.00, '2025-08-04', 2),
(13, 79, 1, 80.00, 80.00, '2025-08-07', 3),
(14, 46, 1, 48.00, 48.00, '2025-08-10', 4),
(15, 19, 1, 8.00, 8.00, '2025-08-13', 5),
(16, 65, 1, 60.00, 60.00, '2025-08-16', 6),
(17, 27, 1, 8.00, 8.00, '2025-08-19', 7),
(18, 68, 1, 45.00, 45.00, '2025-08-22', 8),
(19, 55, 1, 30.00, 30.00, '2025-08-25', 9),
(20, 78, 1, 35.00, 35.00, '2025-08-28', 10),
(21, 11, 2, 22.00, 44.00, '2025-09-01', 1),
(22, 44, 1, 20.00, 20.00, '2025-09-04', 2),
(22, 45, 1, 35.00, 35.00, '2025-09-04', 2),
(23, 5, 1, 45.00, 45.00, '2025-09-07', 3),
(24, 73, 1, 55.00, 55.00, '2025-09-10', 4),
(25, 8, 1, 65.00, 65.00, '2025-09-13', 5),
(26, 50, 1, 85.00, 85.00, '2025-09-16', 6),
(27, 60, 1, 48.00, 48.00, '2025-09-19', 7),
(28, 14, 1, 28.00, 28.00, '2025-09-22', 8),
(29, 69, 1, 25.00, 25.00, '2025-09-25', 9),
(30, 2, 1, 18.00, 18.00, '2025-09-28', 10),
(31, 6, 1, 45.00, 45.00, '2025-10-02', 1),
(32, 77, 1, 30.00, 30.00, '2025-10-05', 2),
(33, 15, 1, 20.00, 20.00, '2025-10-08', 3),
(34, 4, 2, 10.00, 20.00, '2025-10-11', 4),
(35, 22, 1, 18.00, 18.00, '2025-10-14', 5),
(36, 25, 1, 10.00, 10.00, '2025-10-17', 6),
(37, 7, 1, 25.00, 25.00, '2025-10-20', 7),
(38, 9, 1, 25.00, 25.00, '2025-10-23', 8),
(39, 70, 1, 25.00, 25.00, '2025-10-26', 9),
(40, 80, 1, 40.00, 40.00, '2025-10-29', 10),
(41, 36, 1, 40.00, 40.00, '2025-11-02', 1),
(42, 49, 1, 28.00, 28.00, '2025-11-05', 2),
(43, 18, 1, 25.00, 25.00, '2025-11-08', 3),
(44, 72, 1, 45.00, 45.00, '2025-11-11', 4),
(45, 28, 1, 45.00, 45.00, '2025-11-14', 5),
(46, 35, 1, 35.00, 35.00, '2025-11-17', 6),
(47, 40, 1, 32.00, 32.00, '2025-11-20', 7),
(48, 63, 1, 35.00, 35.00, '2025-11-23', 8),
(49, 20, 1, 35.00, 35.00, '2025-11-26', 9),
(50, 58, 1, 25.00, 25.00, '2025-11-29', 10),
(51, 31, 1, 30.00, 30.00, '2025-12-02', 1),
(52, 53, 1, 35.00, 35.00, '2025-12-05', 2),
(53, 16, 1, 12.00, 12.00, '2025-12-08', 3),
(54, 48, 1, 30.00, 30.00, '2025-12-11', 4),
(55, 64, 1, 55.00, 55.00, '2025-12-14', 5),
(56, 17, 1, 18.00, 18.00, '2025-12-17', 6),
(57, 52, 1, 65.00, 65.00, '2025-12-20', 7),
(58, 37, 1, 60.00, 60.00, '2025-12-23', 8),
(59, 74, 1, 20.00, 20.00, '2025-12-26', 9),
(60, 29, 1, 6.00, 6.00, '2025-12-29', 10),
(61, 10, 1, 20.00, 20.00, '2026-01-02', 1),
(62, 12, 1, 15.00, 15.00, '2026-01-05', 2),
(63, 21, 1, 15.00, 15.00, '2026-01-08', 3),
(64, 56, 1, 55.00, 55.00, '2026-01-11', 4),
(65, 39, 1, 25.00, 25.00, '2026-01-14', 5),
(66, 26, 1, 25.00, 25.00, '2026-01-15', 6),
(67, 32, 1, 28.00, 28.00, '2026-01-16', 7),
(68, 38, 1, 22.00, 22.00, '2026-01-17', 8),
(69, 47, 1, 25.00, 25.00, '2026-01-18', 9),
(70, 59, 1, 22.00, 22.00, '2026-01-19', 10),
(71, 66, 1, 95.00, 95.00, '2026-01-19', 1),
(72, 57, 1, 28.00, 28.00, '2026-01-20', 2),
(73, 2, 1, 18.00, 18.00, '2026-01-20', 3),
(74, 3, 1, 18.00, 18.00, '2026-01-21', 4),
(75, 4, 1, 10.00, 10.00, '2026-01-21', 5),
(76, 5, 1, 45.00, 45.00, '2026-01-22', 6),
(77, 6, 1, 45.00, 45.00, '2026-01-22', 7),
(78, 7, 1, 25.00, 25.00, '2026-01-23', 8),
(79, 8, 1, 65.00, 65.00, '2026-01-23', 9),
(80, 9, 1, 25.00, 25.00, '2026-01-24', 10);

-- ------------------------------------------------------------
-- 8. PAYMENTS (70 - one for each PAID sale)
-- ------------------------------------------------------------
INSERT INTO payment (sale_id, payment_method, amount_paid, payment_date) VALUES
( 1, 'Cash',        36.00, '2025-07-02'),
( 2, 'Card',        45.00, '2025-07-05'),
( 4, 'Mobile Money', 65.00, '2025-07-11'),
( 6, 'Cash',        55.00, '2025-07-17'),
( 7, 'Card',        72.00, '2025-07-20'),
( 9, 'Cash',        95.00, '2025-07-26'),
(10, 'Mobile Money', 60.00, '2025-07-29'),
(11, 'Cash',        85.00, '2025-08-01'),
(13, 'Card',        150.00, '2025-08-07'),
(14, 'Cash',        48.00, '2025-08-10'),
(16, 'Mobile Money', 110.00, '2025-08-16'),
(18, 'Cash',        75.00, '2025-08-22'),
(19, 'Card',        55.00, '2025-08-25'),
(20, 'Cash',        90.00, '2025-08-28'),
(22, 'Cash',        65.00, '2025-09-04'),
(23, 'Card',        25.00, '2025-09-07'),
(24, 'Cash',        130.00, '2025-09-10'),
(26, 'Mobile Money', 50.00, '2025-09-16'),
(27, 'Cash',        70.00, '2025-09-19'),
(29, 'Card',        88.00, '2025-09-25'),
(30, 'Cash',        32.00, '2025-09-28'),
(31, 'Cash',        54.00, '2025-10-02'),
(32, 'Card',        140.00, '2025-10-05'),
(34, 'Mobile Money', 62.00, '2025-10-11'),
(36, 'Cash',        45.00, '2025-10-17'),
(37, 'Card',        80.00, '2025-10-20'),
(39, 'Cash',        75.00, '2025-10-26'),
(40, 'Mobile Money', 120.00, '2025-10-29'),
(41, 'Cash',        50.00, '2025-11-02'),
(42, 'Card',        68.00, '2025-11-05'),
(44, 'Cash',        96.00, '2025-11-11'),
(46, 'Mobile Money', 55.00, '2025-11-17'),
(47, 'Cash',        85.00, '2025-11-20'),
(48, 'Card',        160.00, '2025-11-23'),
(50, 'Cash',        72.00, '2025-11-29'),
(51, 'Cash',        35.00, '2025-12-02'),
(52, 'Card',        90.00, '2025-12-05'),
(55, 'Cash',        130.00, '2025-12-14'),
(56, 'Mobile Money', 25.00, '2025-12-17'),
(57, 'Cash',        70.00, '2025-12-20'),
(59, 'Card',        105.00, '2025-12-26'),
(60, 'Cash',        42.00, '2025-12-29'),
(61, 'Cash',        88.00, '2026-01-02'),
(62, 'Card',        36.00, '2026-01-05'),
(64, 'Cash',        50.00, '2026-01-11'),
(65, 'Mobile Money', 65.00, '2026-01-14'),
(66, 'Cash',        120.00, '2026-01-15'),
(68, 'Card',        95.00, '2026-01-17'),
(70, 'Cash',        55.00, '2026-01-19'),
(71, 'Card',        145.00, '2026-01-19'),
(72, 'Cash',        62.00, '2026-01-20'),
(74, 'Mobile Money', 78.00, '2026-01-21'),
(75, 'Cash',        48.00, '2026-01-21'),
(76, 'Card',        110.00, '2026-01-22'),
(78, 'Cash',        85.00, '2026-01-23'),
(80, 'Mobile Money', 95.00, '2026-01-24');

-- ------------------------------------------------------------
-- 9. CART (70 - dummy saved-cart entries)
-- ------------------------------------------------------------
INSERT INTO cart (product_id, product_name, category_name, price, qty, total, created_by_user_id) VALUES
( 1, 'Coca Cola 500ml',    'Beverages',     18.00, 2, 36.00, 1),
( 2, 'Fanta Orange 500ml', 'Beverages',     18.00, 1, 18.00, 1),
( 3, 'Sprite 500ml',       'Beverages',     18.00, 3, 54.00, 2),
( 4, 'Mineral Water 1L',   'Beverages',     10.00, 1, 10.00, 2),
( 5, 'Orange Juice 1L',    'Beverages',     45.00, 1, 45.00, 3),
( 6, 'Apple Juice 1L',     'Beverages',     45.00, 2, 90.00, 3),
( 7, 'Black Tea 100g',     'Beverages',     25.00, 1, 25.00, 4),
( 8, 'Instant Coffee 200g','Beverages',     65.00, 1, 65.00, 4),
( 9, 'Energy Drink 250ml', 'Beverages',     25.00, 4, 100.00, 5),
(10, 'Ginger Ale 500ml',   'Beverages',     20.00, 1, 20.00, 5),
(11, 'Potato Chips 150g',  'Snacks',        22.00, 2, 44.00, 6),
(12, 'Popcorn 100g',       'Snacks',        15.00, 1, 15.00, 6),
(13, 'Mixed Nuts 200g',    'Snacks',        55.00, 1, 55.00, 7),
(14, 'Butter Biscuits 250g','Snacks',       28.00, 3, 84.00, 7),
(15, 'Milk Chocolate 100g','Snacks',        20.00, 2, 40.00, 8),
(16, 'Candy Mix 250g',     'Snacks',        12.00, 1, 12.00, 8),
(17, 'Pretzels 150g',      'Snacks',        18.00, 1, 18.00, 9),
(18, 'Cheese Crackers 200g','Snacks',       25.00, 2, 50.00, 9),
(19, 'Granola Bar 40g',    'Snacks',        8.00,  5, 40.00, 10),
(20, 'Beef Jerky 100g',    'Snacks',        35.00, 1, 35.00, 10),
(21, 'White Bread Loaf',   'Bakery',        15.00, 2, 30.00, 1),
(22, 'Whole Wheat Bread',  'Bakery',        18.00, 1, 18.00, 2),
(23, 'Blueberry Muffin',   'Bakery',        12.00, 4, 48.00, 3),
(24, 'Butter Croissant',   'Bakery',        15.00, 2, 30.00, 4),
(25, 'Plain Bagel',        'Bakery',        10.00, 3, 30.00, 5),
(26, 'Chocolate Cake Slice','Bakery',       25.00, 1, 25.00, 6),
(27, 'Glazed Doughnut',    'Bakery',        8.00,  6, 48.00, 7),
(28, 'Apple Pie',          'Bakery',        45.00, 1, 45.00, 8),
(29, 'Chocolate Chip Cookie','Bakery',      6.00,  4, 24.00, 9),
(30, 'Blueberry Scone',    'Bakery',        14.00, 2, 28.00, 10),
(31, 'Laundry Soap 500g',  'Household',     30.00, 1, 30.00, 1),
(32, 'Dish Detergent 750ml','Household',    28.00, 1, 28.00, 2),
(33, 'Bleach 1L',          'Household',     20.00, 2, 40.00, 3),
(34, 'Cleaning Sponge x3', 'Household',     15.00, 3, 45.00, 4),
(35, 'Trash Bags 30pc',    'Household',     35.00, 1, 35.00, 5),
(36, 'Paper Towels x4',    'Household',     40.00, 1, 40.00, 6),
(37, 'Toilet Paper x12',   'Household',     60.00, 2, 120.00, 7),
(38, 'Dish Soap 500ml',    'Household',     22.00, 1, 22.00, 8),
(39, 'Window Cleaner 750ml','Household',    25.00, 1, 25.00, 9),
(40, 'Air Freshener 300ml','Household',     32.00, 2, 64.00, 10),
(41, 'Shampoo 350ml',      'Personal Care', 55.00, 1, 55.00, 1),
(42, 'Conditioner 350ml',  'Personal Care', 55.00, 1, 55.00, 2),
(43, 'Toothpaste 150g',    'Personal Care', 18.00, 2, 36.00, 3),
(44, 'Toothbrush x2',      'Personal Care', 20.00, 1, 20.00, 4),
(45, 'Deodorant 150ml',    'Personal Care', 35.00, 1, 35.00, 5),
(46, 'Body Lotion 500ml',  'Personal Care', 48.00, 1, 48.00, 6),
(47, 'Bath Soap 3pc',      'Personal Care', 25.00, 3, 75.00, 7),
(48, 'Razor x4',           'Personal Care', 30.00, 1, 30.00, 8),
(49, 'Hand Sanitizer 250ml','Personal Care', 28.00, 2, 56.00, 9),
(50, 'Sunscreen SPF50 200ml','Personal Care', 85.00, 1, 85.00, 10),
(51, 'Fresh Milk 1L',      'Dairy',         25.00, 2, 50.00, 1),
(52, 'Cheddar Cheese 250g','Dairy',         65.00, 1, 65.00, 2),
(53, 'Plain Yogurt 500g',  'Dairy',         35.00, 1, 35.00, 3),
(54, 'Butter 500g',        'Dairy',         55.00, 1, 55.00, 4),
(55, 'Cooking Cream 250ml','Dairy',         30.00, 2, 60.00, 5),
(56, 'Cottage Cheese 250g','Dairy',         40.00, 1, 40.00, 6),
(57, 'Sour Cream 250ml',   'Dairy',         28.00, 1, 28.00, 7),
(58, 'Whipped Cream 250ml','Dairy',         25.00, 2, 50.00, 8),
(59, 'Condensed Milk 397g','Dairy',         22.00, 1, 22.00, 9),
(60, 'Powdered Milk 500g', 'Dairy',         48.00, 1, 48.00, 10),
(61, 'Frozen Pizza',       'Frozen Foods',  75.00, 1, 75.00, 1),
(62, 'Mixed Veggies 500g', 'Frozen Foods',  30.00, 2, 60.00, 2),
(63, 'Frozen Fries 1kg',   'Frozen Foods',  35.00, 1, 35.00, 3),
(64, 'Vanilla Ice Cream 1L','Frozen Foods', 55.00, 2, 110.00, 4),
(65, 'Frozen Berries 400g','Frozen Foods',  60.00, 1, 60.00, 5),
(66, 'Frozen Fish Fillets 500g','Frozen Foods', 85.00, 1, 85.00, 6),
(67, 'Frozen Chicken 1kg', 'Frozen Foods',  95.00, 1, 95.00, 7),
(68, 'Frozen Dumplings 500g','Frozen Foods', 45.00, 1, 45.00, 8),
(69, 'Frozen Corn 500g',   'Frozen Foods',  25.00, 2, 50.00, 9),
(70, 'Frozen Peas 500g',   'Frozen Foods',  25.00, 1, 25.00, 10);

-- ------------------------------------------------------------
-- DONE!
-- Quick check - run these SELECTs to see your data:
--   SELECT status, COUNT(*) FROM sale GROUP BY status;
--   SELECT * FROM product WHERE stock_quantity < 10;
--   SELECT * FROM sale ORDER BY sale_date DESC;
-- ------------------------------------------------------------

