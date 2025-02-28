<?php 
if($_POST['isSuccessful'] == 'False'){
	echo '<div class="alert alert-danger">
			HATA !!!!  Ödeme isleminiz tamamlanamadi. <br>
			<p>Banka Cevabi : '.$_POST['resultMessage'].'</p>
		</div>';
}
else if($_POST['isSuccessful'] == 'True'){	
	echo '	<div class="alert alert-success">
				<p><span>Basarili !</span> Odeme Isleminiz Basariyla Tamamlandi.</p>
				<p>'.$_GET['MyTrxCode'].'</p>
				<p>'.$_POST['trxCode'].'</p>
			</div>';
}

?>