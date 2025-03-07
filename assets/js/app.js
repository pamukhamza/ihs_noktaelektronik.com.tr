function dynamicSil(gid, gel, customType, successMessage, redirectPage) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu eylem geri alınamaz!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: customType
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: successMessage /* Ürün Silindi! */,
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = redirectPage ;
                    }, 1000);
                }
            });
        }
    });
}
function updateSepetCount() {
    // AJAX isteği
    $.ajax({
        type: 'GET',
        url: 'php/sepet_count.php', // Sepet sayısını getirecek olan PHP dosyanızın yolunu belirtin
        dataType: 'json',
        success: function (response) {
            // AJAX başarılı olduğunda burası çalışacak
            if (response.success) {
                var sepetCountElement = $('#sepetCount');
                var sepetCountElement1 = $('#sepetCountM');
                sepetCountElement.html(response.sepetCount);
                sepetCountElement1.html(response.sepetCount);
            }
            console.log(response);
        },
        error: function (error) {
            console.error('AJAX hatası:', error);
        }
    });
}
function favoriKaldır(gid) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Ürün favorilerden kaldırılacak.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'functions/delete.php',
                data: {
                    'gid': gid,
                    type: 'favoriKaldır'
                },
                success: function (response) {
                    console.log(response);
                    Swal.fire({
                        title: "Ürün favorilerden kaldırıldı!",
                        toast: true,
                        position: 'top-end',
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000
                    });
                    // Favori kaldırıldıktan sonra sayfayı yenileme yerine, favori öğesini arayüzden kaldırma
                    $('#favori_' + gid).remove(); // Bu satırı ekleyin, favori öğesini kaldırır
                },error: function(xhr, status, error) { console.log(status, error +'hamza'); }
            });
        }
    });
}
function performSearch() {
    var stokKoduValue = $("#stokArama").val();
    var finalUrl = "tr/urunler?cat=&brand=&filter=&search=" + stokKoduValue;
    window.location.href = finalUrl;
}
function performSearchMobil() {
    var stokKoduValue = $("#stokArama1").val();
    var finalUrl = "tr/urunler?cat=&brand=&filter=&search=" + stokKoduValue;
    window.location.href = finalUrl;
}
function ebultenKaydet() {
    var email = $("#ebulten_mail").val();
    $.ajax({
        type: "POST",
        url: "functions/edit_info.php",  // Dosya yolu doğru mu?
        data: { 'ebulten_mail': email,
                type: 'ebulten_kaydet'},
        
        success: function(response) {
            console.log(response);
            if (response.cvp !== "success") {
                Swal.fire({
                    icon: 'error',
                    title: 'Lütfen geçerli bir E-Posta giriniz!',
                    toast: true,
                    position: 'center',
                    timer: 4000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'E-bülten kaydınız yapılmıştır!',
                    toast: true,
                    position: 'center',
                    timer: 4000,
                    showConfirmButton: false
                });
            }
        }
    });
    
}
function sepetKaldir(gid) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Ürün sepetinizden kaldırılacak.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'functions/delete.php',
                data: {
                    'gid': gid,
                    type: 'sepetKaldir'
                },
                success: function () {
                    Swal.fire({
                        title: "Ürün sepetten kaldırıldı!",
                        toast: true,
                        position: 'top-end',
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000
                    });
                    // Sepet öğesini DOM'dan kaldır
                    $('#sepet_' + gid).remove();
                    updateSepetCount();
                    toplamFiyatVeKDV();
                }
            });
        }
    });
    updateSepetCount();
    toplamFiyatVeKDV();
}
function sepeteFavoriEkle(fid, uid, id) {
    $.ajax({
        type: 'POST',
        url: 'functions/edit_info.php',
        data: {
            'uye_id': id,
            'urun_id': uid,
            type: 'sepeteFavoriEkle'
        },
        success: function () {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: ' Sepete Eklendi',
                showConfirmButton: false,
                timer: 1500
            });
            setTimeout(function () {
                window.location.reload();
            }, 1500);
        }
    });
}
function sepeteUrunEkle(uid, id, adet) {
    $.ajax({
        type: 'POST',
        url: 'functions/edit_info.php',
        data: {
            'uye_id': id,
            'urun_id': uid,
            'adet': adet,
            type: 'sepeteUrunEkle'

        },
        success: function (gel) {
            console.log(gel);
            const Toast = Swal.mixin({
                toast: true,
                position: "center",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Ürün sepetinize eklendi."
            });

            sepetGuncelle();
            updateSepetCount();
        }
    });
}
// URL parametresini kontrol etmek ve uyarı mesajını göstermek için fonksiyon
function checkAndGetWarning() {
    const urlParams = new URLSearchParams(window.location.search);
    const sParam = urlParams.get('s');

    if (sParam === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Güncelleme Kaydedilmiştir!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '2') {
        Swal.fire({
            icon: 'success',
            title: 'Kullanıcı Kaydı Başarılı!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }

    if (sParam === '10') {
        Swal.fire({
            icon: 'error',
            title: 'Dosya Fotoğraf Değil!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '11') {
        Swal.fire({
            icon: 'error',
            title: 'Dosya Boyutu 4MB den küçük olmalıdır!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '12') {
        Swal.fire({
            icon: 'error',
            title: 'Dosya Türü Hatalı!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '13') {
        Swal.fire({
            icon: 'error',
            title: 'Dosya Yüklenirken Hata Oluştu!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '14') {
        Swal.fire({
            icon: 'success',
            title: 'Dosya Yüklendi!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '15') {
        Swal.fire({
            icon: 'error',
            title: 'Fotoğraf Türü Hatalı!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '16') {
        Swal.fire({
            icon: 'error',
            title: 'Fotoğraf Yüklenirken Hata Oluştu!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '17') {
        Swal.fire({
            icon: 'error',
            title: 'Dosya Boyutu 4MB den küçük olmalıdır!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '18') {
        Swal.fire({
            icon: 'success',
            title: 'Katalog Başarıyla Yüklendi!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '19') {
        Swal.fire({
            icon: 'error',
            title: 'Kullanıcı adı veya Şifre Hatalı!',
            toast: true,
            position: 'center',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '30') {
        Swal.fire({
            icon: 'error',
            title: 'Bu sayfaya erişim izniniz yok!',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '20') {
        Swal.fire({
            icon: 'success',
            title: 'İletişim Formu Gönderilmiştir',
            toast: true,
            position: 'top-end',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (sParam === '22') {
        Swal.fire({
            text: "Lütfen mail adresinize gelen linke tıklayarak üyeliğinizi aktifleştirin!",
            icon: "success",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '23') {
        Swal.fire({
            text: "Please activate your account by clicking on the link sent to your e-mail address!",
            icon: "success",
            confirmButtonText: 'Ok',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '24') {
        Swal.fire({
            text: "Bilgileriniz başarıyla güncellenmiştir.",
            icon: "success",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            timer: 2000,
            showCloseButton: true
        });
    }
    if (sParam === '25') {
        Swal.fire({
            text: "Your information has been successfully updated.",
            icon: "success",
            confirmButtonText: 'Ok',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '26') {
        Swal.fire({
            text: "Üyeliğiniz aktif edilmiştir!",
            icon: "success",
            confirmButtonText: 'Ok',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '27') {
        Swal.fire({
            text: "Bu mail adresi kullanılıyor!",
            icon: "warning",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '28') {
        Swal.fire({
            text: "Bu tc kimlik no kullanılıyor!",
            icon: "warning",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '29') {
        Swal.fire({
            text: "Bu vergi no kullanılıyor!",
            icon: "warning",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '30') {
        Swal.fire({
            text: "Şifreler eşleşmiyor!",
            icon: "warning",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }
    if (sParam === '31') {
        Swal.fire({
            text: "Ödeme alınırken hata oluştu!",
            icon: "warning",
            confirmButtonText: 'Tamam',
            allowOutsideClick: false,
            showCloseButton: true
        });
    }

}
// Sayfa yüklendiğinde uyarı mesajını kontrol et
document.addEventListener("DOMContentLoaded", function () {
    checkAndGetWarning();
});

   
    