<?php
include "functions.php";
// Database connection parameters
$host = "localhost"; // Change to your database host
$username = "noktaelektronik"; // Change to your database username
$password = "Dell28736.!"; // Change to your database password
$database = "noktaelektronik_depo"; // Change to your database name

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['k_adi'];
    $password = $_POST['k_sifre'];

    // SQL query to check if the user exists
    $sql = "SELECT * FROM kullanicilar WHERE k_adi = ?";
    
    // Using prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $storedPasswordHash = $row['k_sifre']; // Replace 'password' with the actual column name storing hashed passwords

            // Verify the password
            if (password_verify($password, $storedPasswordHash)) {
                // Password is correct, redirect to a different page
                session_start();
                $_SESSION['k_adi'] = $username;
                header('Location: index');
                exit();
            } else {
                // Password is incorrect
                echo "Invalid username or password. Please try again.";
            }
        } else {
            // User not found
            echo "Invalid username or password. Please try again.";
        }
        
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <base href="https://www.noktaelektronik.com.tr/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>Depo Kontrol</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 bg-light">
    <div class="container">
        <main class="form-signin bg-white p-4 rounded shadow">
            <form method="post">
                <h1 class="h3 mb-3 fw-normal text-center">Nokta Stok Sayımı</h1>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="k_adi" name="k_adi" placeholder="Kullanıcı Adı">
                    <label for="k_adi">Kullanıcı Adı</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="k_sifre" name="k_sifre" placeholder="Şifre">
                    <label for="k_sifre">Şifre</label>
                </div>

                <button class="btn btn-primary w-100 py-2" type="submit">Giriş Yap</button>
            </form>
        </main>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

