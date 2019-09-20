<?php
	/*
		This is a barcode validating page
		
		Written by Jimmy Glasscock June 2019
	*/
	
//More info for this library can be found at: http://www.phpclasses.org/package/8560-PHP-Detect-type-and-check-EAN-and-UPC-barcodes.htm
include_once('../Libraries/BarcodeValidator/clsLibGTIN.php');

//set to false for production
$debug = true;

function getCustomType($barcode){
	$firstThree = substr($barcode, 0, 3);
	$intFirstThree = (int)$firstThree;
	
	//checks for mbs specific flavor of code 39	
	if($intFirstThree > 199 && $intFirstThree < 280){
		if(strlen($barcode) == 7 || strlen($barcode) == 17){
			return "MBS Code 39";
		}
	}
	
	//checks for UPC-E
	if(strlen($barcode) == 8){
		
		$checkDigit = calculateCheckUPCDigit($result);
		
		if($checkDigit == substr($barcode, 7)){
			return "UPC-E";
		}
		
	}
	return null;
}

function checkOtherTypes($barcode){
	
	$firstThree = substr($barcode, 0, 3);
	$intFirstThree = (int)$firstThree;
	
	//checks for mbs specific flavor of code 39	
	if($intFirstThree > 199 && $intFirstThree < 280){
		if(strlen($barcode) == 7 || strlen($barcode) == 17){
			return true;
		}
	}
	
	//checks for UPC-E
	if(strlen($barcode) == 8){
		
		$checkDigit = calculateCheckUPCDigit($result);
		
		if($checkDigit == substr($barcode, 7)){
			return true;
		}
		
	}
	
	return false;
}

function calculateCheckUPCDigit($barcode){
	//calculate UPC Check digit
		$result = 0;
		//adds odd digits to result
		for($i = 1; $i < strlen($barcode); $i = $i + 2){
			$result += substr($barcode, $i, $i+1);
		}
		
		$result *= 3;
		//adds even digits to result (excluding check digit)
		for($i = 0; $i < strlen($barcode)-1; $i = $i + 2){
			$result += substr($barcode, $i, $i+1);
		}
		
		$result = $result % 10;
		
		if($result != 0){
			$result = 10 - $result;
		}
		
		return $result;
}


//Page code begins here 

include("header.php");

if(isset($_POST['barcode']) && $_POST['barcode'] != "")
{	
	
	$validity = clsLibGTIN::GTINCheck($_POST['barcode']);
	$barcodeType = clsLibGTIN::GTINCheck($_POST['barcode'],FALSE,1);
	$customType = null;
	
	if($validity == false){
		$validity = checkOtherTypes($_POST['barcode']);
		$customType = getCustomType($_POST['barcode']);
	}
	
	echo '<br><h1 align="center">This tag is ' . ($validity ? '<span style="color: green">Valid</span>' : '<span style="color: red">Invalid</span>'). '!</h1>';
	
	echo '<br><h2><strong>Barcode:</strong> '.$_POST['barcode'].'</h2><br>';
	
	if($barcodeType != false && $debug){
		echo "<p align=\"center\">Barcode Type: " . $barcodeType . "</p>";
	}
	if($customType != null && $debug){
		echo "<p align=\"center\">Barcode Type: " . $customType . "</p>";
	}
	
	echo "<br>";
}else{
	header('Location: index.html');
}

?>

<div align="center">
<form align="center" method="post" action="validate.php">
		<!--<p>Click inside the textbox, then scan the Barcode.</p>-->
		<input align="center" class="center-block" type="text" id="barcode" name="barcode" size="35" autofocus>
		<button type="submit" name="submit" style="display: none;">Submit</button>
</form>
</div>

<?php 
include("footer.php");
?>