<?php
include_once '../../config/database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)) {
    $user->email = $data->email;
    
    $stmt = $user->login();
    
    if($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $nrp = $row['nrp'];
        $nama = $row['nama'];
        $email = $row['email'];
        $hashed_password = $row['password'];
        $is_service_provider = $row['is_service_provider'];
        
        if(password_verify($data->password, $hashed_password)) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful.",
                "user" => array(
                    "id" => $id,
                    "nrp" => $nrp,
                    "nama" => $nama,
                    "email" => $email,
                    "is_service_provider" => $is_service_provider
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login failed. Wrong password."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "User not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
}
?>