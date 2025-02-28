<?php
require_once '../db.php';

// Alt kategorileri bulan fonksiyon
class UrunDeneme
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function altKategorileriBul($kategori_id, $alt_kategori_ids = []) {
        $result = $this->db->fetchAll("SELECT id FROM nokta_kategoriler WHERE parent_id = :kategori_id", [
            'kategori_id' => $kategori_id
        ]);

        foreach ($result as $row) {
            $alt_kategori_ids[] = $row['id'];
            $alt_kategori_ids = $this->altKategorileriBul($row['id'], $alt_kategori_ids);
        }
        return $alt_kategori_ids;
    }
}
?>