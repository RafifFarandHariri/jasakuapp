<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

$provider_id = isset($_GET['provider_id']) ? $_GET['provider_id'] : die();

$stmt = $service->getServicesByProvider($provider_id);
$num = $stmt->rowCount();

if($num > 0) {
    $services_arr = array();
    $services_arr["success"] = true;
    $services_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $service_item = array(
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "price" => (float)$price,
            "images" => json_decode($images),
            "sold_count" => (int)$sold_count,
            "rating" => (float)$rating,
            "review_count" => (int)$review_count,
            "category_name" => $category_name,
            "is_active" => (bool)$is_active,
            "created_at" => $created_at
        );
        array_push($services_arr["data"], $service_item);
    }

    http_response_code(200);
    echo json_encode($services_arr);
} else {
    http_response_code(404);
    echo json_encode(array(
        "success" => false,
        "message" => "No services found for this provider."
    ));
}
?>