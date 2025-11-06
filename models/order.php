<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $customer_id;
    public $service_id;
    public $quantity;
    public $total_price;
    public $status;
    public $notes;
    public $deadline;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE ORDER
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET customer_id=:customer_id, service_id=:service_id, quantity=:quantity, 
                      total_price=:total_price, notes=:notes, deadline=:deadline";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":deadline", $this->deadline);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // GET ORDERS BY CUSTOMER
    public function getOrdersByCustomer($customer_id) {
        $query = "SELECT o.*, s.title as service_title, s.images as service_images, 
                         u.nama as provider_name, u.profile_image as provider_image
                  FROM " . $this->table_name . " o
                  LEFT JOIN services s ON o.service_id = s.id
                  LEFT JOIN users u ON s.provider_id = u.id
                  WHERE o.customer_id = :customer_id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->execute();
        
        return $stmt;
    }

    // GET ORDERS BY PROVIDER
    public function getOrdersByProvider($provider_id) {
        $query = "SELECT o.*, s.title as service_title, s.images as service_images,
                         u.nama as customer_name, u.profile_image as customer_image
                  FROM " . $this->table_name . " o
                  LEFT JOIN services s ON o.service_id = s.id
                  LEFT JOIN users u ON o.customer_id = u.id
                  WHERE s.provider_id = :provider_id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->execute();
        
        return $stmt;
    }

    // UPDATE ORDER STATUS
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status=:status, updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>