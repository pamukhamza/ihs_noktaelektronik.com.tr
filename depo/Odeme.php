<?php

	function aylar(){
		for($i=1;$i<=12;$i++){
				$gunler = sprintf("%02s", $i);
				echo '<option '.($gunler==date("m")?"selected":"").' value="'.$gunler.'">'.$gunler.'</option>';
		}
	}

	function yillar(){
		$yil = date("y");
		$max = 20;
		for($i=$yil;$i<=$yil+$max;$i++){
				$yillar = sprintf("%02s", $i);
				echo '<option '.($yillar==date("y")?"selected":"").' value="'.$yillar.'">'.$yillar.'</option>';
		}
	}
	
?>

<?php

if(isset($_POST['pan'])){
	
	$moka_url = "https://service.refmoka.com/PaymentDealer/DoDirectPaymentThreeD";
	$dealer_code = "xxx";
	$username = "xxx";
	$password = "xxx";
	$currency = "TL";
	$InstallmentNumber = 0;
	$OtherTrxCode = "111";
	$SubMerchantName = "";
	$RedirectUrl = "https://pos.refmoka.com/OdemeSonuc.php?MyTrxCode=".$OtherTrxCode;

	$checkkey = hash("sha256",$dealer_code."MK".$username."PD".$password);
	$veri = array('PaymentDealerAuthentication'=>array('DealerCode'=>$dealer_code,'Username'=>$username,'Password'=>$password,
														'CheckKey'=>$checkkey),
				'PaymentDealerRequest'=>array('CardHolderFullName'=>$_POST['kart_isim'],
												'CardNumber'=>$_POST['pan'],
												'ExpMonth'=>$_POST['Ecom_Payment_Card_ExpDate_Month'],
												'ExpYear'=>'20'.$_POST['Ecom_Payment_Card_ExpDate_Year'],
												'CvcNumber'=>$_POST['cv2'],
												'Amount'=>$_POST['amount'],
												'Currency'=>$currency,
												'InstallmentNumber'=>$InstallmentNumber,
												'ClientIP'=>$_SERVER['REMOTE_ADDR'],
												'RedirectUrl'=>$RedirectUrl,
												'OtherTrxCode'=>$OtherTrxCode,
												'SubMerchantName'=>$SubMerchantName));


	$veri = json_encode($veri);
	$ch = curl_init($moka_url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $veri);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);	  // TLS 1.2 baglanti destegi icin
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);    // ssl sayfa baglantilarinda aktif edilmeli
	$result = curl_exec ($ch); 
	curl_close ($ch);
	$result = json_decode($result);

	if($result->ResultCode == 'Success'){
		echo '<div class="alert alert-success">
			<p>KART BILGILERINIZ DOGRULANDI !!!
			<br><br>
				Simdi kart güvenligini dogrulamak için bankanin sayfasina yönlendirileceksiniz. 
			<br>
				Burada kart sahibinin bankada kayitli cep telefonuna gelecek SMS\'i girdikten sonra islemleriniz tamamlanacaktir.
			</p>
			<p>
				<a href="'.$result->Data.'" class="btn btn-primary">Yönlendirme çalismaz ise buraya tiklayin...</a>				
			</p>
		</div>';
		header("Location: ".$result->Data);
		exit;
	}else{
		echo '<div class="alert alert-danger">
			<p>Kart bilgileri dogrulanamadi. Tekrar denemek için <a href="#" onclick="window.location.reload();">buraya tiklayin.</a></p>
		</div>';
	}

}else{
?>

<form method="post" action="">
	<table class="table table-condensed table-bordered" style="border:1px solid #ccc;background:#fbfbfb;margin:5px;width:99%;">
		<tr>
			<td style="font-size:15px;width:210px;"><b>Cekilecek Tutar</b></td>
			<td>
				<div class="col-sm-5">
				  <input type="text" class="form-control" id="amount" name="amount" onkeyup="sadece_para('amount');" placeholder="Cekilecek Tutar" value="<?=$_POST['amount']?>">
				</div>
			</td>
		</tr>
		<tr>
			<td style="font-size:15px;width:210px;"><b>Kredi Karti Uzerindeki Isim</b></td>
			<td>
				<div class="col-sm-5">
				  <input type="text" class="form-control" id="kart_isim" name="kart_isim" placeholder="Kredi Karti Uzerindeki Isim" autocomplete=off  size="20">
				</div>
			</td>
		</tr>
		<tr>
			<td style="font-size:15px;width:210px;"><b>Kredi Kart Numarasi</b></td>
			<td>
				<div class="col-sm-5">
				  <input type="text" class="form-control" id="pan" name="pan"  maxlength="16" onkeydown="sadece_rakam('pan');" placeholder="Kredi Kart Numarasi" autocomplete=off  size="20">
				</div>
			</td>
		</tr>
		<tr>
			<td style="font-size:15px;"><b>Son Kullanma Ay / Yil: </b></td>
			<td style="text-align:left;">
				<div class="col-sm-8"  style="float:left;">
					  <select style="width:80px;padding:3px;font-size:15px;" name="Ecom_Payment_Card_ExpDate_Month" id="Ecom_Payment_Card_ExpDate_Month">
							<?php aylar();?>
						</select>
						/
						<select style="width:80px;padding:3px;font-size:15px;" name="Ecom_Payment_Card_ExpDate_Year" id="Ecom_Payment_Card_ExpDate_Year">
							<?php yillar();?>
						</select>
				</div>
			</td>
		</tr>
		<tr>
			<td style="font-size:15px;"><b>Guvenlik Kodu: <br/><span style="font-weight:100;">(Kartin arkasinda ki son 3 hane)</span></b></td>
			<td>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="cv2" name="cv2" maxlength="4" placeholder="Guvenlik Kodu" autocomplete=off size="4" value="">
				</div>
				<button type="submit" id="devamEt" style="float:right;margin:20px;" class="btn btn-primary">Devam Et</button>
			</td>
		</tr>
	</table>
</form>

<?php } ?>
