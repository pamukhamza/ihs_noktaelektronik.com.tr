///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*Silme İşlemleri */

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
                        window.location.href = redirectPage /* adminSlider.php */;
                    }, 1000);
                }
            });
        }
    });
}
function teknikSil(gid) {
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
                type:'POST',
                url:'php/delete.php',
                data:{
                    'gid': gid,
                    type : 'teknik-servis'
                },
                success: function(){
                    Swal.fire({position: 'center',icon:'success',title: gid + ' id numaralı arıza kaydı silindi!',showConfirmButton: false,timer: 1000});
                    setTimeout(function() {window.location.href = 'https://www.noktaelektronik.com.tr/admin/adminTdp';}, 1000);
                }
            });
        }
    });
}
function blogSil(gid,gel) {
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
                type:'POST',
                url:'php/delete.php',
                data:{
                    'gid': gid,
                    'gel': gel,
                    type : 'blog'
                },
                success: function(){
                    Swal.fire({position: 'center',icon:'success',title: gid + ' id numaralı haber silindi!',showConfirmButton: false,timer: 1000});
                    setTimeout(function() {window.location.href = 'https://www.noktaelektronik.com.tr/admin/icerikyonetimi/adminBlog';}, 1000);
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

function markaSil(gid) {
    Swal.fire({
        title: 'Marka silinecek, emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'marka'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı marka silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminMarka.php';
                    }, 1000);
                }
            });
        }
    });
}

function filtreSil(gid) {
    Swal.fire({
        title: 'Filtre silinecek, emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'filtre'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı filtre silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminFiltre';
                    }, 1000);
                }
            });
        }
    });
}
function filtreKategoriSil(gid) {
    Swal.fire({
        title: 'Kategori silinecek, emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'filtreKategori'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı kategori silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminFiltre';
                    }, 1000);
                }
            });
        }
    });
}

function kullaniciSil(gid) {
    Swal.fire({
        title: 'Kullanıcı silinecek, emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'user'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı kullanıcı silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/ayarlar/adminKullanicilar';
                    }, 1000);
                }
            });
        }
    });
}

function kargoSil(gid) {
    Swal.fire({
        title: 'Kargo firması silinecek, emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'kargo'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı kargo firması silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/ayarlar/adminKargoFirmalari';
                    }, 1000);
                }
            });
        }
    });
}

function urunSil(gid) {
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
                type: 'POST',
                url: 'php/delete.php',
                data: {
                    'gid': gid,
                    type: 'urun'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı urun silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminUrunler.php';
                    }, 1000);
                }
            });
        }
    });
}

function urunFotoSil(gid, gel, urun) {
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
                    type: 'urun_foto'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı fotoğraf silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminUrunDuzenle?id=' + urun;
                    }, 1000);
                }
            });
        }
    });
}

function urunAnaFotoSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'urun_ana_foto'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı fotoğraf silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminUrunDuzenle?id=' + gid;
                    }, 1000);
                }
            });
        }
    });
}

function adresSil(gid) {
    Swal.fire({
        title: 'Adres silinecek, emin misiniz?',
        text: 'Bu işlem geri alınmaz!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    type: 'adres'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı adres silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/siteduzenleme/adminIletisim.php';
                    }, 1000);
                }
            });
        }
    });
}

function dosyaTipiSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'dosyaTipi'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı dosya silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminDosyaTipleri.php';
                    }, 1000);
                }
            });
        }
    });
}

function yuklemeSil(gid) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    type: 'yuklemeSil'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı dosya yolu silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminYuklemeBasliklari.php';
                    }, 1000);
                }
            });
        }
    });
}

function sliderSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'slider'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı slider silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/siteduzenleme/adminSlider';
                    }, 1000);
                }
            });
        }
    });
}
function popupSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'popup'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı popup silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminKampanyalar';
                    }, 1000);
                }
            });
        }
    });
}

function varyasyonSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'varyasyon'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Varyasyon silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminUrunVaryasyon.php';
                    }, 1000);
                }
            });
        }
    });
}
function kampanyaSil(gid, gel) {
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
                url: '../php/delete.php',
                type: 'POST',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'kampanya'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Kampanya silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminKampanyalar.php';
                    }, 1000);
                }
            });
        }
    });
}

function indirmeSil(gid, gel, id) {
    urunid = id;
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
                    type: 'indirme'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı indirme dosyası silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/urunler/adminUrunDuzenle.php?id=' + urunid + '&s=1';
                    }, 1000);
                }
            });
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

                    // Favori kaldırıldıktan sonra sayfayı yenileme
                    // Ancak bu satırı kaldırın, çünkü sayfa yenilenmeyecek
                    // setTimeout(function () { window.location.href = 'favoriler?lang=' + lang }, 1000);

                    // Favori kaldırıldıktan sonra sayfayı yenileme yerine, favori öğesini arayüzden kaldırma
                    $('#favori_' + gid).remove(); // Bu satırı ekleyin, favori öğesini kaldırır
                },error: function(xhr, status, error) { console.log(status, error); }
            });
        }
    });
}

function bannerSil(gid, gel) {
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
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    'gel': gel,
                    type: 'banner'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı banner silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminBanner.php';
                    }, 1000);
                }
            });
        }
    });
}


function haberSil(gid, gel) {
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
                type: 'POST',
                url: '../php/delete.php',
                data: {
                    'gid': gid,
                    'gel': gel
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı haber silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'adminHaber.php';
                    }, 1000);
                }
            });
        }
    });
}

function performSearch(user_language) {
    var stokKoduValue = $("#stokArama").val();
    var finalUrl = "urunler?lang=" + user_language + "&cat=&brand=&filter=&search=" + stokKoduValue;
    window.location.href = finalUrl;
}
function performSearchMobil(user_language) {
    var stokKoduValue = $("#stokArama1").val();
    var finalUrl = "urunler?lang=" + user_language + "&cat=&brand=&filter=&search=" + stokKoduValue;
    window.location.href = finalUrl;
}

function katalogSil(gid, foto, file) {
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
                type: 'POST',
                url: 'php/delete.php',
                data: {
                    'gid': gid,
                    'foto': foto,
                    'file': file,
                    type: 'katalog'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı katalog silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/icerikyonetimi/adminKatalog.php';
                    }, 1000);
                }
            });
        }
    });
}

function ebultenKaydet() {
    var email = $("#ebulten_mail").val();
    $.ajax({
        type: "POST",
        url: "function.php",
        data: {ebulten_mail: email, type: 'ebulten_kaydet'},
        dataType: 'json',
        success: function(response) {
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


function ebultenSil(gid) {
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
                type: 'POST',
                url: 'php/delete.php',
                data: {
                    'gid': gid,
                    type: 'ebulten'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı mail silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/icerikyonetimi/adminEbulten.php';
                    }, 1000);
                }
            });
        }
    });
}

function formSil(gid) {
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
                type: 'POST',
                url: 'php/delete.php',
                data: {
                    'gid': gid,
                    type: 'form'
                },
                success: function () {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: gid + ' id numaralı mesaj silindi!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                    setTimeout(function () {
                        window.location.href = 'admin/icerikyonetimi/adminIletisimForm.php';
                    }, 1000);
                }
            });
        }
    });
}

function teklifSil(gid) {
    $.ajax({
        type: 'POST',
        url: 'php/silTeklif.php',
        data: 'gid=' + gid,
        success: function () {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: gid + ' id numaralı teklif silindi!',
                showConfirmButton: false,
                timer: 1500
            });
            setTimeout(function () {
                window.location.href = 'pages/adminTeklif.php';
            }, 1500);
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*Silme İşlemleri Sonu */
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Favoriyi Sepete Ekle
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
        url: 'php/edit_info.php',
        data: {
            'uye_id': id,
            'urun_id': uid,
            'adet': adet,
            type: 'sepeteUrunEkle'
        },
        success: function () {
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

/*Aktif Pasif Ürün */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifUrun', function () {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_urunler";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});
/*Aktif Pasif Ürün */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifProje', function () {
        var id = $(this).attr("id");
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasifProje.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/*Aktif Pasif Yeni Ürün */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifYeni', function () {
        var durum = ($(this).is(':checked')) ? '1' : '0';
        var id = $(this).data('yeni-id');
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.
        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/yeniAktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function (gel) {
                alert('Hata');
                alert(gel);
            }
        });
    });
});

/*Aktif Pasif Banka Bilgisi */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifBankaBilgisi', function () {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_banka_bilgileri";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});



/*Aktif Pasif İlan */
$(document).ready(function () {
    $('.aktifPasifIlan').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_ilanlar";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/*Aktif Pasif Marka */
$(document).ready(function () {
    $('.aktifPasifMarka').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_urun_markalar_1";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/* Aktif Pasif Haber */
$(document).ready(function () {
    $('.aktifPasifHaber').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_haber";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/*Aktif Pasif Blog */
$(document).ready(function () {
    $('.aktifPasifBlog').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_blog";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function (gel) {
                alert('Hata');
            }
        });
    });
});

/* Aktif Pasif Slider */
$(document).ready(function () {
    $('.aktifPasifSlider').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "noktaslider";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function (gel) {
                alert('Hata');
            }
        });
    });
});
$(document).ready(function () {
    $('.aktifPasifPromosyon').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "promosyon";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function (gel) {
                alert('Hata');
            }
        });
    });
});
/* Aktif Pasif Popup */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifPopup', function () {
        var id = $(this).data('popup-id');
        var konum = "popup_kampanya";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});
/* Aktif Pasif Kampanya */
$(document).ready(function () {
    $(document).on('click', '.aktifPasifKampanya', function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "kampanyalar";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function (gel) {
                alert('Hata');
            }
        });
    });
});
$(document).ready(function () {
    $(document).on('click', '.aktifPasifBanka', function (event) {
        var id = $(this).attr("id");
        var konum = "banka_taksit_eslesme";
        var durum = ($(this).is(':checked')) ? '1' : '0';

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',
            data: {id: id, durum: durum, konum: konum},
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function (gel) {
                alert('Hata');
            }
        });
    });
});


/* Aktif Pasif Banner */
$(document).ready(function () {
    $('.aktifPasifBanner').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_banner";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/* Aktif Pasif Banner */
$(document).ready(function () {
    $('.aktifPasifAdres').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_iletisim";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/* Aktif Pasif Uye */
$(document).ready(function () {
    $('.aktifPasifUye').click(function (event) {
        var id = $(this).attr("id");
        var konum = "uyeler";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum},
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

/* Aktif Pasif Katalog */
$(document).ready(function () {
    $('.aktifPasifKatalog').click(function (event) {
        var id = $(this).attr("id");  //id değerini alıyoruz
        var konum = "nokta_kataloglar";
        var durum = ($(this).is(':checked')) ? '1' : '0';
        //checkbox a göre aktif mi pasif mi bilgisini alıyoruz.

        $.ajax({
            type: 'POST',
            url: 'https://www.noktaelektronik.com.tr/php/aktifPasif.php',  //işlem yaptığımız sayfayı belirtiyoruz
            data: {id: id, durum: durum, konum: konum}, //datamızı yolluyoruz
            success: function (result) {
                Swal.fire({
                    title: "Aktif/Pasif işlemi yapıldı!",
                    toast: true,
                    position: 'top-end',
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function () {
                alert('Hata');
            }
        });
    });
});

//Sweet Alertler //

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

   
    