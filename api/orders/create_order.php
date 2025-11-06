<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->customer_id) && !empty($data->service_id) && !empty($data->total_price)) {
    $order->customer_id = $data->customer_id;
    $order->service_id = $data->service_id;
    $order->quantity = $data->quantity ?? 1;
    $order->total_price = $data->total_price;
    $order->notes = $data->notes ?? "";
    $order->deadline = $data->deadline ?? null;
    $order->status = "pending";

    $order_id = $order->create();

    if($order_id) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Order created successfully.",
            "order_id" => $order_id
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create order."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create order. Data is incomplete."
    ));
}
?>