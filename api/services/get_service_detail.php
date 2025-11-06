<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

$service_id = isset($_GET['id']) ? $_GET['id'] : die();

$stmt = $service->getServiceById($service_id);
$num = $stmt->rowCount();

if($num > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $service_detail = array(
        "id" => $row['id'],
        "title" => $row['title'],
        "description" => $row['description'],
        "price" => (float)$row['price'],
        "images" => json_decode($row['images']),
        "sold_count" => (int)$row['sold_count'],
        "rating" => (float)$row['rating'],
        "review_count" => (int)$row['review_count'],
        "provider_id" => $row['provider_id'],
        "provider_name" => $row['provider_name'],
        "provider_image" => $row['provider_image'],
        "provider_phone" => $row['provider_phone'],
        "provider_email" => $row['provider_email'],
        "category_name" => $row['category_name'],
        "created_at" => $row['created_at']
    );

    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "data" => $service_detail
    ));
} else {
    http_response_code(404);
    echo json_encode(array(
        "success" => false,
        "message" => "Service not found."
    ));
}
?>