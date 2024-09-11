<?php
	include('inc/functions.php');
		// prepare backup files
		// scan folder for all files
		$sourceFolderPath = 'inc/db_backups/factory-recovery';
		// Get the list of all files and directories in the folder
		$files = scandir($sourceFolderPath); foreach ($files as $file) {
		    // Use pathinfo to get the file extension
		    $fileInfo = pathinfo($file);
		    if ($file == "." || $file == "..") { continue; }
		    // echo $file."</br>";
		}

		// filter sql files
		// Loop through the files and filter for only sql files (to input in database)
		$allowedExtensions = array('sql');
		foreach ($files as $file) {
		    // Use pathinfo to get the file extension
		    $fileInfo = pathinfo($file);
		    // Check if the "extension" key exists and if it's in the list of allowed extensions
		    if (isset($fileInfo['extension']) && in_array(strtolower($fileInfo['extension']), $allowedExtensions)) { $sqlFiles[] = $file; }
		}

		// get the sqlfile name {gets the last value of an array}
		$sqlFileName = end($sqlFiles);
	
	// $templateProcessor->setValues(
	// 	[
	// 		"msl_num" => "$msl_num",
	// 		"vessel" => "$vessel",
	// 		"vsl_imo" => "$vsl_imo",
	// 		"vsl_call_sign" => "$vsl_call_sign",
	// 		"vsl_mmsi_number" => "$vsl_mmsi_number",
	// 		"vsl_class" => "$vsl_class",
	// 		"vsl_nationality" => "$vsl_nationality",
	// 		"vsl_registry" => "$vsl_registry",
	// 		"vsl_official_number" => "$vsl_official_number",
	// 		"vsl_nrt" => "$vsl_nrt",
	// 		"vsl_grt" => "$vsl_grt",
	// 		"vsl_dead_weight" => "$vsl_dead_weight",
	// 		"vsl_breth" => "$vsl_breth",
	// 		"vsl_depth" => "$vsl_depth",
	// 		"vsl_loa" => "$vsl_loa",
	// 		"vsl_owner_name" => "$vsl_owner_name",
	// 		"vsl_owner_address" => "$vsl_owner_address",
	// 		"vsl_owner_email" => "$vsl_owner_email",
	// 		"vsl_operator_name" => "$vsl_operator_name",
	// 		"vsl_operator_address" => "$vsl_operator_address",
	// 		"vsl_nature" => "$vsl_nature",
	// 		"vsl_cargo_name" => "$vsl_cargo_name",
	// 		"shipper_name" => "$shipper_name",
	// 		"shipper_address" => "$shipper_address",
	// 		"last_port" => "$last_port",
	// 		"capt_name" => "$capt_name",
	// 		"number_of_crew" => "$number_of_crew",
	// 		"rotation" => "$rotation",
	// 		"year" => "$year"
	// 	]
	// );
	
		// custom, consignee, owner, pni, chattrer
		if (
			isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_consignee && $survey_custom != 23 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_owner && $survey_custom != 23 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_pni && $survey_custom != 23 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_chattrer && $survey_custom != 23 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_supplier && $survey_custom != 23 


			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_custom && $survey_consignee != 23 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_owner && $survey_consignee != 23 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_pni && $survey_consignee != 23 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_chattrer && $survey_consignee != 23 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_supplier && $survey_consignee != 23 


			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_consignee && $survey_owner != 23 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_custom && $survey_owner != 23 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_pni && $survey_owner != 23 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_chattrer && $survey_owner != 23 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_supplier && $survey_owner != 23 


			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_consignee && $survey_pni != 23 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_owner && $survey_pni != 23 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_custom && $survey_pni != 23 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_chattrer && $survey_pni != 23 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_supplier && $survey_pni != 23 


			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_consignee && $survey_chattrer != 23 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_owner && $survey_chattrer != 23 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_pni && $survey_chattrer != 23 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_custom && $survey_chattrer != 23
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_supplier && $survey_chattrer != 23


			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_consignee && $survey_supplier != 23 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_owner && $survey_supplier != 23 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_pni && $survey_supplier != 23 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_custom && $survey_supplier != 23
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_chattrer && $survey_supplier != 23
		) {
			$msg = alertMsg('One survey company can\'t do more then one survey at a time!','success');
		}

	function calculateSailingDate($inputDate) {
	    $inputTimestamp = strtotime($inputDate);
	    $futureTimestamp = strtotime('+29 days', $inputTimestamp);
	    $futureDate = date('d-m-Y', $futureTimestamp);
	    return $futureDate;
	}


	function calculateFutureDate($day) { 
		$day = $day-1; $inputDate = date('d-m-Y');
	    $inputTimestamp = strtotime($inputDate);
	    $futureTimestamp = strtotime('+'.$day.' days', $inputTimestamp);
	    $futureDate = date('d-m-Y', $futureTimestamp);
	    return $futureDate;
	}
	// function calculateFutureDate($day) { 
	// 	$day = $day-1; $futureTimestamp = strtotime('+'.$day.' days', strtotime(date('d-m-Y')));
	//     $futureDate = date('d-m-Y', $futureTimestamp); return $futureDate;
	// }

	$from = $to = $totalday = $sailing = $nextdate = ""; 
	include('inc/server.php'); 
	if (isset($_POST['submit'])) {
		$from = mysqli_real_escape_string($db, $_POST['from']);
		$to = mysqli_real_escape_string($db, $_POST['to']);
		$totalday = dayCount($from, $to)."<br/>";
	}
	if (isset($_POST['lightsail'])) {
		$arrival = mysqli_real_escape_string($db, $_POST['arrival']);
		$sailing = calculateSailingDate($arrival);
	}
	if (isset($_POST['nextdate'])) {
		$from = mysqli_real_escape_string($db, $_POST['from']);
		$ttlday = mysqli_real_escape_string($db, $_POST['ttlday']);

		$day = $ttlday-1; //$inputDate = date('d-m-Y');
	    $inputTimestamp = strtotime($from);
	    $futureTimestamp = strtotime('+'.$day.' days', $inputTimestamp);
	    $nextdate = date('d-m-Y', $futureTimestamp);
	}
	$msl_num = 200;

	echo calculateFutureDate(2);

	$val = 1; $count = 0;
	if ($val == 1) {
		echo "working";$count++;
	}if ($val == 2) {
		echo "working";$count++;
	}if ($val == 1) {
		echo "working";$count++;
	}
	echo "<br/>".$count;


	if (isset($_POST['changeimporterbin'])) {
		$getconbin = mysqli_query($db, "SELECT * FROM bins WHERE type = 'IMPORTER' ");
		while ($rowbin = mysqli_fetch_assoc($getconbin)) {
			$binname = $rowbin['name']; $binbin = $rowbin['bin'];
			mysqli_query($db, "UPDATE consignee SET name = '$binname' WHERE bin = '$binbin' ");
		}
		$getbinbin = mysqli_query($db, "SELECT * FROM consignee ");
		while ($rowcon = mysqli_fetch_assoc($getbinbin)) {
			$conname = $rowcon['name']; $conbin = $rowcon['bin']; $id = $rowcon['id'];
			mysqli_query($db, "UPDATE bins SET name = '$id' WHERE bin = '$conbin' ");
		}
		echo "</br>Exchanged Successfully!</br>";
	}


	$run1 = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = 200 ");
    $row1 = mysqli_fetch_assoc($run1);
    $vessel = $row1['vessel_name'];
    $rotation = $row1['rotation'];
    if(!empty($row1['rcv_date'])){
    	$rcv_date = date('d.m.Y', strtotime($row1['rcv_date']));
    }
    else{$rcv_date = "";}
    if(!empty($row1['sailing_date'])){
    	$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));
    	$sl = date('Y-m-d',strtotime($row1['sailing_date']));
    	echo "Sailing Date working: ".$sl;
    }

    echo "<hr/>";
 //    function numberToWords($num) {
	//     $ones = array(
	//         "", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", 
	//         "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", 
	//         "seventeen", "eighteen", "nineteen"
	//     );
	    
	//     $tens = array(
	//         "", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"
	//     );

	//     $hundreds = array(
	//         "hundred", "thousand", "lakh", "crore"
	//     );

	//     if ($num == 0) {
	//         return "zero";
	//     }

	//     $num = (string) $num;

	//     // Helper function to convert numbers less than 1000
	//     function convertToWords($num, $ones, $tens) {
	//         $str = "";

	//         if ($num > 99) {
	//             $str .= $ones[intval($num / 100)] . " hundred ";
	//             $num = $num % 100;
	//         }

	//         if ($num > 19) {
	//             $str .= $tens[intval($num / 10)] . " ";
	//             $num = $num % 10;
	//         }

	//         if ($num > 0) {
	//             $str .= $ones[$num] . " ";
	//         }

	//         return $str;
	//     }

	//     $length = strlen($num);
	//     $output = "";

	//     // Process the crore place if applicable
	//     if ($length > 7) {
	//         $output .= convertToWords(intval(substr($num, 0, -7)), $ones, $tens) . "crore ";
	//         $num = substr($num, -7);
	//         $length = strlen($num);
	//     }

	//     // Process the lakh place if applicable
	//     if ($length > 5) {
	//         $output .= convertToWords(intval(substr($num, 0, -5)), $ones, $tens) . "lakh ";
	//         $num = substr($num, -5);
	//         $length = strlen($num);
	//     }

	//     // Process the thousand place if applicable
	//     if ($length > 3) {
	//         $output .= convertToWords(intval(substr($num, 0, -3)), $ones, $tens) . "thousand ";
	//         $num = substr($num, -3);
	//     }

	//     // Process the rest (hundreds and below)
	//     $output .= convertToWords(intval($num), $ones, $tens);

	//     return ucfirst(trim($output)) . " only";
	// }

	// Example usage
	$vsl_nrt = 21224;
	echo "Number to Word: ".$vsl_nrt." = ".strtoupper(numberToWords($vsl_nrt*10));  // Output: Two lakh twelve thousand two hundred forty only
	echo "<hr/>";

	$number = 100000;
	
	// function formatIndianNumber($number) {
	//     // Convert the number to a string if it is not already
	//     $numberStr = (string)$number;

	//     // Get the length of the number string
	//     $length = strlen($numberStr);

	//     // If the length is less than or equal to 3, return the number as is
	//     if ($length <= 3) { return $numberStr; }

	//     // Split the number string into parts
	//     $lastThree = substr($numberStr, -3);
	//     $remaining = substr($numberStr, 0, $length - 3);

	//     // Add commas to the remaining part of the number
	//     $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);

	//     // Combine the remaining part with the last three digits
	//     $formattedNumber = $remaining . ',' . $lastThree;

	//     return $formattedNumber;
	// }

	// Examples
	echo formatIndianNumber("1000");     // Output: 1,000
	echo "\n";
	echo formatIndianNumber("10000");    // Output: 10,000
	echo "\n";
	echo formatIndianNumber("100000");   // Output: 1,00,000
	echo "\n";
	echo formatIndianNumber("123456789"); // Output: 12,34,56,789
	






	// 
	echo "<hr/>";

	

?>
<!DOCTYPE html>
<html>
<head>
	<title>test play</title>
</head>
<body>
	<?php
	echo "</br>".date('d')."</br>".date('t');
	echo "</br>".$date = date('m', strtotime('+1 month', strtotime('2024-06-15')));
	echo "</br>Rcv Date: ".date('d.m.Y', strtotime(allDataUpdated("vessels", "msl_num", 204, "rcv_date")));
	?>
	<h1>Daycount</h1>
	<!-- <form method="post" action="test.php">
		From: <input type="date" name="from">
		To: <input type="date" name="to">
		<input type="submit" name="submit">
	</form>
	<p>
		<b>From: </b> <?php echo $from; ?>
		<b>To: </b> <?php echo $to; ?><br>
		<b>Total day:</b> <?php echo $totalday; ?>
	</p>
	<hr> -->
	<form method="post" action="test.php">
		Arrival: <input type="date" name="arrival">
		<input type="submit" name="lightsail"><br><br>
		Sailing: <?php echo $sailing; ?>
	</form>

	<form method="post" action="test.php">
		Change Importer bin: <input type="submit" name="changeimporterbin"><br><br>
	</form>

	<form method="post" action="test.php">
		Day From: <input type="date" name="from">
		Day after: <input type="number" name="ttlday">
		<input type="submit" name="nextdate"><br><br>
		Next date: <?php echo $nextdate; ?>
	</form>
	<?php
	$db = mysqli_connect('localhost', 'root', '', 'test'); 
	if (isset($_POST['booleanval'])) {
		if (isset($_POST['has_grab'])) {
			$val = $_POST['has_grab'];
			$run = mysqli_query($db,"INSERT INTO test_table(test_field,date_time)VALUES('$val',NOW())");
			if ($run) {
				echo "<h1>Data Inserted: $val</h1>";
			}
		}
			
	}
	?>
	<form method="post" action="test.php">
		<h1>Check Boolean data insert</h1>
		<?php
		$run = mysqli_query($db, "SELECT test_field FROM test_table WHERE id =1 ");
		$row = mysqli_fetch_assoc($run);
		echo $row['test_field'];
		?>
		<input class="form-check-input" type="checkbox" name="has_grab">
		<button type="submit" name="booleanval">Insert Boolean</button>
	</form>

	<?php 
	// dynamic variable name
	$year = 2018; 
	while ($year <= date('Y')) {
		$yr = "yr".$year;
		$$yr = $year;
		$year++;
	}
	echo $yr2019;
	// dynamic variable name
	// set number of months in each years
	$months = array(
		'jan' => 'jan',
		'feb' => 'feb',
		'mar' => 'mar',
		'apr' => 'apr',
		'may' => 'may',
		'jun' => 'jun',
		'jul' => 'jul',
		'aug' => 'aug',
		'sep' => 'sep',
		'oct' => 'oct',
		'nov' => 'nov',
		'dec' => 'dec'
	);
	foreach ($months as $key => $value) {
		echo "key: ".$key." And Value: ".$value."</br>";
	}
	$year = 2018;$ttl = "total".$year;
	$$ttl = 0; echo $total2018;

	function show($text = ""){
		return $text;
	}
	$val = ttlcargoqty(55);
	$expectedValue = 55495.274;
	$tolerance = 0.0001; // Adjust this tolerance based on your requirements

	if (abs($val - $expectedValue) < $tolerance) {
	    echo "</br>True</br>" . ttlcargoqty(55);
	} else {
	    echo "</br>False</br>" . gettype($val) . "</br>" . $val;
	}
	?>
<script type="text/javascript">
	const d = new Date();
	var stryear = [];
	for (let year = 2018; year <= d.getFullYear(); year++) {
		// console.log(i);
		stryear.push(year);
	}
	console.log("<?php echo show(json_encode("something")); ?>");
</script>
</body>
</html>