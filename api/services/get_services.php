<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

$stmt = $service->getServices();
$num = $stmt->rowCount();

if($num > 0) {
    $services_arr = array();
    $services_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $service_item = array(
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "price" => $price,
            "images" => json_decode($images),
            "sold_count" => $sold_count,
            "rating" => $rating,
            "review_count" => $review_count,
            "provider_name" => $provider_name,
            "provider_image" => $provider_image,
            "category_name" => $category_name,
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
        "message" => "No services found."
    ));
}
?>