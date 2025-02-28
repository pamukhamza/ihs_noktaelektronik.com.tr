<?php
/**
 * Created by PhpStorm.
 * User: malic
 * Date: 10.07.2018
 * Time: 17:49
 */

namespace EFatura;


class Client
{
    private $session_id = null;
    private $hata;

    /**
     * Client constructor.
     * @param string $service_url
     */
    public function __construct($service_url)
    {
        Util::$service_url = $service_url;
    }

    /**
     * @return mixed
     */
    public function getHata()
    {
        return $this->hata;
    }

    /**
     * @param mixed $hata
     */
    public function setHata($hataKod, $hataMesaj)
    {
        $this->hata = array(
            "KOD" => $hataKod,
            "MESAJ" => $hataMesaj
        );
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return (is_null($this->session_id) ? $_SESSION["EFATURA_SESSION"] : $this->session_id);
    }

    /**
     * @param mixed $session_id
     */
    public function setSessionId($session_id)
    {
        $_SESSION["EFATURA_SESSION"] = $session_id;
        $this->session_id = $session_id;
    }

    /**
     * @param $username
     * @param $password
     * @return boolean
     */
    public function login($username, $password)
    {
        $header = new RequestHeader();
        $header->session_id = "-1";
        $param = $header->getArray();
        $param["USER_NAME"] = $username;
        $param["PASSWORD"] = $password;
        $request = new Request();
        $session = $request->send("Login", $param);
        if ($session->SESSION_ID != "") {
            $this->setSessionId($session->SESSION_ID);
            return true;
        } else {
            $this->setHata($request->hataKod, $request->hataMesaj);
            return false;
        }
    }

    public function logout()
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $param = $req_header->getArray();
        $request = new Request();
        $sonuc = $request->send("Logout", $param);
        if ($sonuc->REQUEST_RETURN->RETURN_CODE == "0") {
            return true;
        } else {
            $this->setHata($request->hataKod, $request->hataMesaj);
            return false;
        }
    }

    public function getUserList($okuma_zaman = "", $format = "XML")
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        if ($okuma_zaman != "") {
            $send_data["REGISTER_TIME_START"] = $okuma_zaman;
        }
        $send_data["FORMAT"] = $format;
        $req = new Request();
        $sonuc = $req->send("GetUserList", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->Items->Items;
    }

    public function checkUser($vkn = "", $alias = null, $unvan = null, $tip = null, $kayit_zaman = null)
    {
        if ($vkn == "") {
            return false;
        } else {
            $req_header = new RequestHeader();
            $req_header->session_id = $this->getSessionId();
            $send_data = $req_header->getArray();
            $send_data["USER"]["IDENTIFIER"] = $vkn;
            if (!is_null($alias)) {
                $send_data["USER"]["ALIAS"] = $alias;
            }
            if (!is_null($unvan)) {
                $send_data["USER"]["TITLE"] = $unvan;
            }
            if (!is_null($tip)) {
                $send_data["USER"]["TYPE"] = $tip;
            }
            if (!is_null($kayit_zaman)) {
                $send_data["USER"]["REGISTER_TIME"] = $kayit_zaman;
            }
            $req = new Request();
            $sonuc = $req->send("CheckUser", $send_data);
            $this->setHata($req->hataKod, $req->hataMesaj);
            return $sonuc->USER;
        }
    }

    public function sendInvoice(Fatura $fatura)
    {

        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $readFatura = $fatura->readXML();
        echo $readFatura;
        $send_data["SENDER"] = array("_" => "", "alias" => $fatura->getDuzenleyen()->getGibUrn(), "vkn" => $fatura->getDuzenleyen()->getVkn());
        $send_data["RECEIVER"] = array("_" => "", "alias" => $fatura->getAlici()->getGibUrn(), "vkn" => $fatura->getAlici()->getVkn());
        $send_data["INVOICE"]["CONTENT"] = $readFatura;
        $req = new Request();
        $sonuc = $req->send("SendInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc;
    }

    private function getInvoice($limit = 1, $alanURN = null, $gonderenURN = null, $faturaNo = null, $faturaUUID = null, $baslangicTarih = null, $bitisTarih = null, $gelenFaturalar = false, $contentType = "XML")
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();
        $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = $limit;
        $send_data["INVOICE_CONTENT_TYPE"] = $contentType;
        if (!is_null($gonderenURN)) {
            $send_data["INVOICE_SEARCH_KEY"]["FROM"] = $gonderenURN;
        }
        if (!is_null($alanURN)) {
            $send_data["INVOICE_SEARCH_KEY"]["TO"] = $alanURN;
        }
        if (!is_null($faturaNo)) {
            $send_data["INVOICE_SEARCH_KEY"]["ID"] = $faturaNo;
        }
        if (!is_null($faturaUUID)) {
            $send_data["INVOICE_SEARCH_KEY"]["UUID"] = $faturaUUID;
        }
        if (!is_null($baslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["START_DATESpecified"] = true;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATESpecified"] = true;
        }
        if (!is_null($bitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["END_DATESpecified"] = true;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATESpecified"] = true;
        }
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDE"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDEDSpecified"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["ISARCHIVED"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["ISARCHIVEDSpecified"] = true;

        if ($gelenFaturalar) {
            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "IN";
        } else {
            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "OUT";
        }
        $req = new Request();
        $sonuc = $req->send("GetInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc;
    }

    public function UBLtoFatura($xmlStr = null)
    {
        if (is_null($xmlStr)) {
            return false;
        } else {
            $xmlStr = Util::UBLClear($xmlStr);
            //echo $xmlStr;
            $obj = simplexml_load_string($xmlStr);
            //Fatura Oluşturuluyor
            $fatura = new \Netesnaf\Edm\EFatura\Fatura();
            $fatura->setProfileId($obj->ProfileID);
            $fatura->setId($obj->ID);
            $fatura->setUuid($obj->UUID);
            $fatura->setIssueDate($obj->IssueDate);
            $fatura->setIssueTime($obj->IssueTime);
            $fatura->setInvoiceTypeCode($obj->InvoiceTypeCode);
            $fatura->setNote($obj->Note);
            $fatura->setDocumentCurrencyCode($obj->DocumentCurrencyCode);
            $fatura->setLineCountNumeric($obj->LineCountNumeric);
            if ($obj->AdditionalDocumentReference) {
                $fatura->setAdditionalDocumentReference(array(
                    "ID" => $obj->AdditionalDocumentReference->ID,
                    "IssueDate" => $obj->AdditionalDocumentReference->IssueDate,
                    "DocumentType" => $obj->AdditionalDocumentReference->DocumentType,
                    "Attachment" => array(
                        "EmbeddedDocumentBinaryObject" => $obj->AdditionalDocumentReference->Attachment->EmbeddedDocumentBinaryObject
                    )
                ));
            }

            //EFatura Gönderici Bilgileri Set Edildi.
            $objG = $obj->AccountingSupplierParty->Party;
            $duzenleyen = new \Netesnaf\Edm\EFatura\Cari();
            $duzenleyen->setUnvan($objG->PartyName->Name);
            $duzenleyen->setAdres($objG->PostalAddress->BuildingName);
            $duzenleyen->setIl($objG->PostalAddress->CityName);
            $duzenleyen->setIlce($objG->PostalAddress->CitySubdivisionName);
            $duzenleyen->setUlkeKod($objG->PostalAddress->Country->IdentificationCode);
            $duzenleyen->setUlkeAd($objG->PostalAddress->Country->Name);
            $duzenleyen->setVergiDaire($objG->PartyTaxScheme->TaxScheme->Name);
            $duzenleyen->setVkn($objG->PartyIdentification[0]["ID"]);
            $duzenleyen->setMersisno($objG->PartyIdentification[1]["ID"]);
            $duzenleyen->setHizmetno($objG->PartyIdentification[2]["ID"]);
            $duzenleyen->setTicaretSicilNo($objG->PartyIdentification[3]["ID"]);
            $duzenleyen->setTelefon($objG->Contact->Telephone);
            $duzenleyen->setEposta($objG->Contact->Telephone);
            $duzenleyen->setWebsite($objG->WebsiteURI);
            //$duzenleyen->setGibUrn($resp->INVOICE->HEADER->FROM);
            $fatura->setDuzenleyen($duzenleyen);
            //EFatura Alıcı Carisi Oluşturulup Faturaya Eklendi
            $objA = $obj->AccountingCustomerParty->Party;
            $alici = new \Netesnaf\Edm\EFatura\Cari();
            $alici->setUnvan($objG->PartyName->Name);
            $alici->setAdres($objG->PostalAddress->BuildingName);
            $alici->setIl($objG->PostalAddress->CityName);
            $alici->setIlce($objG->PostalAddress->CitySubdivisionName);
            $alici->setUlkeKod($objG->PostalAddress->Country->IdentificationCode);
            $alici->setUlkeAd($objG->PostalAddress->Country->Name);
            $alici->setVergiDaire($objG->PartyTaxScheme->TaxScheme->Name);
            $alici->setVkn($objG->PartyIdentification[0]["ID"]);
            $alici->setMersisno($objG->PartyIdentification[1]["ID"]);
            $alici->setHizmetno($objG->PartyIdentification[2]["ID"]);
            $alici->setTicaretSicilNo($objG->PartyIdentification[3]["ID"]);
            $alici->setTelefon($objG->Contact->Telephone);
            $alici->setEposta($objG->Contact->ElectronicMail);
            $alici->setWebsite($objG->WebsiteURI);
            //$alici->setGibUrn($resp->INVOICE->HEADER->FROM);
            $fatura->setAlici($alici);

            //Fatura Altı KDV Eklendi
            $fatura_dip_vergi = new \Netesnaf\Edm\EFatura\Vergi();
            $fatura_dip_vergi->setSiraNo(intval($obj->TaxTotal->TaxSubtotal->CalculationSequenceNumeric));
            $fatura_dip_vergi->setVergiHaricTutar($obj->TaxTotal->TaxSubtotal->TaxableAmount[0]);
            $fatura_dip_vergi->setVergiTutar($obj->TaxTotal->TaxAmount);
            $fatura_dip_vergi->setParaBirimKod($obj->TaxTotal->TaxAmount["currencyID"]);
            $fatura_dip_vergi->setVergiOran($obj->TaxTotal->TaxSubtotal->Percent);
            $fatura_dip_vergi->setVergiKod($obj->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->TaxTypeCode);
            $fatura_dip_vergi->setVergiAd($obj->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->Name);
            $fatura->setVergi($fatura_dip_vergi);


            //Faturaya Dip Toplamlar Ekleniyor

            $fatura->setSatirToplam($obj->LegalMonetaryTotal->LineExtensionAmount);
            $fatura->setVergiHaricToplam($obj->LegalMonetaryTotal->TaxExclusiveAmount);
            $fatura->setVergiDahilToplam($obj->LegalMonetaryTotal->TaxInclusiveAmount);
            $fatura->setToplamIskonto($obj->LegalMonetaryTotal->AllowanceTotalAmount);
            $fatura->setYuvarlamaTutar($obj->LegalMonetaryTotal->PayableRoundingAmount);
            $fatura->setOdenecekTutar($obj->LegalMonetaryTotal->PayableAmount);

            if (!is_null($obj->AllowanceCharge)) {
                $fIskonto = new \Netesnaf\Edm\EFatura\IskontoArttirim();
                $fIskonto->setTip($obj->AllowanceCharge->ChargeIndicator);
                $fIskonto->setOran($obj->AllowanceCharge->MultiplierFactorNumeric);
                $fIskonto->setTutar($obj->AllowanceCharge->Amount);
                $fIskonto->setParabirimKod($obj->AllowanceCharge->Amount["currencyID"]);
                $fIskonto->setUygTutar($obj->AllowanceCharge->BaseAmount);
                $fatura->setIskontoArttirim($fIskonto);
            }

            foreach ($obj->InvoiceLine as $line) {

                $satir = new \Netesnaf\Edm\EFatura\Satir();
                $satir->setSiraNo($line->ID);
                $satir->setBirim($line->InvoicedQuantity["unitCode"]);
                $satir->setMiktar($line->InvoicedQuantity);
                $satir->setBirimFiyat($line->Price->PriceAmount);
                $satir->setSatirToplam($line->LineExtensionAmount);
                $satir->setParaBirimKod($line->Price->PriceAmount["currencyID"]);

                $taxTotal = $line->TaxTotal;
                $satir_vergi = new \Netesnaf\Edm\EFatura\Vergi();
                if (is_array($taxTotal->TaxSubtotal)) {
                    foreach ($taxTotal->TaxSubtotal as $vergiDetay) {
                        $satir_vergi->setSiraNo(intval($vergiDetay->CalculationSequenceNumeric));
                        $satir_vergi->setVergiHaricTutar($vergiDetay->TaxableAmount);
                        $satir_vergi->setVergiTutar($taxTotal->TaxAmount);
                        $satir_vergi->setParaBirimKod($taxTotal->TaxAmount["currencyID"]);
                        $satir_vergi->setVergiOran($vergiDetay->Percent);
                        $satir_vergi->setVergiKod($vergiDetay->TaxCategory->TaxScheme->TaxTypeCode);
                        $satir_vergi->setVergiAd($vergiDetay->TaxCategory->TaxScheme->Name);
                    }
                } else {
                    $satir_vergi->setSiraNo(intval($taxTotal->TaxSubtotal->CalculationSequenceNumeric));
                    $satir_vergi->setVergiHaricTutar($taxTotal->TaxSubtotal->TaxableAmount);
                    $satir_vergi->setVergiTutar($taxTotal->TaxAmount);
                    $satir_vergi->setParaBirimKod($taxTotal->TaxAmount["currencyID"]);
                    $satir_vergi->setVergiOran($taxTotal->TaxSubtotal->Percent);
                    $satir_vergi->setVergiKod($taxTotal->TaxSubtotal->TaxCategory->TaxScheme->TaxTypeCode);
                    $satir_vergi->setVergiAd($taxTotal->TaxSubtotal->TaxCategory->TaxScheme->Name);
                }
                $satir->setVergi($satir_vergi);

                //İskonto Arttırım İşlemi
                if (!is_null($line->AllowanceCharge)) {
                    $iskontoSatir = new \Netesnaf\Edm\EFatura\IskontoArttirim();
                    $iskontoSatir->setTip($line->AllowanceCharge->ChargeIndicator);
                    $iskontoSatir->setOran($line->AllowanceCharge->MultiplierFactorNumeric);
                    $iskontoSatir->setTutar($line->AllowanceCharge->Amount);
                    $iskontoSatir->setParabirimKod($line->AllowanceCharge->Amount["currencyID"]);
                    $iskontoSatir->setUygTutar($line->AllowanceCharge->BaseAmount);
                    $satir->setIskontoArttirim($iskontoSatir);
                }

                $mal_hizmet = new \Netesnaf\Edm\EFatura\Urun();
                $mal_hizmet->setAd($line->Item->Name);
                $satir->setUrun($mal_hizmet);
                $fatura->addSatir($satir);

            }
            return $fatura;

        }
    }

    public function getSingleInvoice($faturaNo = null, $faturaUUID = null, $gelen = false, $contentType = "XML")
    {
        if (is_null($faturaNo) && is_null($faturaUUID)) {
            return false;
        } else {
            $req_header = new RequestHeader();
            $req_header->session_id = $this->getSessionId();
            $send_data = $req_header->getArray();

            $send_data["INVOICE_CONTENT_TYPE"] = $contentType;
            $send_data["HEADER_ONLY"] = "N";
            $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = 1;
            $send_data["INVOICE_SEARCH_KEY"]["LIMITSpecified"] = true;

            $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = ($gelen ? "IN" : "OUT");
            $send_data["INVOICE_SEARCH_KEY"]["ID"] = $faturaNo;

            $req = new Request();
            $sonuc = $req->send("GetInvoice", $send_data);
            $this->setHata($req->hataKod, $req->hataMesaj);
            //print_r($sonuc);
            return array(
                "CONTENT" => $sonuc->INVOICE->CONTENT->_,
                "SENDER" => $sonuc->INVOICE->HEADER->SENDER,
                "RECEIVER" => $sonuc->INVOICE->HEADER->RECEIVER,
                "SUPPLIER" => $sonuc->INVOICE->HEADER->SUPPLIER,
                "CUSTOMER" => $sonuc->INVOICE->HEADER->CUSTOMER,
                "ISSUE_DATE" => $sonuc->INVOICE->HEADER->ISSUE_DATE,
                "PAYABLE_AMOUNT" => $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->_ . " " . $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->currencyID,
                "PARABIRIMI" => $sonuc->INVOICE->HEADER->PAYABLE_AMOUNT->currencyID,
                "FROM" => $sonuc->INVOICE->HEADER->FROM,
                "TO" => $sonuc->INVOICE->HEADER->TO,
                "PROFILEID" => $sonuc->INVOICE->HEADER->PROFILEID,
                "STATUS" => $sonuc->INVOICE->HEADER->STATUS,
                "STATUS_DESCRIPTION" => $sonuc->INVOICE->HEADER->STATUS_DESCRIPTION,
                "ACIKLAMA" => Util::invoiceStatus($sonuc->INVOICE->HEADER->STATUS),
                "STATUS" => $sonuc->INVOICE->HEADER->STATUS,
                "GIB_STATUS_CODE" => $sonuc->INVOICE->HEADER->GIB_STATUS_CODE,
                "GIB_STATUS_DESCRIPTION" => $sonuc->INVOICE->HEADER->GIB_STATUS_DESCRIPTION,
                "RESPONSE_CODE" => $sonuc->INVOICE->HEADER->RESPONSE_CODE,
                "RESPONSE_DESCRIPTION" => $sonuc->INVOICE->HEADER->RESPONSE_DESCRIPTION,
                "FILENAME" => $sonuc->INVOICE->HEADER->FILENAME,
                "HASH" => $sonuc->INVOICE->HEADER->HASH,
                "CDATE" => $sonuc->INVOICE->HEADER->CDATE,
                "ENVELOPE_IDENTIFIER" => $sonuc->INVOICE->HEADER->ENVELOPE_IDENTIFIER,
                "INTERNETSALES" => $sonuc->INVOICE->HEADER->INTERNETSALES,
                "EARCHIVE" => $sonuc->INVOICE->HEADER->EARCHIVE,
                "TRXID" => $sonuc->INVOICE->TRXID,
                "UUID" => $sonuc->INVOICE->UUID,
                "ID" => $sonuc->INVOICE->ID
            );
        }
    }

    public function getInvoiceStatus($faturaNo = "", $faturaUUID = null)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["INVOICE"] = array("_" => "", "ID" => $faturaNo, "UUID" => $faturaUUID);
        $req = new Request();
        $sonuc = $req->send("GetInvoiceStatus", $send_data);
        $sonuc->INVOICE_STATUS->ACIKLAMA = Util::invoiceStatus($sonuc->INVOICE_STATUS->STATUS);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->INVOICE_STATUS;
    }

    public function getIncomingInvoice($limit = 10, $vkn = null, $pk = null, $baslangicTarih = null, $bitisTarih = null, $crbaslangicTarih = null, $crbitisTarih = null)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $this->getSessionId();
        $send_data = $req_header->getArray();

        $send_data["INVOICE_CONTENT_TYPE"] = "XML";
        $send_data["INVOICE_SEARCH_KEY"]["LIMIT"] = $limit;
        $send_data["INVOICE_SEARCH_KEY"]["LIMITSpecified"] = true;

        $send_data["INVOICE_SEARCH_KEY"]["DIRECTION"] = "IN";
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDED"] = true;
        $send_data["INVOICE_SEARCH_KEY"]["READ_INCLUDEDSpecified"] = false;

        if (!is_null($vkn)) {
            $send_data["INVOICE_SEARCH_KEY"]["RECEIVER"] = $vkn;
        }
        if (!is_null($pk)) {
            $send_data["INVOICE_SEARCH_KEY"]["TO"] = $pk;
        }
        if (!is_null($baslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["START_DATE"] = $baslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["START_DATESpecified"] = true;
        }
        if (!is_null($bitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["END_DATE"] = $bitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["END_DATESpecified"] = true;
        }

        if (!is_null($crbaslangicTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATE"] = $crbaslangicTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_START_DATESpecified"] = true;
        }
        if (!is_null($crbitisTarih)) {
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATE"] = $crbitisTarih;
            $send_data["INVOICE_SEARCH_KEY"]["CR_END_DATESpecified"] = true;
        }
        $req = new Request();
        $sonuc = $req->send("GetInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);

        $cevap = array();
        if (property_exists($sonuc, "INVOICE") && count($sonuc->INVOICE) > 0) {
            foreach ($sonuc->INVOICE as $key => $fatura) {
                $cevap[$key] = array(
                    "SENDER" => $fatura->HEADER->SENDER,
                    "RECEIVER" => $fatura->HEADER->RECEIVER,
                    "SUPPLIER" => $fatura->HEADER->SUPPLIER,
                    "CUSTOMER" => $fatura->HEADER->CUSTOMER,
                    "ISSUE_DATE" => $fatura->HEADER->ISSUE_DATE,
                    "PAYABLE_AMOUNT" => $fatura->HEADER->PAYABLE_AMOUNT->_ . " " . $fatura->HEADER->PAYABLE_AMOUNT->currencyID,
                    "FROM" => $fatura->HEADER->FROM,
                    "TO" => $fatura->HEADER->TO,
                    "PROFILEID" => $fatura->HEADER->PROFILEID,
                    "STATUS" => $fatura->HEADER->STATUS,
                    "STATUS_DESCRIPTION" => $fatura->HEADER->STATUS_DESCRIPTION,
                    "ACIKLAMA" => Util::invoiceStatus($fatura->HEADER->STATUS),
                    "GIB_STATUS_CODE" => $fatura->HEADER->GIB_STATUS_CODE,
                    "GIB_STATUS_DESCRIPTION" => $fatura->HEADER->GIB_STATUS_DESCRIPTION,
                    "RESPONSE_CODE" => $fatura->HEADER->RESPONSE_CODE,
                    "RESPONSE_DESCRIPTION" => $fatura->HEADER->RESPONSE_DESCRIPTION,
                    "FILENAME" => $fatura->HEADER->FILENAME,
                    "HASH" => $fatura->HEADER->HASH,
                    "CDATE" => new \DateTime($fatura->HEADER->CDATE),
                    "ENVELOPE_IDENTIFIER" => $fatura->HEADER->ENVELOPE_IDENTIFIER,
                    "INTERNETSALES" => $fatura->HEADER->INTERNETSALES,
                    "EARCHIVE" => $fatura->HEADER->EARCHIVE,
                    "TRXID" => $fatura->TRXID,
                    "UUID" => $fatura->UUID,
                    "ID" => $fatura->ID,
                    "TYPE" => $fatura->HEADER->INVOICE_TYPE,
                    "SENDTYPE" => $fatura->HEADER->INVOICE_SEND_TYPE
                );
            }
        }
        return $cevap;
    }

    public function getOutgoingInvoice($limit = 100, $baslangic = null, $bitis = null)
    {
        $sonuc = $this->getInvoice($limit, null, null, null, null, $baslangic, $bitis, false);
        $cevap = array();
        if (count($sonuc->INVOICE) > 0) {
            foreach ($sonuc->INVOICE as $key => $fatura) {
                $cevap[$key] = array(
                    "SENDER" => $fatura->HEADER->SENDER,
                    "RECEIVER" => $fatura->HEADER->RECEIVER,
                    "SUPPLIER" => $fatura->HEADER->SUPPLIER,
                    "CUSTOMER" => $fatura->HEADER->CUSTOMER,
                    "ISSUE_DATE" => $fatura->HEADER->ISSUE_DATE,
                    "PAYABLE_AMOUNT" => $fatura->HEADER->PAYABLE_AMOUNT->_ . " " . $fatura->HEADER->PAYABLE_AMOUNT->currencyID,
                    "FROM" => $fatura->HEADER->FROM,
                    "TO" => $fatura->HEADER->TO,
                    "PROFILEID" => $fatura->HEADER->PROFILEID,
                    "STATUS" => $fatura->HEADER->STATUS,
                    "STATUS_DESCRIPTION" => $fatura->HEADER->STATUS_DESCRIPTION,
                    "ACIKLAMA" => Util::invoiceStatus($fatura->HEADER->STATUS),
                    "GIB_STATUS_CODE" => $fatura->HEADER->GIB_STATUS_CODE,
                    "GIB_STATUS_DESCRIPTION" => $fatura->HEADER->GIB_STATUS_DESCRIPTION,
                    "RESPONSE_CODE" => $fatura->HEADER->RESPONSE_CODE,
                    "RESPONSE_DESCRIPTION" => $fatura->HEADER->RESPONSE_DESCRIPTION,
                    "FILENAME" => $fatura->HEADER->FILENAME,
                    "HASH" => $fatura->HEADER->HASH,
                    "CDATE" => $fatura->HEADER->CDATE,
                    "ENVELOPE_IDENTIFIER" => $fatura->HEADER->ENVELOPE_IDENTIFIER,
                    "INTERNETSALES" => $fatura->HEADER->INTERNETSALES,
                    "EARCHIVE" => $fatura->HEADER->EARCHIVE,
                    "TRXID" => $fatura->TRXID,
                    "UUID" => $fatura->UUID,
                    "ID" => $fatura->ID
                );
            }
        }
        return $cevap;
    }

    public function markInvoice($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["MARK"]["INVOICE"] = array("_" => "", "ID" => $faturaID);
        $req = new Request();
        $sonuc = $req->send("MarkInvoice", $send_data);
        $this->setHata($req->hataKod, $req->hataMesaj);
        return $sonuc->REQUEST_RETURN;
    }

    public function gelenFaturaKabul($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["STATUS"] = "KABUL";
        $send_data["INVOICE"]["ID"] = $faturaID;
        $req = new Request();
        $sonuc = $req->send("SendInvoiceResponseWithServerSign", $send_data);
        return $sonuc->REQUEST_RETURN;
    }

    public function gelenFaturaRed($faturaID)
    {
        $req_header = new RequestHeader();
        $req_header->session_id = $_SESSION["EFATURA_SESSION"];
        $send_data = $req_header->getArray();
        $send_data["STATUS"] = "RED";
        $send_data["INVOICE"]["ID"] = $faturaID;
        $req = new Request();
        $sonuc = $req->send("SendInvoiceResponseWithServerSign", $send_data);
        return $sonuc->REQUEST_RETURN;
    }
}