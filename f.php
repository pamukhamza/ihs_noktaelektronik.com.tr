<?php
    // Get the visitor's IP address
    //$visitor_ip = $_SERVER['REMOTE_ADDR'];

    /*$allowed_ips = array(
        '213.14.137.98'
    );

    // Check if the visitor's IP is in the allowed IPs array
    if (!in_array($visitor_ip, $allowed_ips)) {
        // IP is not allowed, redirect to bakim.php
        header("Location: bakim.php");
        exit(); // Ensure that script execution stops after redirection
    }*/

    /* Kullanıcı, sayfa ve IP bilgilerini güncelle
    $stmt = $db->prepare("INSERT INTO aktif_log (ip, cihaz_bilgi) VALUES (:user_ip, :cihaz_bilgi)");
    $stmt->bindParam(':user_ip', $visitor_ip);
    $stmt->bindParam(':cihaz_bilgi', $cihaz_bilgi);
    $stmt->execute();*/

//front pages session
function session_on() {
    session_start();
}
function startSessionSafely() {
    if (session_status() == PHP_SESSION_NONE) {
        // Eğer session henüz başlatılmamışsa, başlat
        session_name("user_session");
        session_start();
    }
}


/* Siteye Giris Yapanlarin IPsi */
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