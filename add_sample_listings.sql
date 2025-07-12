-- Add Sample Food Listings to the database
-- Run this in phpMyAdmin or MySQL command line

INSERT INTO food_listings (donor_id, food_name, quantity, expiry_date, location, created_at) VALUES
(1, 'Fresh Bread', '20 loaves', '2024-01-15', 'Colombo 03, Sri Lanka', NOW()),
(1, 'Rice Bags', '5 kg bags', '2024-12-31', 'Nugegoda, Sri Lanka', NOW()),
(1, 'Canned Vegetables', '15 cans', '2024-06-30', 'Dehiwala, Sri Lanka', NOW()),
(1, 'Fresh Fruits', '10 kg mixed', '2024-01-10', 'Mount Lavinia, Sri Lanka', NOW()),
(1, 'Milk Powder', '8 packets', '2024-08-15', 'Battaramulla, Sri Lanka', NOW()),
(1, 'Cooking Oil', '5 liters', '2024-10-20', 'Maharagama, Sri Lanka', NOW()),
(1, 'Sugar', '3 kg', '2024-12-31', 'Kohuwala, Sri Lanka', NOW()),
(1, 'Tea Bags', '100 bags', '2024-09-30', 'Moratuwa, Sri Lanka', NOW());

-- Verify the insertions
SELECT * FROM food_listings ORDER BY created_at DESC; 