<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'hakkimizda';
$template = new Template('Nokta B2B - Hakkımızda', $currentPage);

$template->head();
$database = new Database();
?>
<style>
      .bi {vertical-align: -.125em;fill: currentColor;}
</style>
<body>
    <?php $template->header(); ?>
      <!-- Site Haritası -->
    <nav aria-label="breadcrumb" class="container mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
        </svg>
        <ol class="breadcrumb ">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="index">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden">Anasayfa</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Hakkımızda</li>
        </ol>
    </nav>
    <section class="container mb-3">
        <div class="row">
            <?php $template->pageLeftMenu(); ?>
            <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3">
                <div class="card">
                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/hakkimizda.png" alt="hakkimizda">
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <p><b>Hakkımızda</b><br/><br/>
                            Nokta Elektronik ve Bilişim Sistemleri, 20 yılı aşkın deneyimiyle elektronik ve bilişim sektöründe öncü bir kuruluş olarak tanınmaktadır. Geniş ürün yelpazesi ve güçlü üretim altyapısıyla sektördeki dinamiklerini sürekli olarak yenileyen firmamız, global pazarda dünya çapında lider markaların distribütörlüğünü üstlenmektedir. Türkiye’nin yanı sıra Ortadoğu, Türki Cumhuriyetler ve Balkan ülkelerinde proje yönetimi, ürün tedariği ve teknik destek alanlarında hizmet vermekteyiz. İstanbul merkezli firmamız, İstanbul ve Ankara’daki Proje ve Satış Ofisleri ile müşteri ihtiyaçlarına etkin çözümler sunmaktadır. Ayrıca, İzmir'de bulunan üretim tesisimizde Ar-Ge faaliyetleri gerçekleştirerek, proje odaklı yenilikçi çözümler geliştirmekteyiz.
                            <br/><br/>
                            Firmamızın temel prensibi, uluslararası standartlara uygun kalite ve yeniliği ön planda tutarak müşterilerine en yüksek hizmet standardını sunmaktır. ISO, 3P, ETL ve CE gibi dünya çapında tanınan ürün sertifikalandırma kuruluşlarıyla iş birliği yaparak, pazara sunduğumuz ürünlerin yüksek kalite ve güvenlik standartlarına uygun olmasını sağlamaktayız.
                            <br/><br/>
                            <b>Neden Nokta Elektronik?</b>
                            <br/>
                            Kuruluşunuz için en doğru çözümü seçmek istiyorsanız, Nokta Elektronik ve Bilişim Sistemleri sizin için ideal tercih olacaktır. Şirketimiz, ilk görüşmelerden itibaren keşif, sistem seçimi ve projelendirme aşamalarında ihtiyaçlarınıza uygun ve maliyet etkin çözümler sunmayı garanti eder.
                            <br/>
                            Nokta Elektronik’in profesyonel kadrosu, müşteri memnuniyeti ve hizmet kalitesini ön planda tutarak uzmanlaşmış personelden oluşmaktadır. Çalışanlarımızın bilgi ve becerileri, düzenli olarak güncellenen eğitim ve seminerlerle sürekli olarak geliştirilir.
                            <br/><br/>
                            Uluslararası ve yurtiçi referanslarla kanıtlanmış mühendislik başarımız, firmamızı sektördeki diğerlerinden ayırır ve bizimle çalışmayı avantajlı kılar. Nokta Elektronik, keşif hizmetlerinin ardından aşağıdaki aşamalarda müşterilerine kapsamlı destek sunar:
                                <br/><br/>
                            <li>Proje Öncesi Danışma ve Bilgilendirme</li>
                            <li>Sistem Seçimi, Projelendirme ve Teklif</li>
                            <li>Satış ve Sistem Kurulumu</li>
                            <li>Satış Sonrası Danışma ve Servis</li>
                            <li>Müşteri memnuniyeti odaklı yaklaşımımızla, projelerinizin her aşamasında yanınızdayız.</li>
                            <br/>
                            Saygılarımızla,<br/>
                            <b>Nokta Elektronik ve Bilişim Sistemleri</b>
                        </p>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
    <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/easing.min.js"></script>
<script src="assets/js/waypoints.min.js"></script>
<script>
    new WOW().init();
    $(document).ready(function(){
        $('.counter-value').each(function(){
            $(this).prop('Counter',0).animate({
                Counter: $(this).text()
            },{
                duration: 3000,
                easing: 'easeInQuad',
                step: function (now){
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
</script>
