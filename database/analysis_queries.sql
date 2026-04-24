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