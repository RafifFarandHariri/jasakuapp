<?php
class Service {
    private $conn;
    private $table_name = "services";

    public $id;
    public $provider_id;
    public $category_id;
    public $title;
    public $description;
    public $price;
    public $images;
    public $sold_count;
    public $rating;
    public $review_count;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE SERVICE
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET provider_id=:provider_id, category_id=:category_id, title=:title, 
                      description=:description, price=:price, images=:images";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":provider_id", $this->provider_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":images", $this->images);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // GET ALL SERVICES (Untuk home page)
    public function getServices() {
        $query = "SELECT s.*, u.nama as provider_name, u.profile_image as provider_image, c.name as category_name 
                  FROM " . $this->table_name . " s
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.is_active = 1
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // GET SERVICES BY CATEGORY
    public function getServicesByCategory($category_id) {
        $query = "SELECT s.*, u.nama as provider_name, u.profile_image as provider_image, c.name as category_name 
                  FROM " . $this->table_name . " s
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.category_id = :category_id AND s.is_active = 1
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->execute();
        
        return $stmt;
    }

    // GET SERVICES BY PROVIDER (Untuk profile provider)
    public function getServicesByProvider($provider_id) {
        $query = "SELECT s.*, c.name as category_name 
                  FROM " . $this->table_name . " s
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.provider_id = :provider_id
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":provider_id", $provider_id);
        $stmt->execute();
        
        return $stmt;
    }

    // GET SERVICE BY ID (Detail service)
    public function getServiceById($id) {
        $query = "SELECT s.*, u.nama as provider_name, u.profile_image as provider_image, 
                         u.phone as provider_phone, u.email as provider_email, c.name as category_name 
                  FROM " . $this->table_name . " s
                  LEFT JOIN users u ON s.provider_id = u.id
                  LEFT JOIN categories c ON s.category_id = c.id
                  WHERE s.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt;
    }

    // UPDATE SERVICE
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description, price=:price, 
                      category_id=:category_id, images=:images, is_active=:is_active
                  WHERE id=:id AND provider_id=:provider_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":images", $this->images);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":provider_id", $this->provider_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE SERVICE (Soft delete)
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET is_active=0 WHERE id=:id AND provider_id=:provider_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":provider_id", $this->provider_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>