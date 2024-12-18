<?php

namespace App\Models;

use App\Config\Database as ConfigDatabase;

class Contact{
 
    private $conn;
    private $table_name = 'contacts';

    public $id;
    public $first_name;
    public $last_name;
    public $age;
    public $country;
    public $email;
    public $phone_number;

    public function __construct() {
        $database = new ConfigDatabase();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name} 
                  (first_name, last_name, age, country, email, phone_number) 
                  VALUES (:first_name, :last_name, :age, :country, :email, :phone_number)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone_number', $this->phone_number);

        return $stmt->execute() ? true : false;
    }

    public function read() {
        $query = "SELECT * FROM {$this->table_name} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table_name} 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      age = :age, 
                      country = :country, 
                      email = :email, 
                      phone_number = :phone_number 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone_number', $this->phone_number);

        return $stmt->execute() ? true : false;
    }

    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute() ? true : false;
    }

    public function findById($id) {
        $query = "SELECT * FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function search($searchTerm) {
        // Requête de recherche sur plusieurs champs
        $query = "SELECT * FROM {$this->table_name} 
                  WHERE first_name LIKE :search 
                  OR last_name LIKE :search 
                  OR country LIKE :search 
                  OR email LIKE :search 
                  OR phone_number LIKE :search";
        
        $stmt = $this->conn->prepare($query);
        
        // Ajouter les caractères % pour une recherche partout dans le texte
        $searchParam = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchParam);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Méthode de recherche avancée avec filtres multiples
    public function advancedSearch($filters = []) {
        $conditions = [];
        $bindParams = [];
    
        // Construire dynamiquement les conditions de recherche
        if (!empty($filters['first_name'])) {
            $conditions[] = "first_name LIKE :first_name";
            $bindParams[':first_name'] = "%{$filters['first_name']}%";
        }
    
        if (!empty($filters['last_name'])) {
            $conditions[] = "last_name LIKE :last_name";
            $bindParams[':last_name'] = "%{$filters['last_name']}%";
        }
    
        if (!empty($filters['country'])) {
            $conditions[] = "country LIKE :country";
            $bindParams[':country'] = "%{$filters['country']}%";
        }
    
        if (!empty($filters['min_age'])) {
            $conditions[] = "age >= :min_age";
            $bindParams[':min_age'] = $filters['min_age'];
        }
    
        if (!empty($filters['max_age'])) {
            $conditions[] = "age <= :max_age";
            $bindParams[':max_age'] = $filters['max_age'];
        }
    
        // Construire la requête de base
        $query = "SELECT * FROM {$this->table_name}";
        
        // Ajouter les conditions si elles existent
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
    
        // Ajouter un tri et une limite optionnels
        if (!empty($filters['order_by'])) {
            $query .= " ORDER BY " . $filters['order_by'];
        }
    
        if (!empty($filters['limit'])) {
            $query .= " LIMIT " . $filters['limit'];
        }
    
        // Préparer et exécuter la requête
        $stmt = $this->conn->prepare($query);
        
        // Lier les paramètres
        foreach ($bindParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}