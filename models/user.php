<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nrp;
    public $nama;
    public $email;
    public $password;
    public $phone;
    public $profile_image;
    public $is_service_provider;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " SET nrp=:nrp, nama=:nama, email=:email, password=:password, phone=:phone";
        
        $stmt = $this->conn->prepare($query);
        
        // hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // bind values
        $stmt->bindParam(":nrp", $this->nrp);
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":phone", $this->phone);
        
        if($stmt->execute()) {
            return true;
        }
        
        $errorInfo = $stmt->errorInfo();
        error_log("Database error: " . $errorInfo[2]);
        return false;
    }

    // GET ALL USERS
    public function getUsers() {
        $query = "SELECT id, nrp, nama, email, phone, profile_image, is_service_provider FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // LOGIN USER
    public function login() {
        $query = "SELECT id, nrp, nama, email, password, phone, is_service_provider FROM " . $this->table_name . " WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        return $stmt;
    }
}
?>