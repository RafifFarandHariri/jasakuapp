<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->status)) {
    $order->id = $data->id;
    $order->status = $data->status;

    if($order->updateStatus()) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Order status updated successfully."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to update order status."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to update order status. Data is incomplete."
    ));
}
?>