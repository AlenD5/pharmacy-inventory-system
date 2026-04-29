CREATE DATABASE pharmacy_db;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `suppliers` (
  `supplier_id` int NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(100) NOT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `medications` (
  `medication_id` int NOT NULL AUTO_INCREMENT,
  `medication_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text,
  `reorder_threshold` int NOT NULL DEFAULT '20',
  `supplier_id` int DEFAULT NULL,
  PRIMARY KEY (`medication_id`),
  KEY `fk_medications_supplier` (`supplier_id`),
  CONSTRAINT `fk_medications_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `inventory` (
  `inventory_id` int NOT NULL AUTO_INCREMENT,
  `medication_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `expiration_date` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_id`),
  KEY `fk_inventory_medication` (`medication_id`),
  CONSTRAINT `fk_inventory_medication` FOREIGN KEY (`medication_id`) REFERENCES `medications` (`medication_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `inventory_id` int NOT NULL,
  `user_id` int NOT NULL,
  `transaction_type` varchar(30) NOT NULL,
  `amount_changed` int NOT NULL,
  `transaction_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `fk_transactions_inventory` (`inventory_id`),
  KEY `fk_transactions_user` (`user_id`),
  CONSTRAINT `fk_transactions_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES
(1,'Alen','Dizdaric','alen@pharmacysys.local','password1','Admin','2026-04-06 08:53:00'),
(2,'Brandon','Reagen','brandon@pharmacysys.local','password2','Pharmacist','2026-04-06 08:53:00'),
(3,'Ensar','Fejzic','ensar@pharmacysys.local','password3','Inventory Manager','2026-04-06 08:53:00'),
(4,'Mia','Carter','mia@pharmacysys.local','password4','Technician','2026-04-06 08:53:00'),
(5,'Noah','Bennett','noah@pharmacysys.local','Password5','Pharmacist','2026-04-06 08:53:00');

INSERT INTO `suppliers` VALUES
(1,'Midwest Pharma Supply','Alia Mustafa','319-555-1101','sales@midwestpharma.com','101 Cedar St, Cedar Rapids, IA'),
(2,'HealthCore Distributors','Marko Izetbegovic','319-555-1102','contact@healthcore.com','220 Market Ave, Des Moines, IA'),
(3,'Apex Medical Supply','Daniel Short','515-555-1103','support@apexmedical.com','55 Industrial Rd, Iowa City, IA'),
(4,'PrimeRx Wholesale','Rachel London','563-555-1104','orders@primerx.com','800 Warehouse Blvd, Davenport, IA'),
(5,'CareLine Drug Supply','Steven Adams','712-555-1105','info@careline.com','18 Commerce Dr, Sioux City, IA');

INSERT INTO `medications` VALUES
(1,'Amoxicillin 500mg','Antibiotic','Used to treat bacterial infections',20,1),
(2,'Ibuprofen 200mg','Pain Reliever','Nonsteroidal anti-inflammatory drug',30,2),
(3,'Metformin 500mg','Diabetes','Used to control blood sugar levels',25,3),
(4,'Lisinopril 10mg','Blood Pressure','Used to treat hypertension',20,1),
(6,'Atorvastatin 20mg','Cholesterol','Used to lower cholesterol',20,2),
(7,'Levothyroxine 50mcg','Thyroid','Used to treat hypothyroidism',25,5),
(8,'Omeprazole 20mg','Acid Reflux','Reduces stomach acid',20,1),
(9,'Losartan 50mg','Blood Pressure','Used to treat high blood pressure',20,3),
(10,'Albuterol Inhaler','Respiratory','Relieves bronchospasm',10,4),
(11,'Sertraline 50mg','Mental Health','Used to treat depression and anxiety',15,2),
(12,'Hydrochlorothiazide 25mg','Diuretic','Used to reduce fluid retention',20,5),
(13,'Amlodipine 5mg','Blood Pressure','Calcium channel blocker',20,1),
(14,'Prednisone 10mg','Steroid','Used for inflammation and immune response',15,4),
(15,'Gabapentin 300mg','Neurology','Used for nerve pain and seizures',20,3),
(16,'Cetirizine 10mg','Allergy','Antihistamine for allergy symptoms',25,2),
(17,'Insulin Glargine','Diabetes','Long-acting insulin',10,5),
(18,'Acetaminophen 500mg','Pain Reliever','Used for pain and fever reduction',35,2),
(19,'Clindamycin 300mg','Antibiotic','Used to treat serious infections',15,4),
(20,'Vitamin D3 1000 IU','Supplement','Supports bone and immune health',40,1),
(21,'Doxycycline 100mg','Antibiotic','Used for infections and acne treatment',20,2);

INSERT INTO `inventory` VALUES
(1,1,45,'2027-02-15','Shelf A1','2026-04-06 08:53:00'),
(2,2,120,'2027-06-30','Shelf A2','2026-04-06 08:53:00'),
(3,3,18,'2026-11-20','Shelf A3','2026-04-06 08:53:00'),
(4,4,32,'2027-01-10','Shelf A4','2026-04-06 08:53:00'),
(6,6,50,'2027-03-22','Shelf B2','2026-04-06 08:53:00'),
(7,7,28,'2027-05-14','Shelf B3','2026-04-06 08:53:00'),
(8,8,14,'2026-09-12','Shelf B4','2026-04-06 08:53:00'),
(9,9,26,'2027-04-01','Shelf C1','2026-04-06 08:53:00'),
(10,10,6,'2026-07-19','Shelf C2','2026-04-06 08:53:00'),
(11,11,21,'2027-02-28','Shelf C3','2026-04-06 08:53:00'),
(12,12,17,'2026-12-30','Shelf C4','2026-04-06 08:53:00'),
(13,13,40,'2027-01-25','Shelf D1','2026-04-06 08:53:00'),
(14,14,12,'2026-10-18','Shelf D2','2026-04-06 08:53:00'),
(15,15,24,'2027-03-05','Shelf D3','2026-04-06 08:53:00'),
(16,16,60,'2027-07-07','Shelf D4','2026-04-06 08:53:00'),
(17,17,9,'2026-06-25','Cold Storage 1','2026-04-06 08:53:00'),
(18,18,95,'2027-08-15','Shelf E1','2026-04-06 08:53:00'),
(19,19,11,'2026-08-30','Shelf E2','2026-04-06 08:53:00'),
(20,20,75,'2027-09-09','Shelf E3','2026-04-06 08:53:00');

INSERT INTO `transactions` VALUES
(1,1,1,'Stock In',50,'2026-03-01 09:00:00'),
(2,1,2,'Stock Out',-5,'2026-03-03 10:15:00'),
(3,2,3,'Stock In',120,'2026-03-01 09:30:00'),
(4,2,4,'Stock Out',-10,'2026-03-04 11:00:00'),
(5,3,1,'Stock In',25,'2026-03-02 08:45:00'),
(6,3,5,'Stock Out',-7,'2026-03-05 12:20:00'),
(7,4,2,'Stock In',40,'2026-03-02 09:10:00'),
(8,4,3,'Stock Out',-8,'2026-03-06 14:00:00'),
(11,6,1,'Stock In',60,'2026-03-03 08:30:00'),
(12,6,5,'Stock Out',-10,'2026-03-08 13:10:00'),
(13,7,3,'Stock In',30,'2026-03-03 09:15:00'),
(14,7,4,'Stock Out',-2,'2026-03-09 15:00:00'),
(15,8,2,'Stock In',20,'2026-03-03 09:45:00'),
(16,8,1,'Stock Out',-6,'2026-03-10 10:30:00'),
(17,9,5,'Stock In',35,'2026-03-04 08:20:00'),
(18,9,3,'Stock Out',-9,'2026-03-11 11:25:00'),
(19,10,1,'Stock In',10,'2026-03-04 08:50:00'),
(20,10,2,'Stock Out',-4,'2026-03-12 13:40:00'),
(21,11,4,'Stock In',25,'2026-03-04 09:20:00'),
(22,11,5,'Stock Out',-4,'2026-03-13 16:00:00'),
(23,12,3,'Stock In',20,'2026-03-05 08:10:00'),
(24,12,1,'Stock Out',-3,'2026-03-14 09:10:00'),
(25,13,2,'Stock In',45,'2026-03-05 08:40:00'),
(26,13,4,'Stock Out',-5,'2026-03-15 10:45:00'),
(27,14,1,'Stock In',18,'2026-03-05 09:00:00'),
(28,14,3,'Stock Out',-6,'2026-03-16 14:30:00'),
(29,15,5,'Stock In',30,'2026-03-06 08:25:00'),
(30,15,2,'Stock Out',-6,'2026-03-17 12:00:00'),
(31,16,4,'Stock In',65,'2026-03-06 09:15:00'),
(32,16,1,'Stock Out',-5,'2026-03-18 15:20:00'),
(33,17,3,'Stock In',12,'2026-03-06 10:00:00'),
(34,17,5,'Stock Out',-3,'2026-03-19 10:10:00'),
(35,18,2,'Stock In',100,'2026-03-07 08:35:00'),
(36,18,4,'Stock Out',-5,'2026-03-20 11:35:00'),
(37,19,1,'Stock In',15,'2026-03-07 09:10:00'),
(38,19,2,'Stock Out',-4,'2026-03-21 13:00:00'),
(39,20,5,'Stock In',80,'2026-03-07 09:40:00'),
(40,20,3,'Stock Out',-5,'2026-03-22 09:55:00');

-- Data for April
INSERT INTO transactions
(inventory_id, user_id, transaction_type, amount_changed, transaction_date)
VALUES
(3, 2, 'Stock Out', -6, '2026-04-02 10:00:00'),
(3, 3, 'Stock Out', -5, '2026-04-05 11:00:00'),
(3, 1, 'Stock Out', -7, '2026-04-10 09:30:00'),
(2, 4, 'Stock Out', -8, '2026-04-03 12:00:00'),
(2, 5, 'Stock Out', -6, '2026-04-08 14:00:00'),
(2, 2, 'Stock Out', -5, '2026-04-12 15:30:00'),
(10, 3, 'Stock Out', -2, '2026-04-04 09:00:00'),
(10, 1, 'Stock Out', -3, '2026-04-09 10:30:00'),
(14, 5, 'Stock Out', -4, '2026-04-06 13:00:00'),
(17, 2, 'Stock Out', -2, '2026-04-07 11:00:00'),
(19, 3, 'Stock Out', -3, '2026-04-11 16:00:00');

-- Data for May

INSERT INTO transactions
(inventory_id, user_id, transaction_type, amount_changed, transaction_date)
VALUES
(3, 4, 'Stock Out', -6, '2026-05-02 09:00:00'),
(3, 2, 'Stock Out', -5, '2026-05-06 10:30:00'),
(2, 1, 'Stock Out', -7, '2026-05-03 11:00:00'),
(2, 3, 'Stock Out', -6, '2026-05-09 14:00:00'),
(10, 5, 'Stock Out', -2, '2026-05-05 12:00:00'),
(14, 2, 'Stock Out', -3, '2026-05-07 15:00:00'),
(17, 4, 'Stock Out', -1, '2026-05-04 10:00:00');

-- ============================================
-- PHARMACY INVENTORY DATA ANALYSIS QUERIES
-- Project: Pharmacy Inventory Management System
-- Purpose: SQL analysis for usage, trends, reorder risk, and stockout risk
-- ============================================

-- ============================================
-- QUESTION 1: Most Used Medications
-- Purpose: Identify high-demand medications based on removal transactions
-- ============================================

SELECT 
    m.medication_name,
    COUNT(t.transaction_id) AS times_used,
    SUM(ABS(t.amount_changed)) AS total_quantity_used
FROM transactions t
JOIN inventory i 
    ON t.inventory_id = i.inventory_id
JOIN medications m 
    ON i.medication_id = m.medication_id
WHERE t.transaction_type = 'Stock Out'
GROUP BY m.medication_name
ORDER BY total_quantity_used DESC;


-- ============================================
-- QUESTION 2: Monthly Usage Trends
-- Purpose: Analyze medication usage over time
-- ============================================

SELECT 
    m.medication_name,
    DATE_FORMAT(t.transaction_date, '%Y-%m') AS month,
    SUM(ABS(t.amount_changed)) AS total_usage
FROM transactions t
JOIN inventory i 
    ON t.inventory_id = i.inventory_id
JOIN medications m 
    ON i.medication_id = m.medication_id
WHERE t.transaction_type = 'Stock Out'
GROUP BY m.medication_name, month
ORDER BY m.medication_name, month;


-- ============================================
-- QUESTION 3: Medications Below Reorder Threshold
-- Purpose: Identify inventory risk
-- ============================================

SELECT 
    m.medication_name,
    i.quantity,
    m.reorder_threshold,
    (m.reorder_threshold - i.quantity) AS units_below_threshold
FROM medications m
JOIN inventory i
    ON m.medication_id = i.medication_id
WHERE i.quantity < m.reorder_threshold
ORDER BY units_below_threshold DESC;


-- ============================================
-- QUESTION 4: High-Risk Medications (Low Stock + High Usage)
-- Purpose: Identify medications at highest stockout risk
-- ============================================

SELECT
    m.medication_name,
    i.quantity AS current_quantity,
    m.reorder_threshold,
    (m.reorder_threshold - i.quantity) AS units_below_threshold,
    COUNT(t.transaction_id) AS times_reduced,
    COALESCE(SUM(ABS(t.amount_changed)), 0) AS total_quantity_removed
FROM medications m
JOIN inventory i
    ON m.medication_id = i.medication_id
LEFT JOIN transactions t
    ON i.inventory_id = t.inventory_id
    AND t.transaction_type = 'Stock Out'
GROUP BY
    m.medication_name,
    i.quantity,
    m.reorder_threshold
HAVING i.quantity < m.reorder_threshold
ORDER BY
    times_reduced DESC,
    units_below_threshold DESC;

UPDATE users
SET role = 'Pharmacist'
WHERE user_id IN (1, 2, 3, 5);

UPDATE users
SET role = 'Technician'
WHERE user_id = 5;

UPDATE users
SET role = 'Admin'
WHERE user_id = 1;

UPDATE users
SET role = 'Pharmacist'
WHERE user_id IN (2,5);

UPDATE users
SET role = 'Inventory Manager'
WHERE user_id = 3;

UPDATE users
SET role = 'Technician'
WHERE user_id IN (4);
SELECT user_id, first_name, last_name, email, role
FROM users;