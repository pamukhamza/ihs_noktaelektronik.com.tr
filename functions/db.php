<?php
class Database {
    private $host = 'noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com';
    private $db_name = 'noktanetdb';
    private $username = 'nokta';
    private $password = 'Dell28736.!';
    private $conn;

    // Constructor ile veritabanı bağlantısını başlat
    public function __construct() {
        $this->connect();
    }

    // Veritabanına bağlan
    private function connect() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

    // Fetch tek satır döndürme
    public function fetch($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", htmlspecialchars($value));
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // FetchAll tüm satırları döndürme
    public function fetchAll($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                // Only use htmlspecialchars for string values
                $stmt->bindValue(
                    ":$key", 
                    is_string($value) ? htmlspecialchars($value) : $value,
                    is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
                );
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // FetchColumn: Sadece bir kolon döndürme
    public function fetchColumn($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", htmlspecialchars($value));
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    // Yeni kayıt ekleme (INSERT)
    public function insert($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        if ($stmt->execute()) {
            return true; // Başarıyla eklendi
        }
        return false; // Hata oluştu
    }

    // Son eklenen kaydın ID'sini almak için fonksiyon
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    // Kayıt güncelleme (UPDATE)
    public function update($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        if ($stmt->execute()) {
            return true; // Başarıyla güncellendi
        }
        return false; // Hata oluştu
    }

    // Kayıt silme (DELETE)
    public function delete($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", htmlspecialchars($value));
        }
        if ($stmt->execute()) {
            return true; // Başarıyla silindi
        }
        return false; // Hata oluştu
    }
}