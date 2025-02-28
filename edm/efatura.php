<?
    include_once("edm/autoload.php");

    $client =   new \EFatura\Client("https://test.edmbilisim.com.tr/EFaturaEDM21ea/EFaturaEDM.svc?singleWsdl");
    $login  =   $client->login("Datanet","1234567");

    switch ($_GET["islem"]){
        case "login":
            if($login){
                echo "<h2>Giriş Başarılı - SESSION ID : ".$client->getSessionId()."</h2><br>";
            }else{
                echo "Girişinde Hata Var<br>";
            }
        break;
        case "logout":
            if($client->logout()){
                echo "ÇIKIŞ BAŞARILI";
            }else{
                echo "ÇIKIŞTA HATA VAR";
            }
        break;
        case "kullanici_kontrol":
            $check_user = $client->checkUser("6160044079");
            ?><table width="100%" border="1">
            <thead>
            <tr><th colspan="3"><h3>6160044079 VKN Lİ KULLANICI KONTROLÜ - CheckUser</h3></th></tr>
            <tr>
                <th>ID</th>
                <th>Firma</th>
                <th>Alias</th>
            </tr>
            </thead>
            <tbody><?
            foreach ($check_user as $user){
                ?><tr>
                <td><?=$user->IDENTIFIER?></td>
                <td><?=$user->TITLE?></td>
                <td><?=$user->ALIAS?></td>
                </tr><?
            }
            ?></tbody>
            </table><?
        break;
        case "kullanici_liste":
            $userList   =   $client->getUserList('2017-04-15T00:00:00',"CSV");
            ?><table width="100%" border="1">
            <thead>
            <tr><th colspan="3"><h3>KULLANICI LİSTESİ OKUMA - GetUserList</h3></th></tr>
            <tr>
                <th>ID</th>
                <th>Firma</th>
                <th>Alias</th>
            </tr>
            </thead>
            <tbody><?
            foreach ($userList as $user){
                ?><tr>
                <td><?=$user->IDENTIFIER?></td>
                <td><?=$user->TITLE?></td>
                <td><?=$user->ALIAS?></td>
                </tr><?
            }
            ?></tbody>
            </table><?
        break;
        case "efatura_gonder":
            //header("Content-type: text/xml; charset=utf-8");
            //Fatura Oluşturuluyor
            $fatura = new \EFatura\Fatura();
            $fatura->setProfileId("TICARIFATURA");
            $fatura->setId("TRK2019112717542");
            $fatura->setUuid(\EFatura\Util::GUID());
            $fatura->setIssueDate(\EFatura\Util::issueDate());
            $fatura->setIssueTime(\EFatura\Util::issueTime());
            $fatura->setInvoiceTypeCode("SATIS");
            $fatura->setNote("Yazı İle : #Bin Yüz Seksen Türk Lirası#");
            $fatura->setDocumentCurrencyCode("TRY");
            $fatura->setLineCountNumeric("2");

            //EFatura Gönderici Bilgileri Set Edildi.
            $duzenleyen = new \EFatura\Cari();
            $duzenleyen->setUnvan("DATANET BİLİŞİM");
            $duzenleyen->setTip("TUZELKISI"); // TUZELKISI - GERCEKKISI
            $duzenleyen->setAdres("Karakaş Mah. Yeni Gürpınar İş Merkezi Kat:2 No:35");
            $duzenleyen->setIl("KIRKLARELİ");
            $duzenleyen->setIlce("MERKEZ");
            $duzenleyen->setUlkeKod("TR");
            $duzenleyen->setUlkeAd("TÜRKİYE");
            $duzenleyen->setVergiDaire("KIRKLARELİ");
            $duzenleyen->setVkn("3230512384");
            $duzenleyen->setMersisno("MERSISNO");
            $duzenleyen->setHizmetno("HIZMETNO");
            $duzenleyen->setTicaretSicilNo("TICSICNO");
            $duzenleyen->setTelefon("0288 214 04 30");
            $duzenleyen->setEposta("malicetin@datanetbilisim.com");
            $duzenleyen->setWebsite("www.datanetbilisim.com");
            $duzenleyen->setGibUrn("urn:mail:defaultgb@edmbilisim.com.tr");
            $fatura->setDuzenleyen($duzenleyen);

            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $alici  =  new \EFatura\Cari();
            $alici->setUnvan("ALICI FİRMA ÜNVAN");
            $alici->setAdres("ALICI FİRMA ADRESİ");
            $alici->setIl("İSTANBUL");
            $alici->setIlce("İLCE");
            $alici->setUlkeKod("TR");
            $alici->setUlkeAd("TÜRKİYE");
            $alici->setVergiDaire("VERGİDAİRESİ");
            $alici->setVkn("3230512384");
            $alici->setMersisno("ALICIMERSISNO");
            $alici->setHizmetno("ALICIHIZMETNO");
            $alici->setTicaretSicilNo("ALICITICSICNO");
            $alici->setTelefon("ALICITELEFON");
            $alici->setEposta("malicetin@datanetbilisim.com");
            $alici->setWebsite("ALICIWEBSITE");
            $alici->setGibUrn("urn:mail:defaultpk@edmbilisim.com.tr");
            $fatura->setAlici($alici);

/*-------------Eski fatura altı vergi ekleme, artık kullanılmıyor. Vergiler satirlardan ekleniyor.-------------*/
//            $fatura_dip_vergi = new \EFatura\Vergi();
//			$fatura_dip_vergi->setSiraNo(1);
//            $fatura_dip_vergi->setVergiHaricTutar(1000.00);
//            $fatura_dip_vergi->setVergiTutar(180.00);
//            $fatura_dip_vergi->setParaBirimKod("TRY");
//            $fatura_dip_vergi->setVergiOran(18);
//            $fatura_dip_vergi->setVergiKod("0015");
//            $fatura_dip_vergi->setVergiAd("KDV GERCEK");
//            $fatura->setVergi($fatura_dip_vergi);
/*-------------Eski vergi ekleme, artık kullanılmıyor. Vergiler satirlardan ekleniyor.-------------*/

            //Faturaya Dip Toplamlar Ekleniyor
            $fatura->setSatirToplam(1000.00);
            $fatura->setVergiDahilToplam(1180.00);
            $fatura->setToplamIskonto(0);
            $fatura->setYuvarlamaTutar(0);
            $fatura->setOdenecekTutar(1180.00);

            //Fatura Satırları Oluşturuluyor
            /*1.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(1);
            $satir->setBirim("NIU");
            $satir->setMiktar(5.00);
            $satir->setBirimFiyat(100.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 001");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*2.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(2);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(40.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(8);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*3.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(3);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(510.00);
            $satir_vergi = new \EFatura\Vergi();
            $satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(40.80);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(8);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*3.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(4);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(520.00);
            $satir_vergi = new \EFatura\Vergi();
            $satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(93.60);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);


            $sonuc = $client->sendInvoice($fatura);
            break;
            ?><table width="100%" border="1">
                <tr>
                    <td colspan="3"><strong>REQUEST RETURN</strong></td>
                </tr>
                <tr>
                    <td>INTL_TXN_ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->INTL_TXN_ID?></td>
                </tr>
                <tr>
                    <td>CLIENT_TXN_ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->CLIENT_TXN_ID?></td>
                </tr>
                <tr>
                    <td>RETURN_CODE</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->RETURN_CODE?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>INVOICE</strong></td>
                </tr>
                <tr>
                    <td>TRXID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->TRXID?></td>
                </tr>
                <tr>
                    <td>UUID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->UUID?></td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->ID?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>INVOICE -> HEADER</strong></td>
                </tr>
                <tr>
                    <td>STATUS</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->STATUS?></td>
                </tr>
                <tr>
                    <td>STATUS_DESCRIPTION</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->STATUS_DESCRIPTION?></td>
                </tr>
                <tr>
                    <td>INTERNETSALES</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->INTERNETSALES?></td>
                </tr>
                <tr>
                    <td>EARCHIVE</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->EARCHIVE?></td>
                </tr>
            </table><?
        break;
        case "earsiv_gonder_senaryo_1":
            //Fatura Oluşturuluyor
            $fatura = new \EFatura\Fatura();
            $fatura->setProfileId("EARSIVFATURA");
            $fatura->setId("EKM2018000000920");
            $fatura->setUuid(\EFatura\Util::GUID());
            $fatura->setIssueDate(\EFatura\Util::issueDate());
            $fatura->setIssueTime(\EFatura\Util::issueTime());
            $fatura->setInvoiceTypeCode("SATIS"); //SATIS - IADE
            $fatura->setNote("Yazı İle : #Bin Yüz Seksen Türk Lirası#");
            $fatura->setDocumentCurrencyCode("TRY");
            $fatura->setLineCountNumeric("2");

            //EFatura Gönderici Bilgileri Set Edildi.
            $duzenleyen = new \EFatura\Cari();
            $duzenleyen->setUnvan("DATANET BİLİŞİM");
            $duzenleyen->setAdres("Karakaş Mah. Yeni Gürpınar İş Merkezi Kat:2 No:35");
            $duzenleyen->setIl("KIRKLARELİ");
            $duzenleyen->setIlce("MERKEZ");
            $duzenleyen->setUlkeKod("TR");
            $duzenleyen->setUlkeAd("TÜRKİYE");
            $duzenleyen->setVergiDaire("KIRKLARELİ");
            $duzenleyen->setVkn("3230512384");
            $duzenleyen->setMersisno("MERSISNO");
            $duzenleyen->setHizmetno("HIZMETNO");
            $duzenleyen->setTicaretSicilNo("TICSICNO");
            $duzenleyen->setTelefon("0288 214 04 30");
            $duzenleyen->setEposta("malicetin@datanetbilisim.com");
            $duzenleyen->setWebsite("www.datanetbilisim.com");
            $duzenleyen->setGibUrn("urn:mail:defaultgb@edmbilisim.com.tr");
            $fatura->setDuzenleyen($duzenleyen);

            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $alici  =  new \EFatura\Cari();
            $alici->setUnvan("ALICI FİRMA ÜNVAN");
            $alici->setAdres("ALICI FİRMA ADRESİ");
            $duzenleyen->setTip("TUZELKISI"); // TUZELKISI - GERCEKKISI
            $alici->setIl("İSTANBUL");
            $alici->setIlce("İLCE");
            $alici->setUlkeKod("TR");
            $alici->setUlkeAd("TÜRKİYE");
            $alici->setVergiDaire("VERGİDAİRESİ");
            $alici->setVkn("1111111111");
            $alici->setMersisno("ALICIMERSISNO");
            $alici->setHizmetno("ALICIHIZMETNO");
            $alici->setTicaretSicilNo("ALICITICSICNO");
            $alici->setTelefon("ALICITELEFON");
            $alici->setEposta("malicetin@datanetbilisim.com");
            $alici->setWebsite("ALICIWEBSITE");
            $alici->setGibUrn("urn:mail:defaultpk@edmbilisim.com.tr");
            $fatura->setAlici($alici);

            //Fatura Altı KDV Eklendi
            $fatura_dip_vergi = new \EFatura\Vergi();
			$fatura_dip_vergi->setSiraNo(1);
            $fatura_dip_vergi->setVergiHaricTutar(1000.00);
            $fatura_dip_vergi->setVergiTutar(180.00);
            $fatura_dip_vergi->setParaBirimKod("TRY");
            $fatura_dip_vergi->setVergiOran(18);
            $fatura_dip_vergi->setVergiKod("0015");
            $fatura_dip_vergi->setVergiAd("KDV GERCEK");
            $fatura->setVergi($fatura_dip_vergi);

            //Faturaya Dip Toplamlar Ekleniyor
            $fatura->setSatirToplam(1000.00);
            $fatura->setVergiDahilToplam(1180.00);
            $fatura->setToplamIskonto(0);
            $fatura->setYuvarlamaTutar(0);
            $fatura->setOdenecekTutar(1180.00);

            //Fatura Satırları Oluşturuluyor
            /*1.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(1);
            $satir->setBirim("NIU");
            $satir->setMiktar(5.00);
            $satir->setBirimFiyat(100.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 001");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*2.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(2);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);
            $sonuc = $client->sendInvoice($fatura);
            ?><table width="100%" border="1">
            <tr>
                <td colspan="3"><strong>REQUEST RETURN</strong></td>
            </tr>
            <tr>
                <td>INTL_TXN_ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->INTL_TXN_ID?></td>
            </tr>
            <tr>
                <td>CLIENT_TXN_ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->CLIENT_TXN_ID?></td>
            </tr>
            <tr>
                <td>RETURN_CODE</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->RETURN_CODE?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>INVOICE</strong></td>
            </tr>
            <tr>
                <td>TRXID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->TRXID?></td>
            </tr>
            <tr>
                <td>UUID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->UUID?></td>
            </tr>
            <tr>
                <td>ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->ID?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>INVOICE -> HEADER</strong></td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->STATUS?></td>
            </tr>
            <tr>
                <td>STATUS_DESCRIPTION</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->STATUS_DESCRIPTION?></td>
            </tr>
            <tr>
                <td>INTERNETSALES</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->INTERNETSALES?></td>
            </tr>
            <tr>
                <td>EARCHIVE</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->EARCHIVE?></td>
            </tr>
            </table><?
        break;
        case "earsiv_gonder_senaryo_2":
            //Fatura Oluşturuluyor
            $fatura = new \EFatura\Fatura();
            $fatura->setProfileId("EARSIVFATURA");
            $fatura->setId("EKM2018000000921");
            $fatura->setUuid(\EFatura\Util::GUID());
            $fatura->setIssueDate(\EFatura\Util::issueDate());
            $fatura->setIssueTime(\EFatura\Util::issueTime());
            $fatura->setInvoiceTypeCode("SATIS"); //SATIS - IADE
            $fatura->setNote("Yazı İle : #Bin Yüz Seksen Türk Lirası#");
            $fatura->setDocumentCurrencyCode("TRY");
            $fatura->setLineCountNumeric("2");

            //EFatura Gönderici Bilgileri Set Edildi.
            $duzenleyen = new \EFatura\Cari();
            $duzenleyen->setUnvan("DATANET BİLİŞİM");
            $duzenleyen->setAdres("Karakaş Mah. Yeni Gürpınar İş Merkezi Kat:2 No:35");
            $duzenleyen->setIl("KIRKLARELİ");
            $duzenleyen->setIlce("MERKEZ");
            $duzenleyen->setUlkeKod("TR");
            $duzenleyen->setUlkeAd("TÜRKİYE");
            $duzenleyen->setVergiDaire("KIRKLARELİ");
            $duzenleyen->setVkn("3230512384");
            $duzenleyen->setMersisno("MERSISNO");
            $duzenleyen->setHizmetno("HIZMETNO");
            $duzenleyen->setTicaretSicilNo("TICSICNO");
            $duzenleyen->setTelefon("0288 214 04 30");
            $duzenleyen->setEposta("malicetin@datanetbilisim.com");
            $duzenleyen->setWebsite("www.datanetbilisim.com");
            $duzenleyen->setGibUrn("urn:mail:defaultgb@edmbilisim.com.tr");
            $fatura->setDuzenleyen($duzenleyen);

            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $alici  =  new \EFatura\Cari();
            $alici->setUnvan("ALICI FİRMA ÜNVAN");
            $alici->setAdres("ALICI FİRMA ADRESİ");
            $duzenleyen->setTip("TUZELKISI"); // TUZELKISI - GERCEKKISI
            $alici->setIl("İSTANBUL");
            $alici->setIlce("İLCE");
            $alici->setUlkeKod("TR");
            $alici->setUlkeAd("TÜRKİYE");
            $alici->setVergiDaire("VERGİDAİRESİ");
            $alici->setVkn("1111111111"); //Tüzel Kişi 10 Haneli VKN Gerçek Kişide TCKimlik No
            $alici->setMersisno("ALICIMERSISNO");
            $alici->setHizmetno("ALICIHIZMETNO");
            $alici->setTicaretSicilNo("ALICITICSICNO");
            $alici->setTelefon("ALICITELEFON");
            $alici->setEposta("malicetin@datanetbilisim.com");
            $alici->setWebsite("ALICIWEBSITE");
            $alici->setGibUrn("urn:mail:defaultpk@edmbilisim.com.tr");
            $fatura->setAlici($alici);

            //Fatura Altı KDV Eklendi
            $fatura_dip_vergi = new \EFatura\Vergi();
			$fatura_dip_vergi->setSiraNo(1);
            $fatura_dip_vergi->setVergiHaricTutar(1000.00);
            $fatura_dip_vergi->setVergiTutar(180.00);
            $fatura_dip_vergi->setParaBirimKod("TRY");
            $fatura_dip_vergi->setVergiOran(18);
            $fatura_dip_vergi->setVergiKod("0015");
            $fatura_dip_vergi->setVergiAd("KDV GERCEK");
            $fatura->setVergi($fatura_dip_vergi);

            //Faturaya Dip Toplamlar Ekleniyor
            $fatura->setSatirToplam(1000.00);
            $fatura->setVergiDahilToplam(1180.00);
            $fatura->setToplamIskonto(0);
            $fatura->setYuvarlamaTutar(0);
            $fatura->setOdenecekTutar(1180.00);

            //Fatura Satırları Oluşturuluyor
            /*1.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(1);
            $satir->setBirim("NIU");
            $satir->setMiktar(5.00);
            $satir->setBirimFiyat(100.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 001");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*2.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(2);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            //Irsaliye Ekleniyor
            $irs = new \EFatura\Irsaliye();
            $irs->setEvrakno('IRS-001');
            $irs->setEvrakTarih('2017-04-26');
            $fatura->addIrsaliye($irs);

            $sonuc = $client->sendInvoice($fatura);
            ?><table width="100%" border="1">
            <tr>
                <td colspan="3"><strong>REQUEST RETURN</strong></td>
            </tr>
            <tr>
                <td>INTL_TXN_ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->INTL_TXN_ID?></td>
            </tr>
            <tr>
                <td>CLIENT_TXN_ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->CLIENT_TXN_ID?></td>
            </tr>
            <tr>
                <td>RETURN_CODE</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->REQUEST_RETURN->RETURN_CODE?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>INVOICE</strong></td>
            </tr>
            <tr>
                <td>TRXID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->TRXID?></td>
            </tr>
            <tr>
                <td>UUID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->UUID?></td>
            </tr>
            <tr>
                <td>ID</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->ID?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>INVOICE -> HEADER</strong></td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->STATUS?></td>
            </tr>
            <tr>
                <td>STATUS_DESCRIPTION</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->STATUS_DESCRIPTION?></td>
            </tr>
            <tr>
                <td>INTERNETSALES</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->INTERNETSALES?></td>
            </tr>
            <tr>
                <td>EARCHIVE</td>
                <td>&nbsp;:&nbsp;</td>
                <td><?=$sonuc->INVOICE->HEADER->EARCHIVE?></td>
            </tr>
            </table><?
        break;
        case "earsiv_gonder_senaryo_3":
            //Fatura Oluşturuluyor
            $fatura = new \EFatura\Fatura();
            $fatura->setProfileId("EARSIVFATURA");
            $fatura->setId("EKM2018000000922");
            $fatura->setUuid(\EFatura\Util::GUID());
            $fatura->setIssueDate(\EFatura\Util::issueDate());
            $fatura->setIssueTime(\EFatura\Util::issueTime());
            $fatura->setInvoiceTypeCode("SATIS"); //SATIS - IADE
            $fatura->setNote("Yazı İle : #Bin Yüz Seksen Türk Lirası#");
            $fatura->setDocumentCurrencyCode("TRY");
            $fatura->setLineCountNumeric("2");

            //EFatura Gönderici Bilgileri Set Edildi.
            $duzenleyen = new \EFatura\Cari();
            $duzenleyen->setUnvan("DATANET BİLİŞİM");
            $duzenleyen->setAdres("Karakaş Mah. Yeni Gürpınar İş Merkezi Kat:2 No:35");
            $duzenleyen->setIl("KIRKLARELİ");
            $duzenleyen->setIlce("MERKEZ");
            $duzenleyen->setUlkeKod("TR");
            $duzenleyen->setUlkeAd("TÜRKİYE");
            $duzenleyen->setVergiDaire("KIRKLARELİ");
            $duzenleyen->setVkn("3230512384");
            $duzenleyen->setMersisno("MERSISNO");
            $duzenleyen->setHizmetno("HIZMETNO");
            $duzenleyen->setTicaretSicilNo("TICSICNO");
            $duzenleyen->setTelefon("0288 214 04 30");
            $duzenleyen->setEposta("malicetin@datanetbilisim.com");
            $duzenleyen->setWebsite("www.datanetbilisim.com");
            $duzenleyen->setGibUrn("urn:mail:defaultgb@edmbilisim.com.tr");
            $fatura->setDuzenleyen($duzenleyen);

            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $alici  =  new \EFatura\Cari();
            $alici->setUnvan("ALICI FİRMA ÜNVAN");
            $alici->setAdres("ALICI FİRMA ADRESİ");
            $duzenleyen->setTip("TUZELKISI"); // TUZELKISI - GERCEKKISI
            $alici->setIl("İSTANBUL");
            $alici->setIlce("İLCE");
            $alici->setUlkeKod("TR");
            $alici->setUlkeAd("TÜRKİYE");
            $alici->setVergiDaire("VERGİDAİRESİ");
            $alici->setVkn("1111111111"); //Tüzel Kişi 10 Haneli VKN Gerçek Kişide TCKimlik No
            $alici->setMersisno("ALICIMERSISNO");
            $alici->setHizmetno("ALICIHIZMETNO");
            $alici->setTicaretSicilNo("ALICITICSICNO");
            $alici->setTelefon("ALICITELEFON");
            $alici->setEposta("malicetin@datanetbilisim.com");
            $alici->setWebsite("ALICIWEBSITE");
            $alici->setGibUrn("urn:mail:defaultpk@edmbilisim.com.tr");
            $fatura->setAlici($alici);

            //Fatura Altı KDV Eklendi
            $fatura_dip_vergi = new \EFatura\Vergi();
			$fatura_dip_vergi->setSiraNo(1);
            $fatura_dip_vergi->setVergiHaricTutar(1000.00);
            $fatura_dip_vergi->setVergiTutar(180.00);
            $fatura_dip_vergi->setParaBirimKod("TRY");
            $fatura_dip_vergi->setVergiOran(18);
            $fatura_dip_vergi->setVergiKod("0015");
            $fatura_dip_vergi->setVergiAd("KDV GERCEK");
            $fatura->setVergi($fatura_dip_vergi);

            //Faturaya Dip Toplamlar Ekleniyor
            $fatura->setSatirToplam(1000.00);
            $fatura->setVergiDahilToplam(1180.00);
            $fatura->setToplamIskonto(0);
            $fatura->setYuvarlamaTutar(0);
            $fatura->setOdenecekTutar(1180.00);

            //Fatura Satırları Oluşturuluyor
            /*1.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(1);
            $satir->setBirim("NIU");
            $satir->setMiktar(5.00);
            $satir->setBirimFiyat(100.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 001");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            /*2.SATIR*/
            $satir = new \EFatura\Satir();
            $satir->setSiraNo(2);
            $satir->setBirim("NIU");
            $satir->setMiktar(10.00);
            $satir->setBirimFiyat(50.00);
            $satir->setSatirToplam(500.00);
            $satir_vergi = new \EFatura\Vergi();
			$satir_vergi->setSiraNo(1);
            $satir_vergi->setVergiHaricTutar(500.00);
            $satir_vergi->setVergiTutar(90.00);
            $satir_vergi->setParaBirimKod("TRY");
            $satir_vergi->setVergiOran(18);
            $satir_vergi->setVergiKod("0015");
            $satir_vergi->setVergiAd("KDV GERCEK");
            $satir->setVergi($satir_vergi);
            $mal_hizmet = new \EFatura\Urun();
            $mal_hizmet->setSerbestAciklama("SERBEST ACIKLAMA ALANI-2");
            $mal_hizmet->setAd("ÖRNEK ÜRÜN 002");
            $satir->setUrun($mal_hizmet);
            $fatura->addSatir($satir);

            //Irsaliye Ekleniyor
            $irs = new \EFatura\Irsaliye();
            $irs->setEvrakno('IRS-001');
            $irs->setEvrakTarih('2017-04-26');
            $fatura->addIrsaliye($irs);

            //Teslimat Bilgisi Ekleniyor
            $teslim = new \EFatura\Teslimat();
            $teslim->setKargoAd("DENEME KARGO");
            $teslim->setGonderimTarih('2017-04-25');
            $fatura->setTeslimat($teslim);

            //Ödeme Tipi Bilgileri
            $odemeTip = new \EFatura\OdemeSekli();
            $odemeTip->setKod("1");
            $odemeTip->setTarih('2017-04-25');
            $odemeTip->setHesapKod("0000-0000-0000-0000");
            $odemeTip->setParaBirimKod("TRY");
            $odemeTip->setOdemeNot("KREDIKARTI");
            $fatura->setOdemeTip($odemeTip);

            $sonuc = $client->sendInvoice($fatura);
            ?><table width="100%" border="1">
                <tr>
                    <td colspan="3"><strong>REQUEST RETURN</strong></td>
                </tr>
                <tr>
                    <td>INTL_TXN_ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->INTL_TXN_ID?></td>
                </tr>
                <tr>
                    <td>CLIENT_TXN_ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->CLIENT_TXN_ID?></td>
                </tr>
                <tr>
                    <td>RETURN_CODE</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->REQUEST_RETURN->RETURN_CODE?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>INVOICE</strong></td>
                </tr>
                <tr>
                    <td>TRXID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->TRXID?></td>
                </tr>
                <tr>
                    <td>UUID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->UUID?></td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->ID?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>INVOICE -> HEADER</strong></td>
                </tr>
                <tr>
                    <td>STATUS</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->STATUS?></td>
                </tr>
                <tr>
                    <td>STATUS_DESCRIPTION</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->STATUS_DESCRIPTION?></td>
                </tr>
                <tr>
                    <td>INTERNETSALES</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->INTERNETSALES?></td>
                </tr>
                <tr>
                    <td>EARCHIVE</td>
                    <td>&nbsp;:&nbsp;</td>
                    <td><?=$sonuc->INVOICE->HEADER->EARCHIVE?></td>
                </tr>
            </table><?
        break;
        case "fatura_durum_sorgulama":
            $fatura_durum = $client->getInvoiceStatus("FRT2018000000001");
            ?><table width="100%" border="1">
                <tr>
                    <td>TRXID</td>
                    <td><?=$fatura_durum->TRXID?></td>
                </tr>
                <tr>
                    <td>UUID</td>
                    <td><?=$fatura_durum->UUID?></td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?=$fatura_durum->ID?></td>
                </tr>
                <tr>
                    <td>STATUS</td>
                    <td><?=$fatura_durum->STATUS?></td>
                </tr>
                <tr>
                    <td>AÇIKLAMA</td>
                    <td><?
                        print_r($fatura_durum->ACIKLAMA);
                    ?></td>
                </tr>
                <tr>
                    <td>STATUS_DESCRIPTION</td>
                    <td><?=$fatura_durum->STATUS_DESCRIPTION?></td>
                </tr>
                <tr>
                    <td>RESPONSE_CODE</td>
                    <td><?=$fatura_durum->RESPONSE_CODE?></td>
                </tr>
                <tr>
                    <td>RESPONSE_DESCRIPTION</td>
                    <td><?=$fatura_durum->RESPONSE_DESCRIPTION?></td>
                </tr>
                <tr>
                    <td>CDATE</td>
                    <td><?=$fatura_durum->CDATE?></td>
                </tr>
            </table><?
        break;
        case "tek_fatura_oku":
            //header("Content-type: text/xml; charset=utf-8");
            $oku    =   $client->getSingleInvoice("FRT2018000000001");
            print_r($oku);
        break;
        case "gelen_kutusu":
            $sonuc = $client->getIncomingInvoice(            
				10,
				null,
				null,
				null,
				null,
				date("Y-m-d H:i",strtotime("2019-11-27 00:00")),
				null
			);
            print_r($sonuc);
        break;
        case "okundu_isaretle":
            $sonuc = $client->markInvoice("MEL2017000001716");
            print_r($sonuc);
        break;
        case "kabul_ver":
			//$client->gelenFaturaKabul("FATURANO");
        break;
        case "red_ver":
			//$client->gelenFaturaKabul("FATURANO");
        break;		
    }
?>

