<?php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Simulate the POST data
$test_data = array(
    'provider_id' => 2,
    'title' => 'Jasa joki ngoding',
    'description' => 'Menyediakan jasa untuk ngoding tugas tugas bahasa pemrograman',
    'price' => 150000,
    'category_id' => 4,
    'images' => '["coding1.jpg", "coding2.jpg"]'
);

echo "<h2>🧪 Debug Create Service</h2>";

// Check if provider_id exists in users table
$check_user = $db->prepare("SELECT id, nama FROM users WHERE id = ?");
$check_user->execute([$test_data['provider_id']]);
$user = $check_user->fetch();

if($user) {
    echo "✅ Provider found: " . $user['nama'] . "<br>";
} else {
    echo "❌ Provider ID " . $test_data['provider_id'] . " not found in users table<br>";
}

// Check if category_id exists
$check_category = $db->prepare("SELECT id, name FROM categories WHERE id = ?");
$check_category->execute([$test_data['category_id']]);
$category = $check_category->fetch();

if($category) {
    echo "✅ Category found: " . $category['name'] . "<br>";
} else {
    echo "❌ Category ID " . $test_data['category_id'] . " not found<br>";
}

// Try to insert the service
try {
    $query = "INSERT INTO services 
              SET provider_id=:provider_id, category_id=:category_id, title=:title, 
                  description=:description, price=:price, images=:images";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":provider_id", $test_data['provider_id']);
    $stmt->bindParam(":category_id", $test_data['category_id']);
    $stmt->bindParam(":title", $test_data['title']);
    $stmt->bindParam(":description", $test_data['description']);
    $stmt->bindParam(":price", $test_data['price']);
    $stmt->bindParam(":images", $test_data['images']);
    
    if($stmt->execute()) {
        $new_id = $db->lastInsertId();
        echo "🎉 SUCCESS: Service created with ID: " . $new_id . "<br>";
        
        // Verify the data was inserted
        $verify = $db->prepare("SELECT * FROM services WHERE id = ?");
        $verify->execute([$new_id]);
        $new_service = $verify->fetch();
        
        if($new_service) {
            echo "✅ Verified: Service data saved in database<br>";
            echo "📋 Title: " . $new_service['title'] . "<br>";
        }
    } else {
        echo "❌ FAILED: Execute failed<br>";
        $error = $stmt->errorInfo();
        echo "Error: " . $error[2] . "<br>";
    }
} catch(PDOException $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "<br>";
}

// Show current services count
$count = $db->query("SELECT COUNT(*) as total FROM services")->fetch();
echo "<br>📊 Total services in database: " . $count['total'] . "<br>";

// Show latest services
echo "<h3>Latest Services:</h3>";
$services = $db->query("SELECT id, title, provider_id FROM services ORDER BY id DESC LIMIT 5");
while($service = $services->fetch()) {
    echo "- ID: " . $service['id'] . " | Title: " . $service['title'] . " | Provider: " . $service['provider_id'] . "<br>";
}
?>