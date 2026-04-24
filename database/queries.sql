-- ============================================
-- SYSTEM OPERATIONS (CRUD + TESTING)
-- These queries support application functionality
-- ============================================

-- 1. Retrieve all medications
SELECT *
FROM medications;

-- 2. Search for a medication by name
SELECT *
FROM medications
WHERE medication_name LIKE '%Ibuprofen%';

-- 3. Add a new medication
INSERT INTO medications (medication_name, category, description, reorder_threshold, supplier_id)
VALUES ('Doxycycline 100mg', 'Antibiotic', 'Used for infections and acne treatment', 20, 2);

-- 4. Verify inserted medication
SELECT *
FROM medications
WHERE medication_name LIKE '%Doxycycline%';

-- 5. Update inventory quantity
UPDATE inventory
SET quantity = 50
WHERE inventory_id = 5;

-- 6. Retrieve all inventory
SELECT *
FROM inventory;

-- 7. Delete a medication
DELETE FROM medications
WHERE medication_id = 5;

-- 8. View low-stock medications
SELECT i.inventory_id,
       m.medication_name,
       i.quantity,
       m.reorder_threshold
FROM inventory i
JOIN medications m ON i.medication_id = m.medication_id
WHERE i.quantity < m.reorder_threshold;

-- 9. View medications expiring soon
SELECT i.inventory_id,
       m.medication_name,
       i.expiration_date,
       i.location
FROM inventory i
JOIN medications m ON i.medication_id = m.medication_id
WHERE i.expiration_date <= '2026-09-30'
ORDER BY i.expiration_date ASC;
