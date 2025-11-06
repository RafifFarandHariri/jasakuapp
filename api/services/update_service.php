<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Service.php';

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->provider_id) && !empty($data->title)) {
    $service->id = $data->id;
    $service->provider_id = $data->provider_id;
    $service->title = $data->title;
    $service->description = $data->description ?? "";
    $service->price = $data->price;
    $service->category_id = $data->category_id ?? 1;
    $service->images = $data->images ?? "[]";
    $service->is_active = $data->is_active ?? 1;

    if($service->update()) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Service updated successfully."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to update service."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to update service. Data is incomplete."
    ));
}
?>