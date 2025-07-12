<?php
header('Content-Type: application/json');
include 'db.php';

// Sample food listings data
$sampleListings = [
    [
        'food_name' => 'Fresh Bread',
        'quantity' => '20 loaves',
        'expiry_date' => '2024-01-15',
        'location' => 'Colombo 03, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Rice Bags',
        'quantity' => '5 kg bags',
        'expiry_date' => '2024-12-31',
        'location' => 'Nugegoda, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Canned Vegetables',
        'quantity' => '15 cans',
        'expiry_date' => '2024-06-30',
        'location' => 'Dehiwala, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Fresh Fruits',
        'quantity' => '10 kg mixed',
        'expiry_date' => '2024-01-10',
        'location' => 'Mount Lavinia, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Milk Powder',
        'quantity' => '8 packets',
        'expiry_date' => '2024-08-15',
        'location' => 'Battaramulla, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Cooking Oil',
        'quantity' => '5 liters',
        'expiry_date' => '2024-10-20',
        'location' => 'Maharagama, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Sugar',
        'quantity' => '3 kg',
        'expiry_date' => '2024-12-31',
        'location' => 'Kohuwala, Sri Lanka',
        'donor_id' => 1
    ],
    [
        'food_name' => 'Tea Bags',
        'quantity' => '100 bags',
        'expiry_date' => '2024-09-30',
        'location' => 'Moratuwa, Sri Lanka',
        'donor_id' => 1
    ]
];

$successCount = 0;
$errorCount = 0;

foreach ($sampleListings as $listing) {
    $sql = "INSERT INTO food_listings (food_name, quantity, expiry_date, location, donor_id, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", 
        $listing['food_name'],
        $listing['quantity'],
        $listing['expiry_date'],
        $listing['location'],
        $listing['donor_id']
    );
    
    if ($stmt->execute()) {
        $successCount++;
    } else {
        $errorCount++;
    }
    $stmt->close();
}

echo json_encode([
    'success' => true,
    'message' => "Added $successCount sample listings successfully. Errors: $errorCount",
    'added_count' => $successCount,
    'error_count' => $errorCount
]);
?> 