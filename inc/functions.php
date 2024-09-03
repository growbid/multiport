<?php
	include_once('conn.php');

	// For phpword
	require_once 'vendor/autoload.php';

	// FOR DATABASE BACKUP
	include_once('Mysqldump.php');

	// Convert number to words
	function numberToWords($num) {
	    $ones = array(
	        "", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", 
	        "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", 
	        "seventeen", "eighteen", "nineteen"
	    );  
	    $tens = array(
	        "", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"
	    );
	    $hundreds = array( "hundred", "thousand", "lakh", "crore" );
	    if ($num == 0) { return "zero"; }
	    $num = (string) $num;
	    // Helper function to convert numbers less than 1000
	    function convertToWords($num, $ones, $tens) {
	        $str = "";
	        if ($num > 99) {
	            $str .= $ones[intval($num / 100)] . " hundred ";
	            $num = $num % 100;
	        }
	        if ($num > 19) {
	            $str .= $tens[intval($num / 10)] . " ";
	            $num = $num % 10;
	        }
	        if ($num > 0) { $str .= $ones[$num] . " "; }
	        return $str;
	    }
	    $length = strlen($num); $output = "";
	    // Process the crore place if applicable
	    if ($length > 7) {
	        $output .= convertToWords(intval(substr($num, 0, -7)), $ones, $tens) . "crore ";
	        $num = substr($num, -7);
	        $length = strlen($num);
	    }
	    // Process the lakh place if applicable
	    if ($length > 5) {
	        $output .= convertToWords(intval(substr($num, 0, -5)), $ones, $tens) . "lakh ";
	        $num = substr($num, -5);
	        $length = strlen($num);
	    }
	    // Process the thousand place if applicable
	    if ($length > 3) {
	        $output .= convertToWords(intval(substr($num, 0, -3)), $ones, $tens) . "thousand ";
	        $num = substr($num, -3);
	    }
	    // Process the rest (hundreds and below)
	    $output .= convertToWords(intval($num), $ones, $tens);

	    return ucfirst(trim($output)) . " only";
	}

	function formatIndianNumber($number) {
	    // Convert the number to a string if it is not already
	    $numberStr = (string)$number;
	    // Get the length of the number string
	    $length = strlen($numberStr);
	    // If the length is less than or equal to 3, return the number as is
	    if ($length <= 3) { return $numberStr; }
	    // Split the number string into parts
	    $lastThree = substr($numberStr, -3);
	    $remaining = substr($numberStr, 0, $length - 3);
	    // Add commas to the remaining part of the number
	    $remaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining);
	    // Combine the remaining part with the last three digits
	    $formattedNumber = $remaining . ',' . $lastThree;
	    return $formattedNumber;
	}

	// RESTORE DB
	function restoreMysqlDB($filePath){ 
		GLOBAL $db; $sql = ''; $error = '';
		if (file_exists($filePath)) {
			$lines = file($filePath);
			foreach ($lines as $line) {
				// Ignoring comments from the SQL script
				if (substr($line, 0, 2) == '--' || $line == '') { continue; }
				$sql .= $line;
				if (substr(trim($line), - 1, 1) == ';') {
					$result = mysqli_query($db, $sql);
					if (! $result) { $error .= mysqli_error($db) . "\n"; } $sql = '';
				}
			} // end foreach
			if ($error) { $response = array( "type" => "error", "message" => $error ); } 
			else{$response = array("type"=>"success","message"=>"Restored Successfully.");}
		} // end if file exists
		return $response;
	}

	// get pagename
	function pagename(){
		$pagename = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		return $pagename;
	}
	// get urlval
	function pageurl(){
		$urlval = "";
		if (strpos($_SERVER['REQUEST_URI'], "?") !== false) {
			$urlval = substr($_SERVER["REQUEST_URI"],strrpos($_SERVER["REQUEST_URI"],"?"));
		} return $urlval;
	}

	function isLeapYear($year){
		if(($year%4==0&&$year%100!=0)||($year%400==0)){return true;}else{return false;}
	}

	// Err message
	function alertMsg($msg = "Error Message Here", $type = "success"){
		$val = "<div class=\"alert alert-$type\" role=\"alert\">$msg</div>"; return $val;
	}

	// functions
	function allData($tableName = "users", $id = 1, $data = "name"){
		GLOBAL $db; $sql = "SELECT * FROM $tableName WHERE id = $id ORDER BY id LIMIT 1";
		$run = mysqli_query($db, $sql); 
		if (mysqli_num_rows($run)>0) { $row = mysqli_fetch_assoc($run); $output = $row[$data]; }
		else{$output = "";} return "$output";
	}

	function allDataUpdated($tableName = "users", $fieldName = "id", $fieldValue = 1, $data = "name"){
		GLOBAL $db;$sql="SELECT*FROM $tableName WHERE $fieldName = $fieldValue ORDER BY id LIMIT 1";
		$run = mysqli_query($db, $sql); 
		if (mysqli_num_rows($run)>0){ $row = mysqli_fetch_assoc($run); $output = $row[$data]; }
		else{$output = "";} return "$output";
	}

	function getdata($tableName = "users", $query = "id = 1", $data = "username"){
		GLOBAL $db;$sql="SELECT * FROM $tableName WHERE ".$query." ORDER BY id LIMIT 1";
		$run = mysqli_query($db, $sql); 
		if (mysqli_num_rows($run)>0){ $row = mysqli_fetch_assoc($run); return $row[$data];}
		else{return "Empty";}
	}

	function rawcount($tableName = "users", $query = ""){
		GLOBAL $db; if (empty($query)) { $sql = "SELECT * FROM $tableName"; }
		else{$sql="SELECT * FROM $tableName WHERE ".$query." ";}
		$run = mysqli_query($db, $sql); $num = mysqli_num_rows($run); return (int)$num;
	}

	// get total vessel number in a specific year
	function vslcountyr($year = ""){
		GLOBAL $db; $query = "YEAR(STR_TO_DATE(rcv_date, '%d-%m-%Y')) = '$year'";
		if (empty($year)) { $sql = "SELECT * FROM $vessels"; }
		else{$sql="SELECT * FROM vessels WHERE ".$query." ";}
		$run = mysqli_query($db, $sql); $num = mysqli_num_rows($run); return (int)$num;
	}

	// GET TOTAL CARGO QUANTITY FROM vessels_cargo
	function gettotal($tableName = "vessels_cargo", $fieldName = "msl_num", $fieldValue = 111, $data = "quantity"){
		GLOBAL $db; $sql = "SELECT $data FROM $tableName WHERE $fieldName = $fieldValue";
		$run = mysqli_query($db, $sql); $output = 0;
		if(mysqli_num_rows($run)>0){
			while($row=mysqli_fetch_assoc($run)){$output=$output+$row[$data];}
		} else{$output = "";} return "$output";
	}
	// GET TOTAL CARGO QUANTITY FROM vessels_cargo
	function ttlcargoqty($msl_num = 111){
		GLOBAL $db; $sql = "SELECT quantity FROM vessels_cargo WHERE msl_num = '$msl_num' ";
		$run = mysqli_query($db, $sql); $output = 0; if(mysqli_num_rows($run)>0){
			while($row=mysqli_fetch_assoc($run)){$output=$output+$row['quantity'];}
		}else{$output = "";} return $output;
	}

	function lastData($tableName = "users", $data = "name"){
		GLOBAL $db; $sql = "SELECT $data FROM $tableName ORDER BY id DESC LIMIT 1";
		$run = mysqli_query($db, $sql); $row = mysqli_fetch_assoc($run);
		$output = $row[$data]; return "$output";
	}

	// COUNT PDF PAGES
	function countPages($path) { 
		$pdf=file_get_contents($path);$number=preg_match_all("/\/Page\W/",$pdf,$dummy); 
		return $number; 
	}

	// checkLogin
	function checkLogin(){if(!isset($_SESSION['id'])){header("location: login.php");}}

	// select options
	function selectOptions($database = "users", $fieldName = "name", $selected=""){
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM $database ");
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $value = $row[$fieldName];
			echo"<option value=\"$id\" $selected>$value</option>";
		}
	}

	function dayCount($from=0, $to = 0){
		$begin = strtotime($from); $end = strtotime($to);
		$value = round(($end-$begin) / (60 * 60 * 24) + 1);
		return $value;
	}

	function exist($tableName = "users", $query = "id = 1"){
		GLOBAL $db;$sql="SELECT * FROM $tableName WHERE ".$query." ORDER BY id LIMIT 1";
		$run = mysqli_query($db, $sql); if (mysqli_num_rows($run)>0){ return true; }
		else{return false;}
	}



	function percentage($msl_num = 111){
		GLOBAL $db; $msl = $name = $arrived = $rcv_date = $com_date = $sailing_date = $importer = $stevedore = $representative = $rotation = $anchor = $custom_survey = $consignee_survey = $received_by = $sailed_by = $cargo = $outer_qty = $kutubdia_qty = $retention_qty = $custom_load = $custom_light = $consignee_load = $consignee_light = $supplier_load = $supplier_light = $pni_load = $pni_light = $chattrer_load = $chattrer_light = $owner_load = $owner_light = $cnf_count = 0; 

		$run = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
		$row = mysqli_fetch_assoc($run); 

		$outer = floatval($row['outer_qty']); $kutubdia = floatval($row['kutubdia_qty']);
		$retention = floatval($row['retention_qty']);

		$total_qty = floatval(ttlcargoqty($msl_num)); // total vessels cargo qty

		$ttlctgqty = $outer + $kutubdia; $ttlqtyplused = $outer + $kutubdia + $retention;


		// static item count start
		if(!empty($row['msl_num'])){$msl = 1;}
		if(!empty($row['vessel_name'])){$name = 1;}
		if(!empty($row['arrived'])){$arrived=1;}
		if(!empty($row['rcv_date'])){$rcv_date=1;}
		if(!empty($row['com_date'])){$com_date=1;}
		if(!empty($row['sailing_date'])){$sailing_date=1;}
		if(!empty($row['outer_qty']) || $total_qty == $ttlctgqty){$outer_qty=1;}
		if(exist("vessels_importer","msl_num = ".$msl_num." ")==1){$importer=1;}
		if($row['stevedore'] != 0){$stevedore=1;} 
		if(!empty($row['rotation'])){$rotation=1;}
		if($row['representative'] != 0){$representative=1;}
		if(!empty($row['anchor'])){$anchor=1;}
		if($row['received_by']!=0){$received_by=1;} 
		if($row['sailed_by']!=0){$sailed_by=1;}
		if($row['survey_custom']!=0){$custom_survey=1;}
		if($row['survey_consignee']!=0){$consignee_survey=1;}
		if(exist("vessels_cargo","msl_num = ".$msl_num." ")==1){$cargo=1;}

		// count surveyors and survey companies
		if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1) { $custom_load = 1;}
		if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1) { $custom_light = 1;}
		if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1){ $consignee_load = 1;}
		if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1){ $consignee_light = 1;}
		// static item count end

		// total static item count]
		$itemcount = 21;

		// In "if(`$ttlqtyplused` < `$total_qty`)" the `` is needed for folating precision check

		// checks if outer qty is smaller then total cargo qty(from vessels_cargo).
		if($ttlqtyplused < $total_qty){
			$con1 = $outer + $kutubdia; $con2 = $outer + $retention;
			if ($con1 < $total_qty) { $itemcount++; 
				if(!empty($row['retention_qty'])&&$con1==$total_qty){$retention_qty=1;}
			} if ($con2 < $total_qty) { $itemcount++; 
				if(!empty($row['kutubdia_qty'])&&$con2==$total_qty){$kutubdia_qty=1;}
			}
		}


		if ($row['survey_supplier'] != 0) {
			$itemcount = $itemcount + 2;
			if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1){
				$supplier_load = 1;
			}if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1){
				$supplier_light = 1;
			}
		}if ($row['survey_pni'] != 0) {
			$itemcount = $itemcount + 2;
			if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1){
				$pni_load = 1;
			}if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1){
				$pni_light = 1;
			}
		}if ($row['survey_chattrer'] != 0) {
			$itemcount = $itemcount + 2;
			if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1){
				$chattrer_load = 1;
			}if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1){
				$chattrer_light = 1;
			}
		}if ($row['survey_owner'] != 0) {
			$itemcount = $itemcount + 2;
			if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose = 'Load Draft' AND surveyor != 0 ")==1){
				$owner_load = 1;
			}if (exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose != 'Load Draft' AND surveyor != 0 ")==1){
				$owner_light = 1;
			}
		}

		// count cnf from inporter
		$run_cnf = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		while ($row_cnf = mysqli_fetch_assoc($run_cnf)) {
			$checkCnf = $row_cnf['cnf']; $itemcount++; if ($checkCnf != 0) { $cnf_count++; }
		}
		

		$filled = $msl + $name + $arrived + $rcv_date + $com_date + $sailing_date + $importer + $stevedore + $representative + $rotation + $anchor + $custom_survey + $consignee_survey + $received_by + $sailed_by + $cargo + $outer_qty + $kutubdia_qty + $retention_qty + $custom_load + $custom_light + $consignee_load + $consignee_light + $supplier_load + $supplier_light + $pni_load + $pni_light + $chattrer_load + $chattrer_light + $owner_load + $owner_light + $cnf_count;
		// $filled = $percentage;

		// 15 items, so $division = 15 / 100 = 0.15; $filled / 0.15 = result
		$division = $itemcount / 100;
		$percentage = round($filled / $division);

		return $percentage;
	}



	function vesselsCnfTag($msl_num = 111){
		GLOBAL $db; 
		$run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		$cnfName = array();
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $importer = $row['importer'];$cnf = $row['cnf']; 
			$importer = allData('bins', $importer, 'name'); if ($cnf != 0) {
				$cnfNm = allData('cnf', $cnf, 'name');
				// Check if the value exists in the array
				if (!in_array($cnfNm, $cnfName)) {
				    // If not, add the value to the array
				    $cnfName[] = $cnfNm;
				}
			}
		}
		// extract and convert array values to string
		$output = implode(", ", $cnfName) . ",";
		return $output;
	}

	// 
	function vesselsImporterTag($msl_num = 111){
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		$importerName = array(); while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $importerId = $row['importer']; 
			if ($importerId != 0) { $importerNm = allData('bins', $importerId, 'name');
				// Check if the value exists in the array
				if (!in_array($importerNm, $importerName)) { $importerName[] = $importerNm; }
			}else{}
		} $output = implode(", ", $importerName) . ","; return $output;
	}

	// 
	function vesselsCargoTag($msl_num = 111){
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE msl_num = '$msl_num' ");
		$cargoKeyTag = array(); $loadPortTag = array(); $portCodeTag = array(); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $cargoKeyId = $row['cargo_key']; $loadPortId = $row['loadport'];
			if ($cargoKeyId != 0 && $loadPortId != 0) { 
				$cargoKeyNm = allData('cargokeys', $cargoKeyId, 'name');
				$loadPortNm = allData('loadport', $loadPortId, 'port_name');
				$portCodeNm = allData('loadport', $loadPortId, 'port_code');
				// Check if the value exists in the array
				if (!in_array($cargoKeyNm, $cargoKeyTag)) { $cargoKeyTag[] = $cargoKeyNm; }
				if (!in_array($loadPortNm, $loadPortTag)) { $loadPortTag[] = $loadPortNm; }
				if (!in_array($portCodeNm, $portCodeTag)) { $portCodeTag[] = $portCodeNm; }
			}else{}
		} $output = implode(", ", $cargoKeyTag) . "," . implode(", ", $loadPortTag) . "," . implode(", ", $portCodeTag) . ","; 
		return $output;
	}

	
	function allVessels($key = "all", $query = ""){
		$rcvrnm=$repnm=$stvdrnm=$slnm=$consigneeName="";
		$survey_consignee=$survey_custom=$survey_supplier=$survey_pni=$survey_chattrer=$survey_owner="";
		$remarksName = ""; GLOBAL $db; 
		if ($key == "upcoming") {
			$dynamicsql = "SELECT * FROM vessels WHERE rcv_date = '' ORDER BY msl_num DESC ";
		}elseif ($key == "online") {
			$dynamicsql = "SELECT * FROM vessels WHERE rcv_date != '' AND sailing_date = '' ORDER BY msl_num DESC ";
		}elseif ($key == "completed") {
			$dynamicsql = "SELECT * FROM vessels WHERE rcv_date != '' AND sailing_date != '' ORDER BY msl_num DESC ";
		}elseif ($key!="upcoming"&&$key!="online"&&$key!="completed"&&$key!="query"&&$key!="all") {
			$dynamicsql = "SELECT * FROM vessels WHERE YEAR(STR_TO_DATE(rcv_date, '%d-%m-%Y')) = '$key' ";
		}
		// filter
		elseif ($key == "query" && isset($query) && !empty($query)) {
			// set the array value empty
			$mslnums_importer = $mslnums_portcode = $mslnums_cargo = $mslnums_cnf = $mslnums_surveyor = $mslnums_surveycompany = array();
			$dynamicsql = "SELECT * FROM vessels WHERE msl_num != '' ";

			// gather the datas received from server and store in arrays.
			// filter representative
			if (isset($query['fltrrepresentative']) && !empty($query['fltrrepresentative'])) { // 
				$rep = $query['fltrrepresentative']; $dynamicsql .= "AND representative = '$rep' ";
			} // filter importer
			if (isset($query['fltrimporter']) && !empty($query['fltrimporter'])) {
				$importer_ids_str = implode(',', $query['fltrimporter']); 
				$getvsl = "SELECT * FROM vessels_importer WHERE importer IN ($importer_ids_str)";
				$r = mysqli_query($db, $getvsl); while ($rw = mysqli_fetch_assoc($r)) {
					$mslnums_importer[] = $rw['msl_num'];
				}
			} //filter load port
			if (isset($query['fltrportcode']) && !empty($query['fltrportcode'])) {
				// convert portcode id to string like {port_ids_str = "1,2,4,7";}
				$port_ids_str = implode(',', $query['fltrportcode']); 
				// select from vessels_cargo which has these port codes
				$getvsl = "SELECT * FROM vessels_cargo WHERE loadport IN ($port_ids_str)";
				$r = mysqli_query($db, $getvsl); 
				// checks if found any vessel
				if(mysqli_num_rows($r) > 0){
					while ($rw = mysqli_fetch_assoc($r)) {
						// store the msl_num in an array named $mslnums_portcode[]
						$mslnums_portcode[] = $rw['msl_num'];
					}
				}
				// if didn't find any vessel, stores zero in the array
				else{$mslnums_portcode[] = 0;}
				
			} //filter cargo
			if (isset($query['fltrcargo']) && !empty($query['fltrcargo'])) {
				$cargo_ids_str = implode(',', $query['fltrcargo']); 
				$getvsl = "SELECT * FROM vessels_cargo WHERE cargo_key IN ($cargo_ids_str)";
				$r = mysqli_query($db, $getvsl); while ($rw = mysqli_fetch_assoc($r)) {
					$mslnums_cargo[] = $rw['msl_num'];
				}
			} //filter cnf
			if (isset($query['fltrcnf']) && !empty($query['fltrcnf'])) {
				$cnf_ids_str = $query['fltrcnf']; 
				$getvsl = "SELECT * FROM vessels_importer WHERE cnf IN ($cnf_ids_str)";
				$r = mysqli_query($db, $getvsl); while ($rw = mysqli_fetch_assoc($r)) {
					$mslnums_cnf[] = $rw['msl_num'];
				}
			} //filter surveyor
			if (isset($query['fltrsurveyor']) && !empty($query['fltrsurveyor'])) {
				$surveyor_ids_str = $query['fltrsurveyor']; 
				$getvsl = "SELECT * FROM vessels_surveyor WHERE surveyor IN ($surveyor_ids_str)";
				$r = mysqli_query($db, $getvsl); if (mysqli_num_rows($r) > 0) {
					while($rw=mysqli_fetch_assoc($r)){$mslnums_surveyor[]=$rw['msl_num'];}
				}else{$msl_str_surveyor = 0;} 
			} //filter survey company
			if (isset($query['fltrsurveycompany']) && !empty($query['fltrsurveycompany'])) {
				$surveycompany_ids_str = $query['fltrsurveycompany']; 
				$getvsl = "SELECT * FROM vessels_surveyor WHERE survey_company IN ($surveycompany_ids_str)";
				$r = mysqli_query($db, $getvsl); if (mysqli_num_rows($r) > 0) {
					while($rw=mysqli_fetch_assoc($r)){$mslnums_surveycompany[]=$rw['msl_num'];}
				}else{$msl_str_surveycompany = 0;} 
			} // filter stevedore
			if (isset($query['fltrstevedore']) && !empty($query['fltrstevedore'])) {
				$stevedore = $query['fltrstevedore']; $dynamicsql .= "AND stevedore = '$stevedore' ";
			} // filter vsl_opa
			if (isset($query['fltropa']) && !empty($query['fltropa'])) {
				$vsl_opa = $query['fltropa']; $dynamicsql .= "AND vsl_opa = '$vsl_opa' ";
			} // filter kutubdia
			if (isset($query['fltrkutubdia']) && !empty($query['fltrkutubdia'])) {
				$kutubdia = $query['fltrkutubdia']; $dynamicsql .= "AND anchor = 'Kutubdia' ";
			} // filter outer
			if (isset($query['fltrouter']) && !empty($query['fltrouter'])) {
				$outer = $query['fltrouter']; $dynamicsql .= "AND anchor = 'Outer' ";
			} // filter 78 permission granted
			if (isset($query['fltrseventyeight']) && !empty($query['fltrseventyeight'])) {
				$seventyeight = $query['fltrseventyeight']; $dynamicsql .= "AND seventyeight_qty != '' ";
			}// filter custom_visit
			if (isset($query['fltrcustom']) && !empty($query['fltrcustom'])) {
				$custom_visited = $query['fltrcustom']; $dynamicsql .= "AND custom_visited = '1' ";
			}// filter qurentine_visited
			if (isset($query['fltrqurentine']) && !empty($query['fltrqurentine'])) {
				$qurentine_visited = $query['fltrqurentine']; $dynamicsql .= "AND qurentine_visited = '1' ";
			}// filter psc_visited
			if (isset($query['fltrpsc']) && !empty($query['fltrpsc'])) {
				$psc_visited = $query['fltrpsc']; $dynamicsql .= "AND psc_visited = '1' ";
			}// filter multiple_lightdues
			if (isset($query['fltrlightdues']) && !empty($query['fltrlightdues'])) {
				$multiple_lightdues = $query['fltrlightdues']; $dynamicsql .= "AND multiple_lightdues = '1' ";
			}// filter crew_change
			if (isset($query['fltrcrew']) && !empty($query['fltrcrew'])) {
				$crew_change = $query['fltrcrew']; $dynamicsql .= "AND crew_change = '1' ";
			}// filter has_grab
			if (isset($query['fltrgrab']) && !empty($query['fltrgrab'])) {
				$has_grab = $query['fltrgrab']; $dynamicsql .= "AND has_grab = '1' ";
			}// filter fender
			if (isset($query['fltrfender']) && !empty($query['fltrfender'])) {
				$fender = $query['fltrfender']; $dynamicsql .= "AND fender = '1' ";
			}// filter fresh_water
			if (isset($query['fltrwater']) && !empty($query['fltrwater'])) {
				$fresh_water = $query['fltrwater']; $dynamicsql .= "AND fresh_water = '1' ";
			}// filter piloting
			if (isset($query['fltrpiloting']) && !empty($query['fltrpiloting'])) {
				$piloting = $query['fltrpiloting']; $dynamicsql .= "AND piloting = '1' ";
			}


			// Create an array containing all five arrays
			$arrays = array($mslnums_importer, $mslnums_portcode, $mslnums_cargo, $mslnums_cnf, $mslnums_surveyor, $mslnums_surveycompany);

			// Filter out empty arrays
			$non_empty_arrays = array_filter($arrays, function($arr) { return !empty($arr); });

			// Check if there are at least two non-empty arrays
			if (count($non_empty_arrays) >= 2) {
			    // Find common values among non-empty arrays
			    $common_vessels = call_user_func_array('array_intersect', $non_empty_arrays);
			    // convert array to string using implode
			    $msl_str_common = implode(',', $common_vessels);
			    $dynamicsql .= "AND msl_num IN ($msl_str_common)";
			}elseif (count($non_empty_arrays) === 1) {
			    // if has only one array, it makes it usable to implode
			    $common_vessels = reset($non_empty_arrays); 
			    $msl_str_common = implode(',', $common_vessels);
			    $dynamicsql .= "AND msl_num IN ($msl_str_common)";
			} else { $msl_str_common = 0; }


			if (isset($query['dates']) && !empty($query['dates'])) {
				$dates = $query['dates']; $from = $dates[0]; $to = $dates[1];
				$dynamicsql .= "AND STR_TO_DATE(rcv_date, '%d-%m-%Y') BETWEEN '$from' AND '$to'";
			}
		}else{$dynamicsql = "SELECT * FROM vessels ";}
		// show dynamicsql
		echo "<p>".$dynamicsql."</p>";
		$run = mysqli_query($db, $dynamicsql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; //
			$msl_num = $row['msl_num']; //
			$vessel_name = $row['vessel_name']; //
			$rcv_date = $row['rcv_date']; //
			$sailing_date = $row['sailing_date']; //
			$remarksName = $row['remarks'];
			$anchor = $row['anchor'];
			
			
			$runsurvey = mysqli_query($db, "SELECT * FROM surveycompany WHERE id = ".$row['id']." ");
			$survey_consignee = allData('surveycompany', $row['survey_consignee'], 'company_name');
			$survey_custom = allData('surveycompany', $row['survey_custom'], 'company_name');
			$survey_supplier = allData('surveycompany', $row['survey_supplier'], 'company_name');
			$survey_pni = allData('surveycompany', $row['survey_pni'], 'company_name');
			$survey_chattrer = allData('surveycompany', $row['survey_chattrer'], 'company_name');
			$survey_owner = allData('surveycompany', $row['survey_owner'], 'company_name');

			$cargonm = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_key');
			$loadport = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'loadport');
			if(!is_bool($loadport)&&$loadport!=0){
				$port_code=allData('loadport',$cargonm,'port_code');
				$loadportnm = allData('loadport',$cargonm,'port_name');
			}else{$loadportnm = $port_code = "";}
			
			if(!is_bool($cargonm)&&$cargonm!=0){$cargo_short_name=allData('cargokeys',$cargonm,'name');}
			else{$cargo_short_name="";}
			$cargo_bl_name = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_bl_name');
			$qty = gettotal('vessels_cargo', 'msl_num', $msl_num, 'quantity');

			$stevedore = $row['stevedore']; // 
			if(!is_bool($stevedore)){$stvdrnm = allData('stevedore', $stevedore, 'name'); }
			
			$received_by = $row['received_by'];  //
			if(!is_bool($received_by) && $received_by != 0){$rcvrnm = allData('users', $received_by, 'name');}

			$sailed_by = $row['sailed_by']; //
			if(!is_bool($sailed_by) && $sailed_by != 0){$slnm = allData('users', $sailed_by, 'name'); }
			
			$representative = $row['representative']; //
			if(!is_bool($representative) && $representative != 0){$repnm = allData('users', $representative, 'name'); }
			$status = $row['status']; //
			
			$consignee = allDataUpdated('vessels_importer', 'msl_num', $msl_num, 'importer'); //
			if(!is_bool($consignee) && $consignee != 0){$consigneeName = allData('bins', $consignee, 'name'); }
			// 							table name 		field name 	field value  data
			// $remarks = allDataUpdated('vessels_remarks', 'msl_num', $msl_num, 'remarks'); //
			// if(!is_bool($remarks) && $remarks != 0){$remarksName = allData('remarks', $remarks, 'name'); }
			

						echo "
				<tr>
					<th scope=\"row\">$msl_num</th>
					<td>
						<a href=\"vessel_details.php?edit=$msl_num\">
							MV.$vessel_name
						</a>
					</td>
					<td>$cargo_short_name</td>
					<td>$qty MT</td>

					<!-- SEARCH KEYS -->
					<td style=\"text-align:center;\">
						$repnm 
					</td>

					<td style=\"text-align:center;\">
						".percentage($msl_num)." % 
					</td>

					<td class= style=\"display:inline-block;\">
						".$rcv_date."
						<span style=\"display:none;\">
							$id, $stvdrnm, $remarksName, $rcv_date, $rcvrnm, $slnm, $anchor, $survey_consignee, $survey_custom, $survey_supplier, $survey_pni, $survey_chattrer, $survey_owner, ".vesselsImporterTag($msl_num).vesselsCnfTag($msl_num).vesselsCargoTag($msl_num)."
						</span>
						<!--a href=\"vessel_details.php?msl_num=$msl_num\" class=\"btn btn-success btn-sm\">
							<i class=\"bi bi-file-earmark-break\"></i>
						</a>
						<a href=\"vessel_details.php?edit=$msl_num\" class=\"btn btn-warning btn-sm\">
							<i class=\"bi bi-pencil\" style=\"color: white;\"></i>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"index.php?del_msl_num=$msl_num\" 
							class=\"btn btn-danger btn-sm\"
						><i class=\"bi bi-trash\"></i></a-->
					</td>
                </tr>
			";
			$repnm = "";
		}
	}

	function vesselSurveyors($msl_num = 111, $type = "load"){
		if ($type == "load") {$type = "AND survey_purpose = \"Load Draft\"";}
		elseif($type=="all"){$type = "";}
		else{$type = "AND survey_purpose != \"Load Draft\"";}
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' ".$type." ");
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $survey_party = $row['survey_party'];
			$survey_company = $row['survey_company']; $survey_purpose = $row['survey_purpose'];
			$company_name = allData('surveycompany', $survey_company, 'company_name');
			$surveyorId = $row['surveyor']; $surveyor = allData('surveyors', $surveyorId, 'surveyor_name');
			echo "
				<tr>
					<td scope=\"col\">$survey_party</td>
					<td scope=\"col\">$company_name</td>
					<td scope=\"col\">$survey_purpose</td>
					<td scope=\"col\">$surveyor</td>
					<td scope=\"col\">
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editVesselSurveyors$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i> Edit</span>
						</a>
						<!--a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"vessel_details.php?edit=$msl_num&delVesselSurveyors=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a-->
					</td>
				</tr>
			";
		}
	}

	function vesselSurveyorsUpdated($msl_num = 111, $type = "load"){
		if ($type == "load") {$type = "AND survey_purpose = \"Load Draft\"";}
		elseif($type=="all"){$type = "";}
		else{$type = "AND survey_purpose != \"Load Draft\"";}
		GLOBAL $db; 

		$runvsl = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
		$rowvsl = mysqli_fetch_assoc($runvsl);
		$consignee = $rowvsl['survey_consignee']; $custom = $rowvsl['survey_custom'];
		$supplier = $rowvsl['survey_supplier']; $pni = $rowvsl['survey_pni'];
		$chattrer = $rowvsl['survey_chattrer']; $owner = $rowvsl['survey_owner'];

		$run = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' ".$type." ");
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $survey_party = $row['survey_party'];
			$survey_company = $row['survey_company']; $survey_purpose = $row['survey_purpose'];
			$company_name = allData('surveycompany', $survey_company, 'company_name');
			$surveyorId = $row['surveyor']; $surveyor = allData('surveyors', $surveyorId, 'surveyor_name');
			echo "
				<tr>
					<td scope=\"col\">$survey_party</td>
					<td scope=\"col\">$company_name</td>
					<td scope=\"col\">$survey_purpose</td>
					<td scope=\"col\">$surveyor</td>
					<td scope=\"col\">
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editVesselSurveyors$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i></span>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"vessel_details.php?edit=$msl_num&delVesselSurveyors=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a>
					</td>
				</tr>
			";
		}
	}

	function vesselCargo($msl_num = 111){
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE msl_num = '$msl_num' ");
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $cargo_key = $row['cargo_key']; $loadport = $row['loadport']; 
			$quantity = $row['quantity']; $cargo_bl_name = $row['cargo_bl_name']; 
			$cargo = allData('cargokeys', $cargo_key, 'name');
			$loadportnm = allData('loadport', $loadport, 'port_name');
			echo "
				<tr>
					<td scope=\"col\">$cargo</td>
					<td scope=\"col\">$loadportnm</td>
					<td scope=\"col\">$quantity</td>
					<td scope=\"col\">$cargo_bl_name</td>
					<td scope=\"col\">
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editVesselCargo$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i></span>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"vessel_details.php?edit=$msl_num&delVesselCargoCon=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a>
					</td>
				</tr>
			";
		}
	}

	function vesselsCnf($msl_num = 111){
		GLOBAL $db; $run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $importer = $row['importer'];$cnf = $row['cnf']; 
			$importer = allData('bins', $importer, 'name'); if ($cnf != 0) {
				$cnfName = allData('cnf', $cnf, 'name');
			}else{$cnfName = "";}
			echo "
				<tr>
					<td scope=\"col\">$importer</td>
					<td scope=\"col\">$cnfName</td>
					<td scope=\"col\">
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editVesselsCnf$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i> Edit</span>
						</a>
						<!--a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"vessel_details.php?edit=$msl_num&delVesselsCnf=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a-->
					</td>
				</tr>
			";
		}

		// $run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		// while ($row = mysqli_fetch_assoc($run)) {
		// 	$importer = $row['importer']; $cnf
		// }
	}




	function cargoAndConsigneeWise(){
		GLOBAL $db; 

		// $sql = ;
		$run = mysqli_query($db, "SELECT * FROM users WHERE office_position = 'Representative' ORDER BY id DESC "); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; //
			
			$representative = $row['id']; //
			$repnm = $row['name'];
			$status = $row['activation']; //
			$getNum = mysqli_query($db,"SELECT * FROM vessels WHERE representative = '$representative' ");
			$num = mysqli_num_rows($getNum);
			

			echo "
				<tr>
					<th scope=\"row\">$representative</th>
					<td>
						<img src=\"img/userimg/".allData('users', $id, 'image')."\" alt=\"...\" class=\"img-fluid rounded-circle\" width=\"40\">
					</td>
					<td>$repnm</td>
					<td>$num</td>

					<!-- SEARCH KEYS -->
					<td style=\"text-align:center;\">
						 frm dt
					</td>

					<td class= style=\"display:inline-block;\">
						to-dt
						<span style=\"display:none;\">
							$id
						</span>
					</td>
                </tr>
			";
			$repnm = "";
		}
	}


	function vesselDetails($msl_num){
		GLOBAL $db; $sql = "SELECT * FROM vessels WHERE msl_num = '$msl_num' ";
		$run = mysqli_query($db, $sql); 
		$row = mysqli_fetch_assoc($run);
		// $msl_num = $row['msl_num']; 
		$stevedore = $row['stevedore']; $stvdrnm = allData('stevedore', $stevedore, 'name');
		$received_by = $row['received_by']; 
		if ($received_by != 0) {
			$rcvrnm = allData('users', $received_by, 'name'); //err
		}else{$rcvrnm = "";}
		$sailed_by = $row['sailed_by']; 
		if ($sailed_by != 0) {
			$slrnm = allData('users', $sailed_by, 'name'); //err
		}else{$slrnm = "";}
		$representative = $row['representative']; $repnm = allData('users', $representative, 'name');
		// $remarks = $row['remarks'];

		$vessel_name = allDataUpdated('vessels', 'msl_num', $msl_num, 'vessel_name');
		$cargo_short_name = allDataUpdated('vessels', 'msl_num', $msl_num, 'cargo_short_name');
		$cargo_bl_name = allDataUpdated('vessels', 'msl_num', $msl_num, 'cargo_bl_name');
		$total_qty = allDataUpdated('vessels', 'msl_num', $msl_num, 'total_qty');
		$rcv_date = allDataUpdated('vessels', 'msl_num', $msl_num, 'rcv_date');
		$sailing_date = allDataUpdated('vessels', 'msl_num', $msl_num, 'sailing_date');
		$remarksId = allDataUpdated('vessels_remarks', 'msl_num', $msl_num, 'remarks');

		// check if remarksId returns any boolean value
		if(is_bool($remarksId)){$remarks = allData('remarks', $remarksId, 'name');}

		echo "
			<th>
				MSL: <span style=\"color: #FF6C6C;\">$msl_num</span><br/>
				VESSELS NAME: <span style=\"color: #FF6C6C;\">MV.$vessel_name</span> <br/>
				
				
		";
			// if consignee count is more then one then collaple option would be on
			$run1 = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
			$num = mysqli_num_rows($run1);
			if ($num > 1) {
				echo "
					<p data-bs-toggle=\"collapse\" href=\"#CONSIGNEE\" role=\"button\" aria-expanded=\"false\" aria-controls=\"CONSIGNEE\">
					CONSIGNEE: <span style=\"color: #FF6C6C;\">CLICK TO SEE</span>
					</p>	
					<div class=\"collapse\" id=\"CONSIGNEE\">
						<div class=\"card card-body\">
				";
				while ($row1 = mysqli_fetch_assoc($run1)) {
					$id = $row1['id']; $consigneeId = $row1['consignee']; $vesselId = $row1['msl_num'];
					$consigneeName = allDataUpdated("bins", "id", $consigneeId, "name"); 
					echo "
					<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
						<span style=\"color: #FF6C6C;\">$consigneeName</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"consigneeId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" name=\"delConsigneetovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					</form>
					<br/>
					";
				}
				echo"
					</div>
				</div>
				";
			}
			else{
				echo "
				<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
					CONSIGNEE: ";
				while ($row1 = mysqli_fetch_assoc($run1)) {
					$id = $row1['id']; $consigneeId = $row1['consignee']; $vesselId = $row1['msl_num'];
					$consigneeName = allDataUpdated("bins", "id", $consigneeId, "name"); 
					// echo $vesselId."<br/>";
					echo "
						<span style=\"color: #FF6C6C;\">$consigneeName</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"consigneeId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" name=\"delConsigneetovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					
					<br/>
					";
				}
				echo "</form>";
			}

			// if cnf count is more then one, collaple option would be on
			$run2 = mysqli_query($db, "SELECT * FROM vessels_cnf WHERE msl_num = '$msl_num' ");
			$num1 = mysqli_num_rows($run2);
			if ($num1 > 1) {
				echo "
					<p data-bs-toggle=\"collapse\" href=\"#CNF\" role=\"button\" aria-expanded=\"false\" aria-controls=\"CNF\">
					CNF: <span style=\"color: #FF6C6C;\">CLICK TO SEE</span>
					</p>	
					<div class=\"collapse\" id=\"CNF\">
						<div class=\"card card-body\">
				";
				while ($row2 = mysqli_fetch_assoc($run2)) {
					$id = $row2['id']; $cnfId = $row2['cnf']; $vesselId = $row2['msl_num'];
					$cnfName = allDataUpdated("cnf", "id", $cnfId, "name"); 
					// echo $cnfId."<br/>";
					echo "
					<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
						<span style=\"color: #FF6C6C;\">$cnfName</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"cnfId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" name=\"delCnftovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					</form>
					<br/>
					";
				}
				echo"
					</div>
				</div>
				";
			}
			else{
				echo "<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
					CNF: ";
				while ($row2 = mysqli_fetch_assoc($run2)) {
					$id = $row2['id']; $cnfId = $row2['cnf']; $vesselId = $row2['msl_num'];
					$cnfName = allDataUpdated("cnf", "id", $cnfId, "name"); 
				}if(empty($cnfName)){$cnfName = ""; $id = ""; $hide = "display:none";} else{$hide = "";}
				// echo $vesselId."<br/>";
				echo "
						<span style=\"color: #FF6C6C;\">$cnfName</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"cnfId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" style=\"$hide\" name=\"delCnftovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					</form>
				";
			}
			
		echo"
				
				STEVEDORE: <span style=\"color: #FF6C6C;\">$stvdrnm</span> <br/>
				CARGO AS PER BL: <span style=\"color: #FF6C6C;\">$cargo_bl_name</span><br/>
				CARGO QUANTITY: <span style=\"color: #FF6C6C;\">$total_qty MT</span><br/>
			</th>

			<th>
				ARRIVAL DATE: <span style=\"color: #FF6C6C;\">$rcv_date</span> <br/>
				SAILING DATE: <span style=\"color: #FF6C6C;\">$sailing_date</span> <br/>
				RECEIVED BY: <span style=\"color: #FF6C6C;\">$rcvrnm</span> <br/>
				SAILED BY: <span style=\"color: #FF6C6C;\">$slrnm</span> <br/>
				REPRESENTATIVE: <span style=\"color: #FF6C6C;\">$repnm</span> <br/>
				CARGO: <span style=\"color: #FF6C6C;\">$cargo_short_name</span>
				
		";
			// if remarks count is more then one then collaple option would be on
			$run1 = mysqli_query($db, "SELECT * FROM vessels_remarks WHERE msl_num = '$msl_num' ");
			$num = mysqli_num_rows($run1);
			if ($num > 1) {
				echo "
					<p data-bs-toggle=\"collapse\" href=\"#REMARKS\" role=\"button\" aria-expanded=\"false\" aria-controls=\"REMARKS\">
					REMARKS: <span style=\"color: #FF6C6C;\">CLICK TO SEE</span>
					</p>	
					<div class=\"collapse\" id=\"REMARKS\">
						<div class=\"card card-body\">
				";
				while ($row1 = mysqli_fetch_assoc($run1)) {
					$id = $row1['id']; $remarksId = $row1['remarks']; $vesselId = $row1['msl_num'];
					$remarks = allDataUpdated("remarks", "id", $remarksId, "name"); 
					echo "
					<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
						<span style=\"color: #FF6C6C;\">$remarks</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"remarksId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" name=\"delRemarkstovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					</form>
					<br/>
					";
				}
				echo"
					</div>
				</div>
				";
			}
			else{
				echo "
				<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
					REMARKS: ";
				while ($row1 = mysqli_fetch_assoc($run1)) {
					$id = $row1['id']; $remarksId = $row1['remarks']; $vesselId = $row1['msl_num'];
					$remarks = allDataUpdated("remarks", "id", $remarksId, "name"); 
					// echo $vesselId."<br/>";
					echo "
						<span style=\"color: #FF6C6C;\">$remarks</span>  &nbsp;&nbsp;&nbsp;&nbsp;
						<input type=\"hidden\" name=\"remarksId\" value=\"$id\" >
						<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
						<button type=\"submit\" name=\"delRemarkstovessel\" class=\"btn btn-danger btn-sm\">
							<i class=\"bi bi-trash\"></i>
						</button>
					
					<br/>
					";
				}
				echo "</form><br/>";
			}
				echo"
			</th>
		";
	}

	function vesselDetailsNew($msl_num){
		GLOBAL $db; $sql = "SELECT * FROM vessels WHERE msl_num = '$msl_num' ";
		// $id = allDataUpdated('vessels', 'msl_num', $msl_num, 'id');
		$run = mysqli_query($db, $sql); $row = mysqli_fetch_assoc($run);
		$id = $row['id']; $remarks = $row['remarks'];
		// $msl_num = $row['msl_num']; 
		$stevedore = $row['stevedore']; $stvdrnm = allData('stevedore', $stevedore, 'name');
		$received_by = $row['received_by']; 
		if ($received_by != 0) { $rcvrnm = allData('users', $received_by, 'name');}
		else{$rcvrnm = "";} $sailed_by = $row['sailed_by']; 
		if ($sailed_by != 0) { $slrnm = allData('users', $sailed_by, 'name');}
		else{$slrnm = "";} $representative = $row['representative']; $rotation = $row['rotation'];
		$repnm = allData('users', $representative, 'name');
		// $remarks = $row['remarks'];

		$vessel_name = allData('vessels', $id, 'vessel_name');

		$cargonm = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_key');
		if(!is_bool($cargonm)&&$cargonm!=0){$cargo_short_name=allData('cargokeys',$cargonm,'name');}
			else{$cargo_short_name="";}
		$cargo_bl_name = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_bl_name');
		$total_qty = gettotal('vessels_cargo', 'msl_num', $msl_num, 'quantity');

		$rcv_date = allData('vessels', $id, 'rcv_date');
		$sailing_date = allData('vessels', $id, 'sailing_date');
		$anchor = allData('vessels', $id, 'anchor');

		if (!empty($rcv_date)&&!empty($sailing_date)){$day=dayCount($rcv_date, $sailing_date);}
		else{$day = "";}
		// $remarksId = allData('vessels_remarks', 'msl_num', $msl_num, 'remarks');

		echo "
			<tr>
				<td>$msl_num</td>
				<td colspan=\"2\">$vessel_name</td>
				<td>$rotation</td>
				<td>$rcv_date</td>
				<td>$sailing_date</td>
				<!--td>$stvdrnm</td-->
				<td>$cargo_short_name</td>
				<td>$total_qty MT</td>
			</tr>
			<tr style=\"color: white; border: 1px solid white;\">
				<td colspan=\"4\">Cargo Bl Name</td>
				<td colspan=\"4\">Stevedore</td>
			</tr>
			<tr>
				<td colspan=\"4\">";
					$total = 0;
					$get = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE msl_num = '$msl_num' ");
						while ($got = mysqli_fetch_assoc($get)) {
							$cargo_bl_name = $got['cargo_bl_name']; $quantity = $got['quantity'];
							//$port_name = allData('loadport', $loadport, 'port_name');
							echo "$cargo_bl_name : $quantity MT</br>"; $total = $total + $quantity;
						}echo "Total: ".$total." MT";
				echo"
				</td>
				<td colspan=\"4\">$stvdrnm</td>
			</tr>
			<tr style=\"color: white; border: 1px solid white;\">
				<td>Anchor</td>
				<td colspan=\"2\">Load Port</td>
				<td>Representative</td>
				<td colspan=\"2\">Rcved By</td>
				<td>Sailed By</td>
				<td>Day</td>
			</tr>
			<tr>
				<td>$anchor</td>
				<td colspan=\"2\">
		";
			$get = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE msl_num = '$msl_num' ");
			while ($got = mysqli_fetch_assoc($get)) {
				$loadport = $got['loadport'];
				$port_name = allData('loadport', $loadport, 'port_name');
				echo "$port_name</br>";
			}
			echo"
			</td>
			<td>$repnm</td>
			<td colspan=\"2\">$rcvrnm</td>
			<td>$slrnm</td>
			<td>$day</td>
		</tr>

		<tr style=\"color: white; border: 1px solid white;\">
			<td colspan=\"4\">Consignee</td>
			<td colspan=\"4\">Cnf</td>
		</tr>";
		$getCon = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
		while ($gotCon = mysqli_fetch_assoc($getCon)) {
			$conId = $gotCon['importer']; $cnf = $gotCon['cnf'];
			$conName = allData('bins', $conId, 'name');
			if ($cnf != 0) { $cnfName = allData('cnf', $cnf, 'name'); }
			else{$cnfName = "";}
			echo"
				<tr>
					<td colspan=\"4\">$conName</td>
					<td colspan=\"4\">$cnfName</td>
				</tr>
			";
		}
		echo"
		<tr style=\"color: white; border: 1px solid white;\">
			<td colspan=\"8\">Remarks</td>
		</tr>";
		// $getRemarks = mysqli_query($db, "SELECT * FROM vessels_remarks WHERE msl_num = '$msl_num' ");
		// while ($gotRemarks = mysqli_fetch_assoc($getRemarks)) {
		// 	$remarksId = $gotRemarks['remarks']; $remarks = allData('remarks', $remarksId, 'name');
		// 	echo"
		// 		<tr>
		// 			<td colspan=\"8\">$remarks</td>
		// 		</tr>
		// 	";
		// }
		echo"
			<tr style=\"border: 1px solid white;\">
				<td colspan=\"8\">$remarks</td>
			</tr>
		";
	}

	function allConsignee($val = 'addBtn'){
		GLOBAL $db; $sql = "SELECT * FROM bins WHERE type = 'IMPORTER' ";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name']; $binnumber = $row['bin'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td><a href=\"3rd_parties.php?consigneeview=$id\">$name</a></td>
					<td>$binnumber</td>

					<td>
			";
			if ($val == "editBtn") {
				echo"
					<a 
						href=\"#\" 
						class=\"btn btn-outline-secondary\"
						data-toggle=\"modal\" data-target=\"#editConsignee$id\"
					>Edit</a>
					<a 
						onClick=\"javascript: return confirm('Please confirm deletion');\"
						href=\"3rd_parties.php?page=consignee&delConsignee=$id\" 
						class=\"btn btn-outline-danger\"
					>Delete</a>
				";
			}
			elseif($val == "addBtn"){
				if (isset($_GET['msl_num'])) {$msl_num = $_GET['msl_num'];}
				echo"
				<form method=\"post\" action=\"vessel_details.php?msl_num=$msl_num\">
					<input type=\"hidden\" name=\"consigneeId\" value=\"$id\" >
					<input type=\"hidden\" name=\"vesselId\" value=\"$msl_num\" >
					<button type=\"submit\" name=\"addConsigneetovessel\" class=\"btn btn-outline-success\">+ADD</button>
				</form>
				";
			}
			echo"
					</td>
				</tr>
			";
		}
	}

	function allCnf(){
		GLOBAL $db; $sql = "SELECT * FROM cnf";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name'];
			$contact = allDataUpdated("cnf_contacts", "company", $id, "name");
			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td><a href=\"3rd_parties.php?cnfview=$id\">$name</a></td>
					<td>
			
					<a href=\"#\" 
						class=\"btn btn-outline-secondary\"
						data-toggle=\"modal\" data-target=\"#editCnf$id\"
					>Edit</a>
					<a 
						onClick=\"javascript: return confirm('Please confirm deletion');\"
						href=\"3rd_parties.php?page=consignee&delCnf=$id\" 
						class=\"btn btn-outline-danger\"
					>Delete</a>
				
					<p style=\"display: none;\">$contact</p>
					</td>
				</tr>
			";
		}
	}


	function allUsers(){
		GLOBAL $db; $sql = "SELECT * FROM users WHERE activation != 'delete' ";
		$run = mysqli_query($db, $sql); while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name']; $email = $row['email'];
			$contact = $row['contact']; $office_position = $row['office_position'];
			$img = $row['image'];
			$activation = $row['activation']; if ($activation == "off") {
				$btnnm = "ON"; $btnval = "on"; $btnclr = "success";
			}else{$btnnm = "OFF"; $btnval = "off"; $btnclr = "warning";}

			echo "
				<tr>
					<th scope=\"row\">
						<img src=\"img/userimg/$img\" alt=\"...\" height=\"40\" style=\"border-radius: 50%;\">
					</th>
					<td>$name</td>
					<td>$email</td>
					<td>$contact</td>

					<td>
						<a onClick=\"javascript: return confirm('Please Activation / Deactivation');\"
							href=\"users.php?useraction=$btnval&userid=$id\" 
							class=\"btn btn-outline-$btnclr\"
						>$btnnm</a>

						<a onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"users.php?useraction=delete&userid=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
						<p style=\"display: none\">$office_position</p>
					</td>
				</tr>
			";
		}
	}


	// cnf contact persons
	function cnfContacts($cnf){
		GLOBAL $db; $sql = "SELECT * FROM cnf_contacts WHERE company = '$cnf' ";

		$run = mysqli_query($db, $sql); $num = 0;
		// $num = mysqli_num_rows($run);
		while ($row = mysqli_fetch_assoc($run)) { $num++;
			$id = $row['id']; $name = $row['name']; $contact = $row['contact'];
			$company = $row['company']; $company_name = allData('cnf', $company, 'name');
			echo "
				<tr>
					<th scope=\"row\">$num</th>
					<td>$name</td>
					<td>$contact</td>
					<td>
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editCnfContact$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i></span>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?cnfview=$cnf&delCnfContact=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a>
					</td>
				</tr>
			";
		}
	}



	// consignee contact persons
	function consigneeContacts($consignee){
		GLOBAL $db; $sql = "SELECT * FROM consignee_contacts WHERE company = '$consignee' ";
		$run = mysqli_query($db, $sql); $num = 0;
		// $num = mysqli_num_rows($run);
		while ($row = mysqli_fetch_assoc($run)) { $num++;
			$id = $row['id']; $name = $row['name']; $contact = $row['contact'];
			$company = $row['company']; $company_name = allData('consignee', $company, 'name');
			echo "
				<tr>
					<th scope=\"row\">$num</th>
					<td>$name</td>
					<td>$contact</td>
					<td>
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editConsigneeContact$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i></span>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?consigneeview=$consignee&delConsigneeContact=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a>
					</td>
				</tr>
			";
		}
	}



	// stevedore contact persons
	function stevedoreContacts($stevedore){
		GLOBAL $db; $sql = "SELECT * FROM stevedore_contacts WHERE company = '$stevedore' ";
		$run = mysqli_query($db, $sql); $num = 0;
		// $num = mysqli_num_rows($run);
		while ($row = mysqli_fetch_assoc($run)) { $num++;
			$id = $row['id']; $name = $row['name']; $contact = $row['contact'];
			$company = $row['company']; $company_name = allData('stevedore', $company, 'name');
			echo "
				<tr>
					<th scope=\"row\">$num</th>
					<td>$name</td>
					<td>$contact</td>
					<td>
						<a 
							href=\"#\" 
							style=\"text-decoration: none; padding: 5px;\"
							data-toggle=\"modal\" data-target=\"#editStevedoreContact$id\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-pencil\"></i></span>
						</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?stevedoreview=$stevedore&delStevedoreContact=$id\" 
							style=\"text-decoration: none; padding: 5px;\"
						>
							<span style=\"padding: 5px;\"><i class=\"bi bi-trash\"></i></span>
						</a>
					</td>
				</tr>
			";
		}
	}




	// Surveyors
	function allSurveyors(){
		GLOBAL $db; $sql = "SELECT * FROM surveyors";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $surveyor_name = $row['surveyor_name'];
			$contact_1 = $row['contact_1'];
			$contact_2 = $row['contact_2'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$surveyor_name</td>
					<td>$contact_1</td>
					<td>$contact_2</td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editSurveyor$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?page=surveyors&delSurveyor=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
					</td>
				</tr>
			";
		}
	}

	// stevedores
	function allStevedore(){
		GLOBAL $db; $sql = "SELECT * FROM stevedore";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td><a href=\"3rd_parties.php?stevedoreview=$id\">$name</a></td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editStevedore$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?page=consignee&delStevedore=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
					</td>
				</tr>
			";
		}
	}

	// stevedores
	function allAgent(){
		GLOBAL $db; $sql = "SELECT * FROM agent";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $company_name = $row['company_name']; $contact_person = $row['contact_person'];
			$contact_1 = $row['contact_1']; $contact_2 = $row['contact_2'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>
						<a href=\"3rd_parties.php?agentview=$id\" data-toggle=\"modal\" data-target=\"#editAgent$id\">
							$company_name
						</a>
					</td>
					<td>
						<a href=\"3rd_parties.php?agentview=$id\" data-toggle=\"modal\" data-target=\"#editAgent$id\">
							$contact_person
						</a>
					</td>
					<td>
						<a href=\"3rd_parties.php?agentview=$id\" data-toggle=\"modal\" data-target=\"#editAgent$id\">
							$contact_1
						</a>
					</td>
					<td>
						<a href=\"3rd_parties.php?agentview=$id\" data-toggle=\"modal\" data-target=\"#editAgent$id\">
							$contact_2
						</a>
					</td>
					<td>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?page=agents&delAgent=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
					</td>
				</tr>
			";
		}
	}


	function allBins(){
		GLOBAL $db; $sql = "SELECT * FROM bins";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $type = $row['type']; $name = $row['name']; $bin = $row['bin'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
			";
			if ($type == "IMPORTER") {
				echo "<td><a href=\"3rd_parties.php?consigneeview=$id\">$name</a></td>";
			}else{echo "<td>$name</td>";}
					
			echo"
					<td>$bin</td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editBankBin$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"index.php?del_bin=$id\" 
							class=\"btn btn-danger\"
						>Delete</a>
						<p style=\"display: none;\">$type</p>
					</td>
                </tr>
			";
		}
	}



	function cargoKeys(){
		GLOBAL $db; $sql = "SELECT * FROM cargokeys";
		$run = mysqli_query($db, $sql); $num = 0; $prevmsl = 0; $prevcrkey = 0; $countvsl = 0;
		while ($row = mysqli_fetch_assoc($run)) {
			$num++;
			$id = $row['id']; $name = $row['name']; 
			$countvslid = mysqli_num_rows(mysqli_query($db, "SELECT * FROM vessels_cargo WHERE cargo_key = '$id' "));

			// $countvslid = "countvsl".$id;
			// $run1 = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE cargo_key = '$id' ");
			// while ($row1 = mysqli_fetch_assoc($run1)) {
			// 	$msl_num = $row1['msl_num']; $crkey = $row1['cargo_key'];
			// 	if($msl_num == $prevmsl){
			// 		if($crkey == $prevcrkey){$countvsl++;}
			// 	}else{$countvsl++;}
			// 	$prevmsl = $msl_num; $prevcrkey = $crkey;
			// }
			// $countvslid = $countvsl;
			// $countvsl = 0;


			echo "
				<tr>
					<td>$num</td>
					<td>$name</td>
					<td>$countvslid</td>
					<th>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editCargoKey$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"others_adds.php?page=cargoKeys&del_CargoKey=$id\" 
							class=\"btn btn-danger\"
						>Delete</a>
					</th>
                </tr>
			";
		}
	}



	// databackups
	function databackups(){
		GLOBAL $db; $sql = "SELECT * FROM backups ORDER BY id DESC";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $file = $row['file']; $date = date('d-m-Y', strtotime($row['date']));

			echo "
				<tr>
					<td>$id</td>
					<td>$file</td>
					<td>$date</td>
					<th>
						<a 
							onClick=\"javascript: return confirm('Please confirm Data Restore');\" 
							href=\"databackups.php?restore_database=$id\" 
							class=\"btn btn-outline-secondary\"
						>Restore</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"databackups.php?delbackups=$id\" 
							class=\"btn btn-danger\"
						>Delete</a>
					</th>
                </tr>
			";
		}
	}



	function allRemarks(){
		GLOBAL $db; $sql = "SELECT * FROM remarks";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$name</td>
					<th>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editRemarks$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"index.php?delRemarks=$id\" 
							class=\"btn btn-danger\"
						>Delete</a>
					</th>
                </tr>
			";
		}
	}

	function binandimporter(){
		GLOBAL $db; $sql = "SELECT * FROM bins WHERE type = 'IMPORTER' ";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $name = $row['name']; $bin_num = $row['bin'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$name</td>
					<td>$bin_num</td>
                </tr>
			";
		}
	}


	function allLoadport(){
		GLOBAL $db; $sql = "SELECT * FROM loadport";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $port_name = $row['port_name']; $port_code = $row['port_code'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$port_name</td>
					<td>$port_code</td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editLoadport$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?page=loadport&delLoadport=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
					</td>
				</tr>
			";
		}
	}

	function allSurveycompany(){
		GLOBAL $db; $sql = "SELECT * FROM surveycompany";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $company_name = $row['company_name']; 
			$contact_person = $row['contact_person']; $contact_number = $row['contact_number'];

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$company_name</td>
					<td>$contact_person</td>
					<td>$contact_number</td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editSurveycompany$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\"
							href=\"3rd_parties.php?page=surveycompany&delSurveycompany=$id\" 
							class=\"btn btn-outline-danger\"
						>Delete</a>
					</td>
				</tr>
			";
		}
	}


	function allBonds(){
		GLOBAL $db; $sql = "SELECT * FROM prizebond";
		$run = mysqli_query($db, $sql); 
		while ($row = mysqli_fetch_assoc($run)) {
			$id = $row['id']; $ownerid = $row['owner']; $bond_num = $row['bond_num'];
			$owner = allData('users', $ownerid, 'name');$email = allData('users', $ownerid, 'email');

			echo "
				<tr>
					<th scope=\"row\">$id</th>
					<td>$owner</td>
					<td>$email</td>
					<td>$bond_num</td>
					<td>
						<a 
							href=\"#\" 
							class=\"btn btn-outline-secondary\"
							data-toggle=\"modal\" data-target=\"#editPrizeBond$id\"
						>Edit</a>
						<a 
							onClick=\"javascript: return confirm('Please confirm deletion');\" 
							href=\"prizebond.php?del_bond=$id\" 
							class=\"btn btn-danger\"
						>Delete</a>
					</td>
                </tr>
			";
		}
	}


	// get total vessels
	// function totalVessel($year = 2022){
	// 	GLOBAL $db; $syear = "01-01-".$year; $yyear = "31-12-".$year;
	// 	$run = mysqli_query($db, "SELECT * FROM vessels WHERE STR_TO_DATE(rcv_date, '%d-%m-%Y') BETWEEN '$syear' AND '$yyear' ");
	// 	// $dynamicsql .= "STR_TO_DATE(rcv_date, '%d-%m-%Y') BETWEEN '$syear' AND '$yyear'";
	// 	$output = mysqli_num_rows($run);
	// 	// $output = $year;
	// 	return $output;
	// }

	
	// forwading export functions

	// export Vessel details
	function export_vsl_details($msl_num = 205){
		GLOBAL $db; $filename = "";
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); 
        $vsl_imo = $row['vsl_imo'];
        $vsl_call_sign = $row['vsl_call_sign'];
        $vsl_mmsi_number = $row['vsl_mmsi_number'];
        $vsl_class = $row['vsl_class'];
        $vsl_nationality = $row['vsl_nationality'];
        $vsl_registry = $row['vsl_registry'];
        $vsl_official_number = $row['vsl_official_number'];
        $vsl_grt = formatIndianNumber($row['vsl_grt']);
        $vsl_nrt = formatIndianNumber($row['vsl_nrt']);
        $vsl_dead_weight = formatIndianNumber($row['vsl_dead_weight']);
        $vsl_breth = $row['vsl_breth'];
        $vsl_depth = $row['vsl_depth'];
        $vsl_loa = $row['vsl_loa'];
        $vsl_pni = $row['vsl_pni'];
        $vsl_owner_name = $row['vsl_owner_name'];
        $vsl_owner_address = $row['vsl_owner_address'];
        $vsl_owner_email = $row['vsl_owner_email'];
        $vsl_operator_name = $row['vsl_operator_name'];
        $vsl_operator_address = $row['vsl_operator_address'];
        $vsl_nature = $row['vsl_nature'];
        $vsl_cargo = $row['vsl_cargo'];
        $vsl_cargo_name = $row['vsl_cargo_name'];
        $shipper_name = $row['shipper_name'];
        $shipper_address = $row['shipper_address'];
        $last_port = $row['last_port'];
        $next_port = $row['next_port'];
        $with_retention = $row['with_retention'];
        $capt_name = $row['capt_name'];
        $number_of_crew = $row['number_of_crew'];


		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        
        // looking for missing forwading info is esixt

    	$exten = ".docx"; $filename = "SHIP Details";
    	$new_filename = $filename." ".$msl_num.".MV. ".$vessel;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/vessel_details/".$filename.$exten);
    	// $filename = $msl_num.".MV. ".$vessel."_29.INCOME TAX FORWARDING.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"vsl_imo" => "$vsl_imo",
				"vsl_call_sign" => "$vsl_call_sign",
				"vsl_mmsi_number" => "$vsl_mmsi_number",
				"vsl_class" => "$vsl_class",
				"vsl_nationality" => "$vsl_nationality",
				"vsl_registry" => "$vsl_registry",
				"vsl_official_number" => "$vsl_official_number",
				"vsl_grt" => "$vsl_grt",
				"vsl_nrt" => "$vsl_nrt",
				"vsl_dead_weight" => "$vsl_dead_weight",
				"vsl_breth" => "$vsl_breth",
				"vsl_depth" => "$vsl_depth",
				"vsl_loa"=>"$vsl_loa",
				"vsl_owner_name" => "$vsl_owner_name",
				"vsl_owner_address" => "$vsl_owner_address",
				"vsl_owner_email" => "$vsl_owner_email",
				"vsl_operator_name" => "$vsl_operator_name",
				"vsl_operator_address" => "$vsl_operator_address",
				"vsl_nature" => "$vsl_nature",
				"vsl_cargo_name"=>"$vsl_cargo_name",
				"shipper_name" => "$shipper_name",
				"shipper_address" => "$shipper_address",
				"last_port" => "$last_port",
				"capt_name" => "$capt_name",
				"number_of_crew" => "$number_of_crew"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$new_filename.$exten;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}

	// main file cover || file cover
	function mainfilecover($msl_num = 205){
		GLOBAL $db; $filename = ""; 
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; 


    	$exten = ".docx";$filename = "File Cover Format".$exten;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/file_cover_formats/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_13.FINAL ENTRY (4 COPY).docx";
    	$templateProcessor->setValues(["msl_num" => "$msl_num", "vessel" => "$vessel"]);
    	$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");   
	}

	// main file cover || file cover
	function accfilecover($msl_num = 205){
		GLOBAL $db; $filename = ""; 
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; 

    	$exten = ".docx";$filename = "ACCOUNTS FILE COVER PAGE".$exten;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/file_cover_formats/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_13.FINAL ENTRY (4 COPY).docx";
    	$templateProcessor->setValues(["msl_num" => "$msl_num", "vessel" => "$vessel"]);
    	$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");   
	}

	// finalentry || final entry
	function finalentryexport($msl_num = 205){
		GLOBAL $db; $filename = ""; 

        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $rotation = $row1['rotation'];
        $year = date("Y"); $month = date("m"); $day = date("d");
        if(empty($rotation)){$rotation = "2024/______";}

    	$exten = ".docx";$filename = "13.FINAL ENTRY (4 COPY)".$exten;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_13.FINAL ENTRY (4 COPY).docx";
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"rotation" => "$rotation",
				"year" => "$year",
				"month" => "$month"
			]
		);
    	$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");   
	}


	function pcforwadingexport($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ") ); $vsl_nrt = formatIndianNumber($row['vsl_nrt']); 
        $vsl_nationality = $row['vsl_nationality']; $with_retention = $row['with_retention'];
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/______";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "28.PC FORWARDING_NEW".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_28.PC FORWARDING_NEW.docx";
    	// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"rotation" => "$rotation",
				"vessel" => "$vessel",
				"vsl_nrt" => "$vsl_nrt",
				"vsl_nationality" => "$vsl_nationality",
				"with_retention" => "$with_retention",
				"year" => "$year",
				"month" => "$month"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");    
	}



	function pcstampexport($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ") ); $vsl_nrt = formatIndianNumber($row['vsl_nrt']); $with_retention = $row['with_retention'];
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/______";}
        $rotation_2 = substr($rotation,7)."/".substr($rotation,0,-7);
        if(empty($rotation)){$rotation = date("Y")."/"; $rotation_2 = "______/".date("Y");}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "28.Stamp_PC Undertaking to Customs_New".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_28.Stamp_PC Undertaking to Customs_New.docx";
    	// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"rotation" => "$rotation",
				"rotation_2" => "$rotation_2",
				"vessel" => "$vessel",
				"with_retention" => "$with_retention",
				"year" => "$year",
				"month" => "$month"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
        
	}

	function inctaxforwading($msl_num = 205){
		GLOBAL $db; $filename = "";
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ") ); $vsl_nrt = formatIndianNumber($row['vsl_nrt']); $with_retention = $row['with_retention'];

		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        $lstdaynextmonth = date('t',strtotime('next month'));
        $nextmonth = date('m', strtotime('+1 month', strtotime($row1['arrived'])));
        $inctxsaildate = $lstdaynextmonth.".".$nextmonth.".".$year;
        // looking for missing forwading info is esixt

    	$exten = ".docx";$filename = "29.INCOME TAX FORWARDING".$exten;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_29.INCOME TAX FORWARDING.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"arrived" => "$arrived",
				"inctxsaildate"=>"$inctxsaildate",
				"with_retention" => "$with_retention",
				"year" => "$year",
				"month" => "$month"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function mmdforwading($msl_num = 205){
		GLOBAL $db; $filename = "";

		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}

    	$exten = ".docx";$filename = "MMD FORWADING".$exten;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_29.INCOME TAX FORWARDING.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"arrived" => "$arrived",
				"year" => "$year",
				"month" => "$month",
				"day" => "$day"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$msl_num.".MV. ".$vessel." ".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function inctaxstamp($msl_num = 205){
		GLOBAL $db; $filename = "";
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ") ); $vsl_nrt = formatIndianNumber($row['vsl_nrt']); $with_retention = $row['with_retention'];

        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        $lstdaynextmonth = date('t',strtotime('next month'));
        $nextmonth = date('m', strtotime('+1 month', strtotime($row1['arrived'])));
        $inctxsaildate = $lstdaynextmonth.".".$nextmonth.".".$year;
        // looking for missing forwading info is esixt

	    $exten = ".docx";$filename = "29.Stamp_Income_TAX".$exten;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_arrive/".$filename);
	    // $filename = $msl_num.".MV. ".$vessel."_29.Stamp_Income_TAX.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"arrived" => "$arrived",
				"inctxsaildate"=>"$inctxsaildate",
				"with_retention" => "$with_retention",
				"year" => "$year",
				"month" => "$month"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function prepartique($msl_num = 205){
		GLOBAL $db; $filename = "";
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $last_port = $row['last_port']; $vsl_nationality = $row['vsl_nationality'];
        $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name'];

		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        
        // looking for missing forwading info is esixt

    	$exten = ".docx";$filename = "1.PREPARTIQUE PORT HEALTH".$exten;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_29.INCOME TAX FORWARDING.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"vsl_nationality" => "$vsl_nationality",
				"last_port" => "$last_port",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name"=>"$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function vsl_declearation($msl_num = 205){
		GLOBAL $db; $filename = "";
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $last_port = $row['last_port']; $vsl_nationality = $row['vsl_nationality'];
        $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name'];
        $vsl_grt = formatIndianNumber($row['vsl_grt']);
        $vsl_nrt = formatIndianNumber($row['vsl_nrt']); 
        $vsl_loa = $row['vsl_loa']; $vsl_imo = $row['vsl_imo']; 
        $vsl_pni = mysqli_real_escape_string($db, $row['vsl_pni']);
        $vsl_owner_name = $row['vsl_owner_name']; $shipper_name = $row['shipper_name'];


		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");

        
        // looking for missing forwading info is esixt

    	$exten = ".docx";$filename = "2.VSL DECLARATION TO DTM".$exten;
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_29.INCOME TAX FORWARDING.docx";
		
		// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"month" => "$month",
				"year" => "$year",
				"vsl_nationality" => "$vsl_nationality",
				"vsl_grt" => "$vsl_grt",
				"vsl_nrt" => "$vsl_nrt",
				"vsl_loa" => "$vsl_loa",
				"vsl_imo" => "$vsl_imo",
				"vsl_pni" => "$vsl_pni",
				"last_port" => "$last_port",
				"vsl_owner_name" => "$vsl_owner_name",
				"shipper_name" => "$shipper_name",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name"=>"$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function portigm($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); 
        $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name'];
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "3.PORT IGM WITH FORWARDING".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	// $filename = $msl_num.".MV. ".$vessel."_28.PC FORWARDING_NEW.docx";
    	// set pc forwading values
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name"=>"$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function plantq($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $last_port = $row['last_port'];
        $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name'];
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "4. PLANT QUARENTINE WITH (1 IGM+ANCHORE PER.)".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"last_port" => "$last_port",
				"rotation" => "$rotation",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name"=>"$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}



	function po_booking($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $vsl_cargo = $row['vsl_cargo']; 
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "5.P.O BOOKING TO CUSTOMS".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"vsl_cargo" => "$vsl_cargo"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function survey_booking($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name']; 
        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "6......NEW".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name" => "$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);


		$exten = ".docx";$filename = "6.APPILICATION OF SURVEYOR BOOKING".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"vsl_cargo" => "$vsl_cargo",
				"vsl_cargo_name" => "$vsl_cargo_name"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	// function survey_booking_bangla($msl_num = 205){
	// 	GLOBAL $db; $filename = ""; 
	// 	// get ship_perticular data
 //        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $vsl_cargo = $row['vsl_cargo']; $vsl_cargo_name = $row['vsl_cargo_name']; 
 //        // get vessel data
 //        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
 //        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
 //        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
 //        // looking for missing forwading info is esixt
		
 //    	$exten = ".docx";$filename = "6.APPILICATION OF SURVEYOR BOOKING".$exten;
 //    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/before_arrive/".$filename);
    	
 //    	$templateProcessor->setValues(
	// 		[
	// 			"msl_num" => "$msl_num",
	// 			"vessel" => "$vessel",
	// 			"year" => "$year",
	// 			"month" => "$month",
	// 			"rotation" => "$rotation",
	// 			"vsl_cargo" => "$vsl_cargo",
	// 			"vsl_cargo_name" => "$vsl_cargo_name"
	// 		]
	// 	); $pathToSave = "forwadings/auto_forwardings/".$filename;
	// 	$templateProcessor->saveAs($pathToSave);
	// 	header("location: vessel_details.php?ship_perticular=$msl_num");
	// }


	// after sail
	function port_health($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get ship_perticular data
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $vsl_cargo = $row['vsl_cargo']; $capt_name = $row['capt_name'];
        $vsl_nrt = formatIndianNumber($row['vsl_nrt']); $vsl_grt = $row['vsl_grt']; $vsl_imo = $row['vsl_imo'];
        $vsl_nationality = $row['vsl_nationality']; $vsl_registry = $row['vsl_registry'];
        $last_port = $row['last_port']; $next_port = $row['next_port'];
        $vsl_dead_weight = $row['vsl_dead_weight']; $number_of_crew = $row['number_of_crew'];
        $with_retention = $row['with_retention'];

        // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "20.PORT HEALTH".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_sail/".$filename);
    	
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"capt_name" => "$capt_name",
				"vsl_nrt" => "$vsl_nrt",
				"vsl_grt" => "$vsl_grt",
				"vsl_nationality" => "$vsl_nationality",
				"vsl_registry" => "$vsl_registry",
				"vsl_imo" => "$vsl_imo",
				"last_port" => "$last_port",
				"next_port" => "$next_port",
				"arrived" => "$arrived",
				"sailing_date" => "$sailing_date",
				"vsl_dead_weight" => "$vsl_dead_weight",
				"number_of_crew" => "$number_of_crew",
				"with_retention" => "$with_retention"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function psc_submission($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		 // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "21.PHC SUBMISSION LETTER".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_sail/".$filename);
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"sailing_date" => "$sailing_date"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function egm_forwading($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		$row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $with_retention = $row['with_retention'];

		 // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "22.EGM FOWARDING (NIL COPY-3)".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_sail/".$filename);
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"arrived" => "$arrived",
				"sailing_date" => "$sailing_date",
				"with_retention" => "$with_retention"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function egm_format($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		$row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $next_port = $row['next_port'];
		$with_retention = $row['with_retention']; $vsl_nationality = $row['vsl_nationality'];
		$vsl_grt = $row['vsl_grt']; $vsl_nrt = formatIndianNumber($row['vsl_nrt']); 
		$capt_name = $row['capt_name'];

		 // get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
        if(!empty($row1['rcv_date'])){$rcv_date = date('d.m.Y', strtotime($row1['rcv_date']));}
        else{$rcv_date = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}
        // looking for missing forwading info is esixt
		
    	$exten = ".docx";$filename = "23.EGM_format".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/after_sail/".$filename);
    	$templateProcessor->setValues(
			[
				"msl_num" => "$msl_num",
				"vessel" => "$vessel",
				"year" => "$year",
				"month" => "$month",
				"rotation" => "$rotation",
				"vsl_grt" => "$vsl_grt",
				"vsl_nrt" => "$vsl_nrt",
				"vsl_nationality" => "$vsl_nationality",
				"capt_name" => "$capt_name",
				"next_port" => "$next_port",
				"with_retention" => "$with_retention"
			]
		); $pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}

	function arrival_perticular($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
		
    	$exten = ".docx";$filename = "1-6.Arrival Particulars".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues(["vessel" => "$vessel"]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}

	function ship_required_docs($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
		
    	$exten = ".docx";$filename = "2-6.Ship Docs Required".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues(["vessel" => "$vessel"]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}

	function representative_letter($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rep_id = $row1['representative']; $rep_goodname = allData('users', $rep_id, 'goodname');
	    $rep_contact = allData('users', $rep_id, 'contact');
		
    	$exten = ".docx";$filename = "5-6.REPRESENTATIVE FORWARDING".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues([
    		"msl_num" => "$msl_num",
			"vessel" => "$vessel",
			"year" => "$year",
			"month" => "$month",
    		"rep_goodname" => "$rep_goodname",
    		"rep_contact" => "$rep_contact"
    	]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}




	function lightdues($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
		$row = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ")); $last_port = $row['last_port'];
		$capt_name = $row['capt_name']; $amount = formatIndianNumber($row['vsl_nrt']*10); 
		$amountinword=strtoupper(numberToWords($row['vsl_nrt']*10));$vsl_registry=$row['vsl_registry'];
		$vsl_nrt = formatIndianNumber($row['vsl_nrt']);

        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
        $rotation = $row1['rotation']; if(empty($rotation)){$rotation = "2024/_____";}
		
    	$exten = ".docx";$filename = "6-6.LIGHT DUES".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues([
    		"msl_num" => "$msl_num",
			"vessel" => "$vessel",
			"year" => "$year",
			"month" => "$month",
			"rotation" => "$rotation",
			"vsl_registry" => "$vsl_registry",
    		"capt_name" => "$capt_name",
    		"vsl_nrt" => "$vsl_nrt",
    		"last_port" => "$last_port",
    		"amount" => "$amount",
    		"amountinword" => "$amountinword"
    	]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}


	function watchman_letter($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
		
    	$exten = ".docx";$filename = "3-6.WATCHMAN FORWARDING".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues([
    		"msl_num" => "$msl_num",
			"vessel" => "$vessel",
			"year" => "$year",
			"month" => "$month"
    	]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}




	function vendor_letter($msl_num = 205){
		GLOBAL $db; $filename = ""; 
		// get vessel data
        $row1=mysqli_fetch_assoc(mysqli_query($db,"SELECT*FROM vessels WHERE msl_num='$msl_num'"));
        $vessel = $row1['vessel_name']; $year = date("Y"); $month = date("m"); $day = date("d");
		
    	$exten = ".docx";$filename = "4-6.SUPPLIER APPLICATION".$exten;
    	$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("forwadings/templets/receving_docs/".$filename);
    	$templateProcessor->setValues([
    		"msl_num" => "$msl_num",
			"vessel" => "$vessel",
			"year" => "$year",
			"month" => "$month"
    	]); 
		$pathToSave = "forwadings/auto_forwardings/".$filename;
		$templateProcessor->saveAs($pathToSave);
		header("location: vessel_details.php?ship_perticular=$msl_num");
	}
?>