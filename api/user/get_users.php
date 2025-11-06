<?php
include_once '../../config/database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$stmt = $user->getUsers();
$num = $stmt->rowCount();

if($num > 0) {
    $users_arr = array();
    $users_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $user_item = array(
            "id" => $id,
            "nrp" => $nrp,
            "nama" => $nama,
            "email" => $email,
            "phone" => $phone,
            "profile_image" => $profile_image,
            "is_service_provider" => $is_service_provider
        );
        array_push($users_arr["data"], $user_item);
    }

    http_response_code(200);
    echo json_encode($users_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No users found."));
}
?>