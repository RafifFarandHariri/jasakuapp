<?php
// Include file dengan path yang benar
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->nrp) && !empty($data->nama) && !empty($data->email) && !empty($data->password)) {
    $user->nrp = $data->nrp;
    $user->nama = $data->nama;
    $user->email = $data->email;
    $user->password = $data->password;
    $user->phone = $data->phone ?? "";

    if($user->register()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "User registered successfully."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to register user."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to register user. Data is incomplete."
    ));
}
?>