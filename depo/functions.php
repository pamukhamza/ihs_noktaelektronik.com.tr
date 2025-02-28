<?php
$servername = "localhost";
$username = "noktaelektronik";
$password = "Dell28736.!";
$database = "noktaelektronik_depo";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Set UTF-8 character set
$conn->set_charset("utf8");

// PDO bağlantısı
try {
    $db = new PDO("mysql:host=localhost;dbname=noktaelektronik_depo;charset=utf8", "noktaelektronik", "Dell28736.!");
    $db->exec("SET NAMES 'utf8'");
    $db->exec("SET CHARACTER SET utf8");
    $db->exec("SET COLLATION_CONNECTION = 'utf8_general_ci'");
} catch (PDOException $e) {
    print $e->getMessage();
}

function session() {
  session_start();
  if (!isset($_SESSION['k_adi'])) {
      header("Location: login?s=2");
      exit(); // Dikkat: header'dan sonra exit() kullanarak kodun devam etmesini önleyin.
  }
}

function IP(){
    if(getenv("HTTP_CLIENT_IP")){
        $ip = getenv("HTTP_CLIENT_IP");
    }
    elseif(getenv("HTTP_X_FORWARDED_FOR")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        if(strstr($ip, ',')){
            $tmp = explode (',',$ip);
            $ip = trim($tmp[0]);
        }
    }
    else{
        $ip = getenv("REMOTE_ADDR");
    }
    return $ip;
}

function adminheaders(){?>
    <header class="p-3 mb-3 border-bottom">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      
      <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
      
        <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"/></svg>
      </a>
      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <li><a href="index.php" class="nav-link px-2 link-secondary">Depo Seç</a></li>
        <li><a href="liste.php" class="nav-link px-2 link-secondary">Sayım Listesi</a></li>
        <li><a href="stokekle.php" class="nav-link px-2 link-secondary">Olmayan Stok Ekle</a></li>
      </ul>
      <div class="dropdown text-end">
        <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="assets/img/user.png" width="32" height="32" class="rounded-circle"/>
        </a>
        <ul class="dropdown-menu text-small">
        
          <li><a class="dropdown-item"><?php echo $_SESSION['k_adi'];?></a></li>
          <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
        </ul>
      </div>
    </div>
  </div>
</header><?php
}

if (isset($_POST['stok_kaydet'])) {

  session();
  $connection = mysqli_connect('localhost', 'noktaelektronik', 'Dell28736.!', 'noktaelektronik_depo');
    mysqli_set_charset($connection, 'utf8');
  $stok_kodu = $_POST['stok_kodu'];
  $stok_adedi = $_POST['stok_adedi'];
  $depoid = $_POST['depoid'];
  $aciklama = $_POST['aciklama'];
  $kullanicisi = $_SESSION['k_adi'];
  // Veritabanında stok kodunun varlığını kontrol et
  $q = $db->prepare("SELECT COUNT(*) AS stok_count FROM products WHERE stok_kodu = :stok_kodu");
  $q->bindParam(':stok_kodu', $stok_kodu, PDO::PARAM_STR);
  $q->execute();
  $result = $q->fetch(PDO::FETCH_ASSOC);
  
  if ($result['stok_count'] > 0) {
      // Stok kodu veritabanında mevcut, işleme devam edebilirsiniz
      $query = "INSERT INTO stoklar (stok_kodu, stok_adedi, depo_id, stok_kaydeden, aciklama) VALUES (?, ?, ?, ?, ?)";
      $stmt = $connection->prepare($query);
      $stmt->bind_param("siiss", $stok_kodu, $stok_adedi, $depoid, $kullanicisi, $aciklama);
      $stmt->execute();
      $stmt->close();
      header("Location: admin.php?id=$depoid&s=1");
  } else {
      // Stok kodu veritabanında mevcut değil, hata mesajı gösterebilirsiniz
      header("Location: admin.php?id=$depoid&s=2");
  }
}

if (isset($_POST['yeni_stok_kaydet'])) {

  session();
  $connection = mysqli_connect('localhost', 'noktaelektronik', 'Dell28736.!', 'noktaelektronik_depo');
  $stok_kodu = $_POST['stok_kodu'];
  $stok_adedi = $_POST['stok_adedi'];
  $aciklama = $_POST['aciklama'];
  $kullanicisi = $_SESSION['k_adi'];

      // Stok kodu veritabanında mevcut, işleme devam edebilirsiniz
      $query = "INSERT INTO yeni_stoklar (stok_kodu, stok_adedi,  stok_kaydeden, aciklama) VALUES (?, ?, ?, ?)";
      $stmt = $connection->prepare($query);
      $stmt->bind_param("siss", $stok_kodu, $stok_adedi,  $kullanicisi, $aciklama);
      $stmt->execute();
      $stmt->close();
      header("Location: stokekle.php?s=1");
}