<?php
session_name("user_session");
session_start();
session_regenerate_id(true);
?>
<body onload="javascript:moveWindow()">
<form name="pay_form" method="post" action="https://sanalpos.turkiyefinans.com.tr/fim/est3Dgate">
		<?php
			$postParams = array();
			foreach ($_POST as $key => $value){
				array_push($postParams, $key);
                echo $key . ':';
				echo "<input type=\"hidden\" name=\"" .$key ."\" value=\"" .$value."\" /><br />";
			}
			
			natcasesort($postParams);		
			
			$hashval = "";					
			foreach ($postParams as $param){				
				$paramValue = $_POST[$param];
				$escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue));	
					
				$lowerParam = strtolower($param);
				if($lowerParam != "hash" && $lowerParam != "encoding" )	{
					$hashval = $hashval . $escapedParamValue . "|";
				}
			}
			
			$storeKey = "aga28736";
			$escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey));	
			$hashval = $hashval . $escapedStoreKey;
			
			$calculatedHashValue = hash('sha512', $hashval);  
			$hash = base64_encode (pack('H*',$calculatedHashValue));
			
			echo "<input type=\"hidden\" name=\"HASH\" value=\"" .$hash."\" /><br />";
		
		?>
	</form>
<script type="text/javascript" language="javascript">
    function moveWindow() {
        document.pay_form.submit();
    }
</script>

</body>