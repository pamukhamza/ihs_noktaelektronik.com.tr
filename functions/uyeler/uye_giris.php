<?php
session_name("user_session");
session_start();
require_once  __DIR__. '/../db.php';
$db = new Database();

function checkRememberMeToken($db, $token): ?array {
    // Validate the token
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    // Check the token against the database
    $result = $db->fetch("SELECT * FROM uyeler WHERE remember_token = :token", ['token' => $token]);

    return $result ? $result : null;
}

function handleRememberMe($db) {
    define('COOKIE_DURATION', 86400 * 7); // 7 days

    if (!isset($_SESSION["id"]) && isset($_COOKIE["remember_me"])) {
        $token = $_COOKIE["remember_me"];
        $user = checkRememberMeToken($db, $token);

        if ($user && strtotime($user['token_expires']) >= time()) {
            startSessionSafely();
            // Set session variables
            $_SESSION["id"] = $user["id"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["firma"] = $user["firmaUnvani"];
            $_SESSION['BLKODU'] = $user['BLKODU'];
            $_SESSION["ad"] = $user["ad"];
            $_SESSION["soyad"] = $user["soyad"];

            // Generate a new token for security reasons
            $newToken = bin2hex(random_bytes(32));
            setcookie("remember_me", $newToken, time() + COOKIE_DURATION, "/");
            // Update the token in the database
            $db->update("UPDATE uyeler SET remember_token = :newToken, token_expires = :tokenExpires WHERE id = :id", [
                'newToken' => $newToken,
                'tokenExpires' => date('Y-m-d H:i:s', time() + COOKIE_DURATION),
                'id' => $user["id"]
            ]);
        }
    }
}

function validateGirisForm($db) {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        return "Geçersiz istek yöntemi.";
    }

    $email = $_POST["email"];
    $password = $_POST["parola"];
    $rememberMe = isset($_POST["remember_me"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Geçersiz e-posta formatı";
    }

    $result = $db->fetch("SELECT * FROM uyeler WHERE email = :email", ['email' => $email]);

    if ($result) {
        if ($result['aktivasyon'] == 0) {
            return "Hesabınızı aktive etmek için lütfen e-posta üzerinden gelen aktivasyonu onaylayın.";
        }

        $storedPasswordHash = strtolower($result['parola']);
        $userpsw = md5($password);

        if ($userpsw === $storedPasswordHash) {
            session_name("user_session");
            session_start();
            $_SESSION['email'] = $email;
            $_SESSION['firma'] = $result['firmaUnvani'];
            $_SESSION['BLKODU'] = $result['BLKODU'];
            $_SESSION['ad'] = $result['ad'];
            $_SESSION['soyad'] = $result['soyad'];
            $_SESSION['id'] = $result['id'];

            $satis_temsilcisi = $result['satis_temsilcisi'];

            session_regenerate_id(true);

            $aktivite = "Giriş yaptı";

            $currentDate = date("d.m.Y H:i:s");
            $giristarihi = date("d.m.Y H:i:s", strtotime($currentDate . " +3 hours"));

            $date = date("d.m.y");

            $db->update("UPDATE uyeler SET son_giris = :son_giris WHERE id = :id", [
                'son_giris' => $date,
                'id' => $_SESSION['id']
            ]);

            $db->insert("INSERT INTO b2b_aktif_kullanicilar (uye_id, firma, BLKODU, aktivite, son_aktif, satis_temsilcisi) VALUES (:uye_id, :firma, :BLKODU, :aktivite, :son_aktif, :satis_temsilcisi)", [
                'uye_id' => $_SESSION['id'],
                'firma' => $_SESSION['firma'],
                'BLKODU' => $_SESSION['BLKODU'],
                'aktivite' => $aktivite,
                'son_aktif' => $giristarihi,
                'satis_temsilcisi' => $satis_temsilcisi
            ]);

            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie("remember_me", $token, time() + (86400 * 7), "/");

                // Store the token in the database for future reference
                $bitisTarihi = date('Y-m-d H:i:s', time() + (86400 * 7));
                $db->update("UPDATE uyeler SET remember_token = :token, token_expires = :bitis_tarihi WHERE id = :id", [
                    'token' => $token,
                    'bitis_tarihi' => $bitisTarihi,
                    'id' => $_SESSION['id']
                ]);
            }
            return true;
        } else {
            return "Geçersiz şifre.";
        }
    } else {
        return "Geçersiz e-posta.";
    }
    return "Veritabanı hatası.";
}
?>