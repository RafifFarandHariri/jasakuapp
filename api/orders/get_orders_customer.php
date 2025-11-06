<?php
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : die();

$stmt = $order->getOrdersByCustomer($customer_id);
$num = $stmt->rowCount();

if($num > 0) {
    $orders_arr = array();
    $orders_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $order_item = array(
            "id" => $id,
            "service_title" => $service_title,
            "service_images" => json_decode($service_images),
            "provider_name" => $provider_name,
            "provider_image" => $provider_image,
            "quantity" => $quantity,
            "total_price" => $total_price,
            "status" => $status,
            "notes" => $notes,
            "deadline" => $deadline,
            "created_at" => $created_at
        );
        array_push($orders_arr["data"], $order_item);
    }

    http_response_code(200);
    echo json_encode($orders_arr);
} else {
    http_response_code(404);
    echo json_encode(array(
        "success" => false,
        "message" => "No orders found for this customer."
    ));
}
?>