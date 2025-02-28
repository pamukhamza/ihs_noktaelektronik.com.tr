<?php
require_once '../../baglanti.php'; // Veritabanı bağlantınızı içeren dosya

// Önbellek kontrolü
$cache_file = 'banner_cache.html';
$cache_time = 3600; // 1 saat

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    // Önbellekteki içeriği kullan
    readfile($cache_file);
    exit;
}

ob_start(); // Çıktı tamponlamayı başlat
?>

<div class="d-flex mt-4">
    <div class="row banner-responsive">
        <?php if($user_language == 'tr'){  ?>
            <div class="col-12 col-md-8 d-flex justify-content-center">
            <div class="row">
            <?php
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '1'");
            $q->execute();
            $d = $q->fetch(PDO::FETCH_ASSOC);
            if ($d["aktif"] == 1) { ?>
                <div class="col-12 col-md-8 mt-2">
                    <a href="<?php echo htmlspecialchars($d['banner_link']); ?>">
                        <img src="assets/images/<?php echo htmlspecialchars($d['banner_foto']); ?>" class="shadow-sm rounded" data-banner-id="1" width="100%" alt="">
                    </a>
                </div>
            <?php } else {
                $q = $db->prepare("SELECT * FROM nokta_banner_video WHERE id = '1'");
                $q->execute();
                $d = $q->fetch(PDO::FETCH_ASSOC); ?>
                <div class="col-12 col-md-8 mt-2 position-relative">
                    <video id="myVideo" class="rounded shadow-sm" data-banner-video-id="1" style="width: 100%; height: auto;" controls>
                        <source src="assets/uploads/videolar/banner_video1.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="play-button position-absolute">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 5v14l11-7L8 5z" fill="#fff"/>
                        </svg>
                    </div>
                </div>
            <?php } ?>
            <?php $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '2'");
            $q -> execute(  );
            $d = $q->fetchAll();
            foreach( $d as $k => $row ){?>
                <div class="col-12 col-md-4 mt-2">
                    <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="2" width="100%" alt=""></a>
                </div>
            <?php }
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '3'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    <div class="col-12 col-md-4 mt-3">
                        <a href="<?php echo  $row['banner_link']; ?>"><img src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="3" width="100%" height="100%" alt=""></a>
                    </div>
                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '4'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    <div class="col-12 col-md-8 mt-3">
                        <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="4" width="100%" alt=""></a>
                    </div>

                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '5'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <a href="<?php echo  $row['banner_link']; ?>"><img src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm mt-2 rounded" data-banner-id="5" height="100%" width="100%" alt=""></a>
                    </div>
                <?php }}} elseif($user_language == 'en'){?>
            <div class="col-12 col-md-8 d-flex justify-content-center ">
            <div class="row">
            <?php $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '1'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){ ?>
                    <div class="col-12 col-md-8 mt-2">
                        <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="1" width="100%" alt=""></a>
                    </div>
                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '2'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    <div class="col-12 col-md-4 mt-2">
                        <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="2" width="100%" alt=""></a>
                    </div>
                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '3'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    <div class="col-12 col-md-4 mt-3">
                        <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="3" width="100%" alt=""></a>
                    </div>
                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '4'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    <div class="col-12 col-md-8 mt-3">
                        <a href="<?php echo  $row['banner_link']; ?>"><img  src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="4" width="100%" alt=""></a>
                    </div>

                <?php }}
            $q = $db->prepare("SELECT * FROM nokta_banner WHERE id = '5'");
            $q -> execute(  );
            if ( $d = $q->fetchAll() ){
                foreach( $d as $k => $row ){?>
                    </div>
                    </div>
                    <div class="col-12 col-md-4 ">
                        <a href="<?php echo  $row['banner_link']; ?>"><img src="assets/images/<?php echo  $row['banner_foto']; ?>" class="shadow-sm mt-2 rounded" data-banner-id="5" width="100%" alt=""></a>
                    </div>
                <?php }}}?>
    </div>
</div>
<?php
$output = ob_get_clean(); // Tamponu al ve temizle

// Çıktıyı önbelleğe kaydet
file_put_contents($cache_file, $output);

// Çıktıyı göster
echo $output;
?>
