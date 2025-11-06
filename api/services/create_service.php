<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields - sesuai dengan struktur database
if(!empty($data->title) && !empty($data->description) && !empty($data->price) && 
   !empty($data->category_id) && !empty($data->provider_id)) {
    
    $service->title = $data->title;
    $service->description = $data->description;
    $service->price = $data->price;
    $service->category_id = $data->category_id;
    $service->provider_id = $data->provider_id;
    
    // Handle images - pastikan format JSON valid
    if(!empty($data->images)) {
        if(is_array($data->images)) {
            $service->images = json_encode($data->images);
        } else {
            $service->images = $data->images; // jika sudah string JSON
        }
    } else {
        $service->images = "[]";
    }
    
    // Set default values
    $service->sold_count = $data->sold_count ?? 0;
    $service->rating = $data->rating ?? 0.00;
    $service->review_count = $data->review_count ?? 0;
    $service->is_active = $data->is_active ?? 1;

    if($service->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Service created successfully."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create service."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create service. Data is incomplete.",
        "debug" => array(
            "title" => !empty($data->title) ? "filled" : "empty",
            "description" => !empty($data->description) ? "filled" : "empty",
            "price" => !empty($data->price) ? "filled" : "empty",
            "category_id" => !empty($data->category_id) ? "filled" : "empty",
            "provider_id" => !empty($data->provider_id) ? "filled" : "empty"
        )
    ));
}
?>