<?php
require_once 'db.php';

header('Content-Type: application/json');
session_name("user_session");
session_start(); // Start the session to access user data
// Retrieve the JSON data sent from the JavaScript
$data = json_decode(file_get_contents('php://input'), true);
// Validate and process the data
if (isset($data['elementTag'])) {
    $userId = $_SESSION['id'];
 
        $database = new Database();
        $query = "INSERT INTO tiklama_takip (user_id, element_tag, element_text, element_id, element_classes)
                    VALUES (:user_id, :element_tag, :element_text, :element_id, :element_classes) ";
        $param =[
            'user_id' => $userId,
            'element_tag' => $data['elementTag'],
            'element_text' => $data['elementText'],
            'element_id' => $data['elementId'],
            'element_classes' => $data['elementClasses']
        ];
        $database ->insert($query, $param);
        echo json_encode(['status' => 'success']);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>