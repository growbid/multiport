<?php
	// include all functions!
	if(!isset($_SESSION)) { session_start(); }
	include_once('functions.php'); include_once('infostore.php');
	$username = $email = $msg = ""; $errors = $success = array();

     // if the register button is clicked
	if(isset($_POST['register'])) {
		$username = mysqli_real_escape_string($db, $_POST['registerUsername']);
		$usergoodname = mysqli_real_escape_string($db, $_POST['registerUsergoodname']);
		$email = mysqli_real_escape_string($db, $_POST['registerEmail']);
		$contact = mysqli_real_escape_string($db, $_POST['registerContact']);
		$officePosition = mysqli_real_escape_string($db, $_POST['registerPosition']);
		$password_1 = mysqli_real_escape_string($db, $_POST['registerPassword1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['registerPassword2']);

		// ensure that form fields are filled peoperly
		if(empty($username)){  $msg = alertMsg('Username is required!', 'danger'); }
		elseif(empty($email)) {  $msg = alertMsg('Email is required!', 'danger'); }
		elseif (empty($contact)) { array_push($errors,"Contact shouldn't be empty!"); }
		elseif (empty($officePosition)) { array_push($errors,"Office Position shouldn't be empty!"); }
		elseif(empty($password_1)) { array_push($errors,"Password is required"); }
		elseif($password_1 != $password_2 || strlen($password_1) != strlen($password_2)) //to check if both given pass is same
		{  $msg = alertMsg('The two passwords do not match!', 'danger'); }

		//check if email already exist
		elseif(mysqli_num_rows(mysqli_query($db, "SELECT * FROM users WHERE email = '$email'"))>0){
			$msg =alertMsg('Email already registared!','danger');
		}
		else{
			// password check
			$p_len = 6; 
			// check email
			$e_len = 11; 
			if (strlen($password_1) < $p_len) {
				$msg = alertMsg('Password should be atleast 6 character long!', 'danger');
			}
			
			elseif(strlen($email) < $e_len || !preg_match("/@gmail.com/", $email) && !preg_match("/@email.com/", $email)  && !preg_match("/@yahoo.com/", $email)) {
				$msg = alertMsg('Invalid Email!', 'danger'); $email = "";
			} // if there are no errors, save user to database
			else { //encrypt password before storing
				$password = md5($password_1);
				$sql = "
					INSERT INTO users (name, goodname, image, email, password, contact, office_position, status, activation, registration_date)

				    VALUES('$username', '$usergoodname', 'user-1.jpg', '$email', '$password', '$contact', '$officePosition', 'online', 'off', NOW())
				";
				$data_input = mysqli_query($db, $sql);
				if ($data_input) {
					$_SESSION['email'] = $email;
					$id = lastData("users", "id");
					$_SESSION['id'] = $id;
					// input raw password to another database
					mysqli_query($db, "INSERT INTO passwords(owner, password)VALUES('$id', '$password_1')");
					header("location: index.php?user_id=$id"); //redirect to home page
				} else{ $msg = alertMsg("Couldn't insert data!", "danger"); }
			}
		}
	}



	//log user in from login page
	if (isset($_POST['login'])) {
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password = mysqli_real_escape_string($db, $_POST['password']);
		// ensure that form fields are filled peoperly
		if(empty($email)){array_push($errors,"email is required");} //add error to error array
		if(empty($password)){array_push($errors,"password is required");}

		else{	//check if email already exist
			$result_email = mysqli_query($db, "SELECT * FROM users WHERE email = '$email'");
			if (mysqli_num_rows($result_email) > 0) {
				$password = md5($password); //encrypt password before comparing with database
				$rck = mysqli_fetch_assoc($result_email); $svpassword = $rck['password'];
				if ($svpassword == $password) {
					// $row = mysqli_fetch_assoc($result);
					// check if user is enabled or disabled by admin
					if ($rck['activation'] == "off") {
						$msg = alertMsg("User Disabled, Please contact Admin!", "danger");
					}
					else{
				    	$_SESSION['id'] = $rck['id']; $_SESSION['email'] = $email;
				    	// update online status
				    	$sql_3 = "UPDATE users SET status = 'online' WHERE email = '$email' ";
						$run_3 = mysqli_query($db, $sql_3); header("location: index.php");
					}
				}else{ $msg = alertMsg("Wrong password!", "danger"); }
			} else{ $msg = alertMsg("Email dosen't exist, Please sign up!", "danger"); }
		}
	}

	//logout
	if (isset($_GET['logout'])) {
		$email = $_SESSION['email'];
		if (mysqli_query($db,"UPDATE users SET status = 'offline' WHERE email = '$email' ")) {
			session_destroy(); unset($_SESSION['username']);
			unset($_SESSION['email']); unset($_SESSION['id']);
			header('location: login.php');
		}
	}

	// ADD VESSEL VARIABLES
	// 1st
	$msl_num = $vessel_name = $cargo_short_name = $total_qty = $kutubdia_qty = $outer_qty = $retention_qty = $seventyeight_qty = $loadport = $importer = $stevedore = $representative = $cargo_bl_name = $rotation = $anchor = $arrived = $rcv_date = $sailing_date = $com_date = $fender_off = $survey_custom = $survey_consignee = $survey_supplier = $survey_owner = $survey_pni = $survey_chattrer = $received_by = $sailed_by = $remarks = $rcvbynm = ""; $slbynm = ""; $binnumber = ""; $query = "";
	//add vessel
	if (isset($_POST['addVassel'])) {
		// 1st
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']); // 1
		$vessel_name = strtoupper(mysqli_real_escape_string($db, $_POST['vessel_name'])); // 2
		$com_date = mysqli_real_escape_string($db, $_POST['com_date']); // 18
		$arrived = mysqli_real_escape_string($db, $_POST['arrived']); // 4
		$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']); // 4
		$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); // 5
		// 2nd
		$kutubdia_qty = mysqli_real_escape_string($db, $_POST['kutubdia_qty']); // 10
		$outer_qty = mysqli_real_escape_string($db, $_POST['outer_qty']); // 11
		$retention_qty = mysqli_real_escape_string($db, $_POST['retention_qty']); // 12
		$seventyeight_qty = mysqli_real_escape_string($db, $_POST['seventyeight_qty']); // 13
		// 3rd
		$stevedore = mysqli_real_escape_string($db, $_POST['stevedore']); // 9
		$representative = mysqli_real_escape_string($db, $_POST['representative']); // 19
		// 4th
		$rotation = mysqli_real_escape_string($db, $_POST['rotation']); // 3
		$anchor = mysqli_real_escape_string($db, $_POST['anchor']); // 18
		// 5yh
		// 6th
		$survey_custom = mysqli_real_escape_string($db, $_POST['survey_custom']); // 21
		$survey_consignee = mysqli_real_escape_string($db, $_POST['survey_consignee']); // 20
		$survey_supplier = mysqli_real_escape_string($db, $_POST['survey_supplier']); // 20
		// 7th
		$survey_owner = mysqli_real_escape_string($db, $_POST['survey_owner']); // 24
		$survey_pni = mysqli_real_escape_string($db, $_POST['survey_pni']); // 22
		$survey_chattrer = mysqli_real_escape_string($db, $_POST['survey_chattrer']); // 23
		// 8th
		$received_by = mysqli_real_escape_string($db, $_POST['received_by']); // 16
		$sailed_by = mysqli_real_escape_string($db, $_POST['sailed_by']); // 17
		// 9th
		$remarks = mysqli_real_escape_string($db, $_POST['remarks']); //list


		if (isset($_POST['sameRcv'])) { 
			if (isset($_POST['arrived']) && !empty($_POST['arrived'])) {
				$arrived=mysqli_real_escape_string($db, $_POST['arrived']);$rcv_date=$arrived;
			}elseif (isset($_POST['rcv_date']) && !empty($_POST['rcv_date'])) {
				$rcv_date=mysqli_real_escape_string($db,$_POST['rcv_date']);$arrived=$rcv_date;
			}
			else {
				$rcv_date=mysqli_real_escape_string($db,$_POST['rcv_date']);$arrived=$rcv_date;
			} 
		}
		else{
			$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']);
			$arrived = mysqli_real_escape_string($db, $_POST['arrived']);//done
		}

		if (isset($_POST['sameSail'])) { 
			if (isset($_POST['com_date']) && !empty($_POST['com_date'])) {
				$com_date = mysqli_real_escape_string($db, $_POST['com_date']); 
				$sailing_date = $com_date;
			}elseif (isset($_POST['sailing_date']) && !empty($_POST['sailing_date'])) {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			}
			else {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			} 
		}
		else{
			$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']);
			$com_date = mysqli_real_escape_string($db, $_POST['com_date']);//done
		}


		if (isset($_POST['importer'])) {
			$importer = $_POST['importer']; foreach ($importer as $key =>  $importerId) {
		    	// check if member already sinked
		    	$run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE importer = '$importerId' AND msl_num = '$msl_num' ");
		    	if (mysqli_num_rows($run) > 0 || $importerId == 0 ) { continue; }
		    	// now insert
		    	$sql = "INSERT INTO vessels_importer(msl_num, importer) VALUES('$msl_num', '$importerId')"; $run1 = mysqli_query($db, $sql);
		    }
		}

		if (empty($msl_num)) {$msg = alertMsg("Msl number is empty!", "danger");}
		//check if msl_num is already exist
		elseif (mysqli_num_rows(mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num'"))>0){$msg = alertMsg('This MSL number is already exist!','success');}

		// custom, consignee, owner, pni, chattrer
		// two party can't add same survey company unless it is selected "unknown". 35 is unknown id
		elseif (
			isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_consignee && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_owner && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_pni && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_chattrer && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_supplier && $survey_custom != 35 


			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_custom && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_owner && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_pni && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_chattrer && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_supplier && $survey_consignee != 35 


			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_consignee && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_custom && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_pni && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_chattrer && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_supplier && $survey_owner != 35 


			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_consignee && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_owner && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_custom && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_chattrer && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_supplier && $survey_pni != 35 


			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_consignee && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_owner && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_pni && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_custom && $survey_chattrer != 35
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_supplier && $survey_chattrer != 35


			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_consignee && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_owner && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_pni && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_custom && $survey_supplier != 35
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_chattrer && $survey_supplier != 35
		){$msg = alertMsg('One survey company can\'t do more then one survey at a time!','success');}
		else{
			$sql = "
				INSERT INTO vessels(msl_num, vessel_name, rotation, arrived, rcv_date, sailing_date, stevedore, kutubdia_qty, outer_qty, retention_qty, seventyeight_qty, com_date, fender_off, received_by, sailed_by, anchor, representative, survey_consignee, survey_custom, survey_supplier, survey_pni, survey_chattrer, survey_owner, remarks, status) 

				VALUES('$msl_num', '$vessel_name', '$rotation', '$arrived', '$rcv_date', '$sailing_date', '$stevedore', '$kutubdia_qty', '$outer_qty', '$retention_qty', '$seventyeight_qty', '$com_date', '', '$received_by', '$sailed_by', '$anchor', '$representative', '$survey_consignee', '$survey_custom', '$survey_supplier', '$survey_pni', '$survey_chattrer', '$survey_owner', '$remarks', '') 
			";
			$run = mysqli_query($db, $sql);
			if ($run) {
				$msl_num = lastData("vessels", "msl_num");
				mysqli_query($db, "INSERT INTO vessel_details(msl_num, with_retention) VALUES('$msl_num', 'IN-BALAST')");
				header("location: vessel_details.php?msl_num=$msl_num");
			}
			else{ $msg = alertMsg("Sorry, Something went wrong inserting data!", "danger"); }

			// by default, add custom and consignee to add surveyor
			$addsurveycompanysql = "
				INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) 
				VALUES('$msl_num', 'survey_custom', '$survey_custom', '', 'Load Draft'),
				('$msl_num', 'survey_custom', '$survey_custom', '', 'Light Draft'),
				('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Load Draft'),
				('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Light Draft')
			";
			$runsurveycompanysql = mysqli_query($db, $addsurveycompanysql);

			// if exist survey_supplier, add to add surveyor
			if (!empty($survey_supplier)) {
				// by default, add custom and consignee to add surveyor
				$addsurvey_suppliersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Load Draft'),
					('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Light Draft')";
				$runsurvey_suppliersql = mysqli_query($db, $addsurvey_suppliersql);
			}// if exist survey_owner, add to add surveyor
			if (!empty($survey_owner)) {
				// by default, add custom and consignee to add surveyor
				$addsurvey_ownersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_owner', '$survey_owner', '', 'Load Draft'),
					('$msl_num', 'survey_owner', '$survey_owner', '', 'Light Draft')";
				$runsurvey_ownersql = mysqli_query($db, $addsurvey_ownersql);
			}// if exist survey_pni, add to add surveyor
			if (!empty($survey_pni)) {
				// by default, add custom and consignee to add surveyor
				$addsurvey_pnisql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_pni', '$survey_pni', '', 'Load Draft'),
					('$msl_num', 'survey_pni', '$survey_pni', '', 'Light Draft')";
				$runsurvey_pnisql = mysqli_query($db, $addsurvey_pnisql);
			}// if exist survey_chattrer, add to add surveyor
			if (!empty($survey_chattrer)) {
				// by default, add custom and consignee to add surveyor
				$addsurvey_chattrersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Load Draft'),
					('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Light Draft')";
				$runsurvey_chattrersql = mysqli_query($db, $addsurvey_chattrersql);
			}
		}
	}







	// update vessel
	if (isset($_POST['vslUpdate'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);//done
		$vesselId = allDataUpdated('vessels', 'msl_num', $msl_num, 'id');
		$vessel_name = strtoupper(mysqli_real_escape_string($db, $_POST['vessel_name']));//done
		$kutubdia_qty = mysqli_real_escape_string($db, $_POST['kutubdia_qty']);//done
		$outer_qty = mysqli_real_escape_string($db, $_POST['outer_qty']);//done
		$retention_qty = mysqli_real_escape_string($db, $_POST['retention_qty']);//done
		$seventyeight_qty = mysqli_real_escape_string($db, $_POST['seventyeight_qty']);//done
		// impoter done
		// loadport done
		$stevedore = mysqli_real_escape_string($db, $_POST['stevedore']);//done
		$representative = mysqli_real_escape_string($db, $_POST['representative']);//done
		$rotation = mysqli_real_escape_string($db, $_POST['rotation']);//done
		$anchor = mysqli_real_escape_string($db, $_POST['anchor']);//done

		if (isset($_POST['sameRcv'])) { 
			if (isset($_POST['arrived']) && !empty($_POST['arrived'])) {
				$arrived = mysqli_real_escape_string($db, $_POST['arrived']); 
				$rcv_date = $arrived;
			}elseif (isset($_POST['rcv_date']) && !empty($_POST['rcv_date'])) {
				$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']); 
				$arrived = $rcv_date;
			}
			else {
				$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']); 
				$arrived = $rcv_date;
			} 
		}
		else{
			$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']);
			$arrived = mysqli_real_escape_string($db, $_POST['arrived']);//done
		}

		if (isset($_POST['sameSail'])) { 
			if (isset($_POST['com_date']) && !empty($_POST['com_date'])) {
				$com_date = mysqli_real_escape_string($db, $_POST['com_date']); 
				$sailing_date = $com_date;
			}elseif (isset($_POST['sailing_date']) && !empty($_POST['sailing_date'])) {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			}
			else {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			} 
		}
		else{
			$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']);
			$com_date = mysqli_real_escape_string($db, $_POST['com_date']);//done
		}
		$survey_custom = mysqli_real_escape_string($db, $_POST['survey_custom']);//done
		$survey_consignee = mysqli_real_escape_string($db, $_POST['survey_consignee']);//done
		$survey_owner = mysqli_real_escape_string($db, $_POST['survey_owner']);//done
		$survey_supplier = mysqli_real_escape_string($db, $_POST['survey_supplier']);//done
		$survey_pni = mysqli_real_escape_string($db, $_POST['survey_pni']);//done
		$survey_chattrer = mysqli_real_escape_string($db, $_POST['survey_chattrer']);//done
		$vsl_opa = mysqli_real_escape_string($db, $_POST['vsl_opa']);
		$received_by = mysqli_real_escape_string($db, $_POST['received_by']);//done
		$sailed_by = mysqli_real_escape_string($db, $_POST['sailed_by']);//done
		$remarks = mysqli_real_escape_string($db, $_POST['remarks']);//done

		// checkbox values
		$custom_visited=$qurentine_visited=$psc_visited=$multiple_lightdues=$crew_change=$has_grab=$fender=$fresh_water=$piloting=0;
		if(isset($_POST['custom_visited'])){$custom_visited = $_POST['custom_visited'];}
		if(isset($_POST['qurentine_visited'])){$qurentine_visited = $_POST['qurentine_visited'];}
		if(isset($_POST['psc_visited'])){$psc_visited = $_POST['psc_visited'];}
		if(isset($_POST['multiple_lightdues'])){$multiple_lightdues = $_POST['multiple_lightdues'];}
		if(isset($_POST['crew_change'])){$crew_change = $_POST['crew_change'];}
		if(isset($_POST['has_grab'])){$has_grab = $_POST['has_grab'];}
		if(isset($_POST['fender'])){$fender = $_POST['fender'];}
		if(isset($_POST['fresh_water'])){$fresh_water = $_POST['fresh_water'];}
		if(isset($_POST['piloting'])){$piloting = $_POST['piloting'];}

		
		
		
		// update vessels importer
		if (isset($_POST['importer'])) {
			$importer = $_POST['importer'];

			// Convert the importer list to a comma-separated string for SQL query
			$importerListString = "'" . implode("', '", $importer) . "'";
			// SQL query to delete importers not in the importer list
			$delsql = "DELETE FROM vessels_importer WHERE msl_num = '$msl_num' AND importer NOT IN ($importerListString)"; mysqli_query($db,$delsql);

			// indest vessels consignee
			foreach ($importer as $key =>  $importerId) {
		    	$importer_name = allData('bins', $importerId, 'name');

		    	// check if importer already sinked
		    	$run1 = mysqli_query($db, "SELECT * FROM vessels_importer WHERE importer = '$importerId' AND msl_num = '$msl_num' ");
		    	// skip if importer already exists
		    	if (mysqli_num_rows($run1) > 0 || $importerId == 0 ) { continue; }
		    	// now insert
		    	$sql = "
			    	INSERT INTO vessels_importer(msl_num, importer, cnf)
			    	VALUES('$msl_num', '$importerId', '')
		    	"; $run = mysqli_query($db, $sql);
		    }
		}else{mysqli_query($db, "DELETE FROM vessels_importer WHERE msl_num = '$msl_num' ");}

		// check if multiple survey_party choosen for more then one purpose
		if (
			isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_consignee && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_owner && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_pni && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_chattrer && $survey_custom != 35 
			|| isset($survey_custom) && $survey_custom != "" && $survey_custom == $survey_supplier && $survey_custom != 35 


			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_custom && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_owner && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_pni && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_chattrer && $survey_consignee != 35 
			|| isset($survey_consignee) && $survey_consignee != "" && $survey_consignee == $survey_supplier && $survey_consignee != 35 


			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_consignee && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_custom && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_pni && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_chattrer && $survey_owner != 35 
			|| isset($survey_owner) && $survey_owner != "" && $survey_owner == $survey_supplier && $survey_owner != 35 


			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_consignee && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_owner && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_custom && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_chattrer && $survey_pni != 35 
			|| isset($survey_pni) && $survey_pni != "" && $survey_pni == $survey_supplier && $survey_pni != 35 


			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_consignee && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_owner && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_pni && $survey_chattrer != 35 
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_custom && $survey_chattrer != 35
			|| isset($survey_chattrer) && $survey_chattrer != "" && $survey_chattrer == $survey_supplier && $survey_chattrer != 35


			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_consignee && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_owner && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_pni && $survey_supplier != 35 
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_custom && $survey_supplier != 35
			|| isset($survey_supplier) && $survey_supplier != "" && $survey_supplier == $survey_chattrer && $survey_supplier != 35
		) {
			$msg = alertMsg('One survey company can\'t do more then one survey at a time!','success');
		}
		else{
			$sql = "
				UPDATE vessels SET vessel_name = '$vessel_name', rotation = '$rotation', arrived = '$arrived', rcv_date = '$rcv_date', sailing_date = '$sailing_date', stevedore = '$stevedore', kutubdia_qty = '$kutubdia_qty', outer_qty = '$outer_qty', retention_qty = '$retention_qty', seventyeight_qty = '$seventyeight_qty', com_date = '$com_date', fender_off = '', received_by = '$received_by', sailed_by = '$sailed_by', anchor = '$anchor', representative = '$representative', survey_consignee = '$survey_consignee', survey_custom = '$survey_custom', survey_supplier = '$survey_supplier', survey_pni = '$survey_pni', survey_chattrer = '$survey_chattrer', survey_owner = '$survey_owner', vsl_opa = '$vsl_opa', custom_visited = '$custom_visited', qurentine_visited = '$qurentine_visited', psc_visited = '$psc_visited', multiple_lightdues = '$multiple_lightdues', crew_change = '$crew_change', has_grab = '$has_grab', fender = '$fender', fresh_water = '$fresh_water', piloting = '$piloting', remarks = '$remarks', status = '' WHERE msl_num = '$msl_num'
			";
			$run = mysqli_query($db, $sql); 
			if ($run) {
				$msg = alertMsg("Updated Successfully! Vsl: $vesselId, Msl: $msl_num", "success");

				// check and add to vessels_urveyor table if new survey company added
				if (!empty($survey_custom)) {
					// check if survey supplier not exists in vessels surviour table
					if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Load Draft' ")){
						$addsurvey_customsql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_custom', '$survey_custom', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_customsql);
					}
					// check if survey supplier not exists in vessels surviour table
					if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Light Draft' OR msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Rob' ")){
						$addsurvey_customsql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_custom', '$survey_custom', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_customsql);
					}
				}
				
				
				// check and add to vessels_urveyor table if new survey company added
				if (!empty($survey_consignee)) {
					// check if survey supplier not exists in vessels surviour table
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Load Draft' ")==0){
						$addsurvey_consigneesql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_consigneesql);
					}
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Light Draft' OR msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Rob' ")==0){
						$addsurvey_consigneesql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_consigneesql);
					}
				}
				

				
				// check and add to vessels_urveyor table if new survey company added
				if (!empty($survey_supplier)) {
					// check if survey supplier not exists in vessels surviour table
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose = 'Load Draft' ")==0){
						$addsurvey_suppliersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_suppliersql);
					}
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose = 'Light Draft' ")==0){
						$addsurvey_suppliersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_suppliersql);
					}
				}
				
					
				// check and add to vessels_urveyor table if new survey company added
				if (!empty($survey_owner)) {
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose = 'Load Draft' ")==0){
						$addsurvey_ownersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_owner', '$survey_owner', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_ownersql);
					}
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose = 'Light Draft' ")==0){
						$addsurvey_ownersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_owner', '$survey_owner', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_ownersql);
					}
				}

				// if exist survey_pni, add to add surveyor
				if (!empty($survey_pni)) {
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose = 'Load Draft' ")==0){
						// by default, add custom and consignee to add surveyor
						$addsurvey_pnisql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_pni', '$survey_pni', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_pnisql);
					}
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose = 'Light Draft' ")==0){
						// by default, add custom and consignee to add surveyor
						$addsurvey_pnisql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_pni', '$survey_pni', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_pnisql);
					}
				}
				// if exist survey_chattrer, add to add surveyor
				if (!empty($survey_chattrer)) {
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose = 'Load Draft' ")==0){
						// by default, add custom and consignee to add surveyor
						$addsurvey_chattrersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Load Draft')";
						mysqli_query($db, $addsurvey_chattrersql);
					}
					if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose = 'Light Draft' ")==0){
						// by default, add custom and consignee to add surveyor
						$addsurvey_chattrersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
							('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Light Draft')";
						mysqli_query($db, $addsurvey_chattrersql);
					}
				}

				// update vessels survey company
				// these values comes from infostore start
				$prevsurvey_custom = $vessel['survey_custom'];
				$prevsurvey_consignee = $vessel['survey_consignee'];
				$prevsurvey_supplier = $vessel['survey_supplier'];
				$prevsurvey_owner = $vessel['survey_owner'];
				$prevsurvey_pni = $vessel['survey_pni'];
				$prevsurvey_chattrer = $vessel['survey_chattrer'];
				// these values comes from infostore end

				$allparties = array("survey_custom","survey_consignee","survey_supplier","survey_owner","survey_pni","survey_chattrer");
				$prev = "prev"; 
				$prevmodifiedsurveycompanyId = array(); 
				$newmodifiedsurveycompanyId = array();
				$newmodifiedsurveyparty = array();

				// store the survey company if modified
				foreach ($allparties as $key => $survey_party) { //example: survey_custom
					// add prev word to all parties. example: prevsurvey_custom, prevsurvey_consignee etc.
					$previous_survey = "prev".$survey_party; //example: prevsurvey_custom
					// checks if prev survey party and submitted survey party is not name.
					if($$survey_party!=$$previous_survey){ //example: $survey_custom != $prevsurvey_custom
						// add prevsurvey_party id to this array
						// $prevmodifiedsurveycompanyId[]=allDataUpdated('vessels','msl_num',$msl_num,$survey_party);	
						$prevmodifiedsurveycompanyId[]=$vessel[$survey_party];
						// add submitted survey_party id to this array
						$newmodifiedsurveycompanyId[] = $$survey_party; // $custom_survey
						// add surmitted survey party
						$newmodifiedsurveyparty[] = $survey_party; //"custom_survey";
					}
				}



				// update survey company from vessels_surveyor table
				foreach ($prevmodifiedsurveycompanyId as $key => $companyId) {
					$newsurveycompanyId = $newmodifiedsurveycompanyId[$key];
					$modiviedsurveyparty = $newmodifiedsurveyparty[$key];

					$update = mysqli_query($db, "UPDATE vessels_surveyor SET survey_company = '$newsurveycompanyId' WHERE msl_num = '$msl_num' AND survey_company = '$companyId' AND survey_party = '$modiviedsurveyparty' ");
					if ($update) {
						$msg = alertMsg("Updated vessels_surveyor!", "success");
					}else{$msg = alertMsg("Something went wrong!", "danger");}
				}
			}
			else{$msg = alertMsg("Something went wrong, Couldn't update data!", "danger");}
		}
		// header("location: vessel_details.php?msl_num=$msl_num");
	}

	// delete vessel
	if (isset($_GET['del_msl_num'])) {
		$msl_num = $_GET['del_msl_num'];

		// delete vessel
		mysqli_query($db, "DELETE FROM vessels WHERE msl_num = '$msl_num' ");

		// delete all vessels related data
		mysqli_query($db, "DELETE FROM vessel_details WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_cargo WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_importer WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_loadport WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_remarks WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_stevedore WHERE msl_num = '$msl_num' ");
		mysqli_query($db, "DELETE FROM vessels_surveyor WHERE msl_num = '$msl_num' ");

		// redirect to homepage
		header('location: index.php');
	}





	// complete percentage
	if (isset($_POST['percentagecomplete'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']); // done
		$vesselId = $vessel['id']; // done
		$vessel_name = strtoupper(mysqli_real_escape_string($db, $_POST['vessel_name'])); 

		$kutubdia_qty = $vessel['kutubdia_qty']; 
		$retention_qty = $vessel['retention_qty'];
		
		if (isset($_POST['kutubdia_qty'])&&!empty($_POST['kutubdia_qty'])) { $kutubdia_qty = mysqli_real_escape_string($db, $_POST['kutubdia_qty']); }

		if (isset($_POST['retention_qty'])&&!empty($_POST['retention_qty'])) { $retention_qty = mysqli_real_escape_string($db, $_POST['retention_qty']); }
		
		if (!exist("vessels_importer","msl_num = ".$msl_num." AND importer != '0' ")) {
			// update vessels importer
			if (isset($_POST['importer'])) {
				$importer = $_POST['importer']; // done
				// Convert the importer list to a comma-separated string for SQL query
				$importerListString = "'" . implode("', '", $importer) . "'";
				// SQL query to delete importers not in the importer list
				$delsql = "DELETE FROM vessels_importer WHERE msl_num = '$msl_num' AND importer NOT IN ($importerListString)"; mysqli_query($db,$delsql);

				// indest vessels consignee
				foreach ($importer as $key =>  $importerId) {
			    	$importer_name = allData('bins', $importerId, 'name');
			    	// check if importer already sinked
			    	$run1 = mysqli_query($db, "SELECT * FROM vessels_importer WHERE importer = '$importerId' AND msl_num = '$msl_num' ");
			    	// skip if importer already exists
			    	if (mysqli_num_rows($run1) > 0 || $importerId == 0 ) { continue; }
			    	// now insert
			    	$sql = "INSERT INTO vessels_importer(msl_num, importer, cnf) VALUES('$msl_num', '$importerId', '')"; $run = mysqli_query($db, $sql);
			    }
			}else{mysqli_query($db, "DELETE FROM vessels_importer WHERE msl_num = '$msl_num' ");}
		}

		$outer_qty = $vessel['outer_qty']; $stevedore = $vessel['stevedore'];
		$representative = $vessel['representative']; $rotation = $vessel['rotation'];
		$anchor = $vessel['anchor']; $arrived = $vessel['arrived']; $rcv_date = $vessel['rcv_date']; $com_date = $vessel['com_date'];

		$sailing_date = $vessel['sailing_date']; $survey_custom = $vessel['survey_custom'];
		$survey_consignee = $vessel['survey_consignee']; $received_by = $vessel['received_by'];
		$sailed_by = $vessel['sailed_by'];

		if (isset($_POST['outer_qty'])) {
			$outer_qty = mysqli_real_escape_string($db, $_POST['outer_qty']);
		}if (isset($_POST['stevedore'])) {
			$stevedore = mysqli_real_escape_string($db, $_POST['stevedore']);
		}if (isset($_POST['representative'])) {
			$representative = mysqli_real_escape_string($db, $_POST['representative']);
		}if (isset($_POST['rotation'])) {
			$rotation = mysqli_real_escape_string($db, $_POST['rotation']);
		}if (isset($_POST['anchor'])) {
			$anchor = mysqli_real_escape_string($db, $_POST['anchor']);
		}if (isset($_POST['arrived'])) {
			$arrived = mysqli_real_escape_string($db, $_POST['arrived']);
		}if (isset($_POST['rcv_date'])) {
			$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']);
		}if (isset($_POST['com_date'])) {
			$com_date = mysqli_real_escape_string($db, $_POST['com_date']);
		}


		if (isset($_POST['sameRcv'])) { 
			if (isset($_POST['arrived']) && !empty($_POST['arrived'])) {
				$arrived = mysqli_real_escape_string($db, $_POST['arrived']); 
				$rcv_date = $arrived;
			}elseif (isset($_POST['rcv_date']) && !empty($_POST['rcv_date'])) {
				$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']); 
				$arrived = $rcv_date;
			}
			else {
				$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']); 
				$arrived = $rcv_date;
			} 
		}
		else{
			if (isset($_POST['rcv_date']) && !empty($_POST['rcv_date'])) {
				$rcv_date = mysqli_real_escape_string($db, $_POST['rcv_date']);
			}if (isset($_POST['arrived']) && !empty($_POST['arrived'])) {
				$arrived = mysqli_real_escape_string($db, $_POST['arrived']);//done
			}
		}

		if (isset($_POST['sameSail'])) { 
			if (isset($_POST['com_date']) && !empty($_POST['com_date'])) {
				$com_date = mysqli_real_escape_string($db, $_POST['com_date']); 
				$sailing_date = $com_date;
			}elseif (isset($_POST['sailing_date']) && !empty($_POST['sailing_date'])) {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			}
			else {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']); 
				$com_date = $sailing_date;
			} 
		}
		else{
			if (isset($_POST['sailing_date']) && !empty($_POST['sailing_date'])) {
				$sailing_date = mysqli_real_escape_string($db, $_POST['sailing_date']);
			}if (isset($_POST['com_date']) && !empty($_POST['com_date'])) {
				$com_date = mysqli_real_escape_string($db, $_POST['com_date']);//done
			}
		}


		if (isset($_POST['survey_custom'])) {
			$survey_custom = mysqli_real_escape_string($db, $_POST['survey_custom']);
		}if (isset($_POST['survey_consignee'])) {
			$survey_consignee = mysqli_real_escape_string($db, $_POST['survey_consignee']);
		}if (isset($_POST['received_by'])) {
			$received_by = mysqli_real_escape_string($db, $_POST['received_by']);
		}if (isset($_POST['sailed_by'])) {
			$sailed_by = mysqli_real_escape_string($db, $_POST['sailed_by']);
		}

		$sql = "
			UPDATE vessels SET rotation = '$rotation', arrived = '$arrived', rcv_date = '$rcv_date', com_date = '$com_date', sailing_date = '$sailing_date', stevedore = '$stevedore', retention_qty = '$retention_qty', kutubdia_qty = '$kutubdia_qty', outer_qty = '$outer_qty', received_by = '$received_by', sailed_by = '$sailed_by', anchor = '$anchor', representative = '$representative', survey_consignee = '$survey_consignee', survey_custom = '$survey_custom' WHERE msl_num = '$msl_num'
		";
		$run = mysqli_query($db, $sql);


		// add cargo section
		if (isset($_POST['loadport']) || isset($_POST['quantity']) || isset($_POST['cargokey']) || isset($_POST['cargo_bl_name'])) {
			$loadport = mysqli_real_escape_string($db, $_POST['loadport']); // done
			$quantity = mysqli_real_escape_string($db, $_POST['quantity']); // done
			$cargokey = mysqli_real_escape_string($db, $_POST['cargokey']); // done
			$cargo_bl_name = mysqli_real_escape_string($db, $_POST['cargo_bl_name']); // done
			
	    	if (empty($loadport) || empty($quantity) || empty($cargokey) || empty($cargo_bl_name)) { $msg = alertMsg('Some field is empty in cargo section--!','danger'); }
	    	else{
	    		$sql = "INSERT INTO vessels_cargo(msl_num, cargo_key, loadport, quantity, cargo_bl_name) VALUES('$msl_num', '$cargokey', '$loadport', '$quantity', '$cargo_bl_name')";
				$run = mysqli_query($db, $sql);if($run){$msg = alertMsg('Added Successfully!','success');}
				else{$msg = alertMsg('Something went wrong, Couldn\'t incert data!','danger');}
				// header('location: 3rd_parties.php?page=stevedore');	
	    	}
		}

    	// add survey table for vessels_surveyor
		// check and add to vessels_surveyor table if new survey company added
		if (!empty($survey_custom)) {
			// check if survey supplier not exists in vessels surviour table {Load}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Load Draft' ")){
				$addsurvey_customsql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_custom', '$survey_custom', '', 'Load Draft')";
				$runsurvey_customsql = mysqli_query($db, $addsurvey_customsql);
			}
			// check if survey supplier not exists in vessels surviour table {Light}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Light Draft' ") && !exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND survey_purpose = 'Rob' ")){
				$addsurvey_customsql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_custom', '$survey_custom', '', 'Light Draft')";
				$runsurvey_customsql = mysqli_query($db, $addsurvey_customsql);
			}
		}
		
		
		// check and add to vessels_urveyor table if new survey company added
		if (!empty($survey_consignee)) {
			// check if survey supplier not exists in vessels surviour table
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Load Draft' ")){
				$addsurvey_consigneesql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Load Draft')";
				$runsurvey_consigneesql = mysqli_query($db, $addsurvey_consigneesql);
			}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Light Draft' ") && !exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND survey_purpose = 'Rob' ")){
				$addsurvey_consigneesql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_consignee', '$survey_consignee', '', 'Light Draft')";
				$runsurvey_consigneesql = mysqli_query($db, $addsurvey_consigneesql);
			}
		}
		

		
		// check and add to vessels_urveyor table if new survey company added
		if (!empty($survey_supplier)) {
			// check if survey supplier not exists in vessels surviour table
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose = 'Load Draft' ")){
				$addsurvey_suppliersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Load Draft')";
				$runsurvey_suppliersql = mysqli_query($db, $addsurvey_suppliersql);
			}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND survey_purpose = 'Light Draft' ")){
				$addsurvey_suppliersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_supplier', '$survey_supplier', '', 'Light Draft')";
				$runsurvey_suppliersql = mysqli_query($db, $addsurvey_suppliersql);
			}
		}
		
			
		// check and add to vessels_urveyor table if new survey company added
		if (!empty($survey_owner)) {
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose = 'Load Draft' ")){
				$addsurvey_ownersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_owner', '$survey_owner', '', 'Load Draft')";
				$runsurvey_ownersql = mysqli_query($db, $addsurvey_ownersql);
			}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND survey_purpose = 'Light Draft' ")){
				$addsurvey_ownersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_owner', '$survey_owner', '', 'Light Draft')";
				$runsurvey_ownersql = mysqli_query($db, $addsurvey_ownersql);
			}
		}

		// if exist survey_pni, add to add surveyor
		if (!empty($survey_pni)) {
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose = 'Load Draft' ")){
				// by default, add custom and consignee to add surveyor
				$addsurvey_pnisql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_pni', '$survey_pni', '', 'Load Draft')";
				$runsurvey_pnisql = mysqli_query($db, $addsurvey_pnisql);
			}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND survey_purpose = 'Light Draft' ")){
				// by default, add custom and consignee to add surveyor
				$addsurvey_pnisql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_pni', '$survey_pni', '', 'Light Draft')";
				$runsurvey_pnisql = mysqli_query($db, $addsurvey_pnisql);
			}
		}
		// if exist survey_chattrer, add to add surveyor
		if (!empty($survey_chattrer)) {
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose = 'Load Draft' ")){
				// by default, add custom and consignee to add surveyor
				$addsurvey_chattrersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Load Draft')";
				$runsurvey_chattrersql = mysqli_query($db, $addsurvey_chattrersql);
			}
			if(!exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND survey_purpose = 'Light Draft' ")){
				// by default, add custom and consignee to add surveyor
				$addsurvey_chattrersql = "INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) VALUES
					('$msl_num', 'survey_chattrer', '$survey_chattrer', '', 'Light Draft')";
				$runsurvey_chattrersql = mysqli_query($db, $addsurvey_chattrersql);
			}
		}


		// add survey company
		// these values comes from infostore start
		$prevsurvey_custom = $vessel['survey_custom'];
		$prevsurvey_consignee = $vessel['survey_consignee'];
		$prevsurvey_supplier = $vessel['survey_supplier'];
		$prevsurvey_owner = $vessel['survey_owner'];
		$prevsurvey_pni = $vessel['survey_pni'];
		$prevsurvey_chattrer = $vessel['survey_chattrer'];
		// these values comes from infostore end

		$allparties = array("survey_custom","survey_consignee","survey_supplier","survey_owner","survey_pni","survey_chattrer");

		$prev = "prev"; 
		$prevmodifiedsurveycompanyId = array(); 
		$newmodifiedsurveycompanyId = array();
		$newmodifiedsurveyparty = array();

		// store the survey company if modified
		foreach ($allparties as $key => $survey_party) { //example: survey_custom
			// add prev word to all parties. example: prevsurvey_custom, prevsurvey_consignee etc.
			$previous_survey = "prev".$survey_party; //example: prevsurvey_custom
			// checks if prev survey party and submitted survey party is not name.
			if($$survey_party!=$$previous_survey){ //example: $survey_custom != $prevsurvey_custom
				// add prevsurvey_party id to this array
				// $prevmodifiedsurveycompanyId[]=allDataUpdated('vessels','msl_num',$msl_num,$survey_party);	
				$prevmodifiedsurveycompanyId[]=$vessel[$survey_party];
				// add submitted survey_party id to this array
				$newmodifiedsurveycompanyId[] = $$survey_party; // $custom_survey
				// add surmitted survey party
				$newmodifiedsurveyparty[] = $survey_party; //"custom_survey";
			}
		}



		// update survey company from vessels_surveyor table
		foreach ($prevmodifiedsurveycompanyId as $key => $companyId) {
			$newsurveycompanyId = $newmodifiedsurveycompanyId[$key];
			$modiviedsurveyparty = $newmodifiedsurveyparty[$key];

			$update = mysqli_query($db, "UPDATE vessels_surveyor SET survey_company = '$newsurveycompanyId' WHERE msl_num = '$msl_num' AND survey_company = '$companyId' AND survey_party = '$modiviedsurveyparty' ");
			if ($update) {
				$msg = alertMsg("Updated vessels_surveyor!", "success");
			}else{$msg = alertMsg("Something went wrong!", "danger");}
		}


		// add vessels surveyor
		if (isset($_POST['listsurveyor']) && $_POST['listsurveyor'] > 0) {
			$listsurveyor = mysqli_real_escape_string($db, $_POST['listsurveyor']);
			for ($s=1; $s <= $listsurveyor; $s++) { 
				$thisrowIdsurveyor = mysqli_real_escape_string($db, $_POST['thisrowIdsurveyor'.$s]);
				$prevSurveyor = allData('vessels_surveyor', $thisrowIdsurveyor, 'surveyor');
				$survey_partys = mysqli_real_escape_string($db, $_POST['party'.$s]);
				$surveyorId = mysqli_real_escape_string($db, $_POST['surveyorId'.$s]);
				$survey_company = $vessel[$survey_partys];
				$survey_purpose = mysqli_real_escape_string($db, $_POST['survey_purpose'.$s]);
				if (empty($thisrowIdsurveyor) || empty($survey_partys) || empty($surveyorId) || empty($survey_purpose)) {
					$msg = alertMsg('Some Fields are empty in surveyor section!','danger');
				}else{
					// check if same surveyor is working in another party on same load/light purpose
					// check: same surveyor, same purpose, same party
					$run_1 = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND surveyor = '$surveyorId' AND survey_purpose = '$survey_purpose' AND survey_party = '$survey_partys' ");

					// check if already added surveyor or survey purpose (load/light) in same company
					// check: same party, same purpose, different surveyor.
					$run = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND survey_party = '$survey_partys' AND survey_purpose = '$survey_purpose' AND id != '$thisrowIdsurveyor' ");
					if (mysqli_num_rows($run_1) > 0) {
						$delete = mysqli_query($db, "DELETE FROM vessels_surveyor WHERE msl_num = '$msl_num' AND surveyor = '$surveyorId' AND survey_purpose = '$survey_purpose' AND survey_party = '$survey_partys' ");

						if ($survey_purpose == "both") {
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId' WHERE survey_party = '$survey_partys' AND msl_num = '$msl_num' ");
							$msg = alertMsg('Running both if', 'success');		
						}
						else{
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowIdsurveyor' ");
							$msg = alertMsg('Running if', 'success');
						}
					}
					elseif (mysqli_num_rows($run) > 0) {
						$delete = mysqli_query($db, "DELETE FROM vessels_surveyor WHERE msl_num = '$msl_num' AND survey_party = '$survey_partys' AND survey_purpose = '$survey_purpose' AND id != '$thisrowIdsurveyor' ");

						if ($survey_purpose == "both") {
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId' WHERE survey_party = '$survey_partys' AND msl_num = '$msl_num' ");
							$msg = alertMsg('Running both elseif', 'success');		
						}
						else{
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowIdsurveyor' ");
							$msg = alertMsg('Running elseif', 'success');
						}
					}
					else{
						if ($survey_purpose == "both") {
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId' WHERE survey_party = '$survey_partys' AND msl_num = '$msl_num' ");
							$msg = alertMsg('Running both end', 'success');		
						}
						else{
							$update = mysqli_query($db, "UPDATE vessels_surveyor SET surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowIdsurveyor' ");
							$msg = alertMsg('Running else', 'success');
						}
					}
					//if ($update) {$msg = alertMsg('Percentage Surveyor Updated!', 'success');}
					//else{$msg = alertMsg('Couldn\'t update percentage Surveyor!', 'danger');}
				}
			}
		}



	    if (isset($_POST['listimporter']) && $_POST['listimporter'] > 0) {
	    	// update cnf
	    	$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
			$listimporter = mysqli_real_escape_string($db, $_POST['listimporter']);
			for ($i=0; $i <= $listimporter; $i++) { 
				if (isset($_POST['thisrowIdcnf'.$i]) && isset($_POST['importerId'.$i])) {
					$thisrowId = mysqli_real_escape_string($db, $_POST['thisrowIdcnf'.$i]);
					$importerId = mysqli_real_escape_string($db, $_POST['importerId'.$i]);
					$cnfId = mysqli_real_escape_string($db, $_POST['cnfId'.$i]);
					if (empty($thisrowId) || empty($importerId) || empty($cnfId)){}
					else{
						$update = mysqli_query($db, "UPDATE vessels_importer SET cnf = '$cnfId' WHERE importer = '$importerId' AND msl_num = '$msl_num' ");
						if ($update) { $msg = alertMsg('Cnf Updated Successfully!', 'success'); }
						else{$msg = alertMsg('Couldn\'t update percentage cnf!', 'danger');}
					}
				}
			}
	    }
	}






	if (isset($_POST['updateProfile'])) {
		$name = $email = $contact = $newpass = ""; $id = $_POST['updateProfile']; 
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$contact = mysqli_real_escape_string($db, $_POST['contact']);
		$oldpass = mysqli_real_escape_string($db, $_POST['oldpass']);
		$newpass = mysqli_real_escape_string($db, $_POST['newpass']);

		// updating basics
		$up = mysqli_query($db,"UPDATE users SET name='$name',email='$email',contact='$contact' WHERE id='$id' "); if ($up) {$msg = alertMsg("Profile Updated Successfully!", "success");}

		// updating password
		if (isset($newpass) && !empty($newpass)) { $newpass = md5($newpass);
			if (!empty($oldpass)) { $oldpass = md5($oldpass);
				$useroldpass = allData('users', $id, 'password'); if ($oldpass == $useroldpass) {
					$psup=mysqli_query($db, "UPDATE users SET password = '$newpass' WHERE id = '$id' ");
					if ($psup) {$msg = alertMsg("Password Updated Successfully!", "success");}
				}else{$msg = alertMsg("Old password Dose not match!: $newpass", "danger");}
			}else{$msg = alertMsg("Please Input Old password!", "danger");}
		}

		// image update
		if (!empty($_FILES['pp']['name'])) {
			$img = "MSL".round(microtime(true)*10).basename($_FILES['pp']['name']);
 			$target = "img/userimg/".$img; move_uploaded_file($_FILES['pp']['tmp_name'],$target);
 			mysqli_query($db, "UPDATE users SET image = '$img' WHERE id = '$id' ");
		}else{$msg = alertMsg("Couldn't Update Image!", "danger");}
	}


	//add cargokey
	if (isset($_POST['addCargoKey'])) {
		$cargoKey = strtolower(mysqli_real_escape_string($db, $_POST['cargoKey']));
		$check = mysqli_query($db, "SELECT * FROM cargokeys WHERE name = '$cargoKey' ");
		if (mysqli_num_rows($check)>0) {$msg = alertMsg("Key Already Exists", "danger");}
		else{
			$sql = "INSERT INTO cargokeys(name) VALUES('$cargoKey')";
			$run = mysqli_query($db, $sql); if($run){alertMsg("Key Added Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		}
	}

	//edit cargokey
	if (isset($_POST['editCargoKey'])) {
		$keyId = mysqli_real_escape_string($db, $_POST['keyId']);
		$cargoKey = strtolower(mysqli_real_escape_string($db, $_POST['cargoKey']));
		$check = mysqli_query($db, "SELECT * FROM cargokeys WHERE name = '$cargoKey' ");
		if (mysqli_num_rows($check)>0) {$msg = alertMsg("Key Already Exists", "danger");}
		else{
			$sql = "UPDATE cargokeys SET name = '$cargoKey' WHERE id = '$keyId' ";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Key Updated Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
			// header('location: 3rd_parties.php?page=consignee');	
		}
	}

	// delete cargokey
	if (isset($_GET['del_CargoKey'])) {
		$keyId = $_GET['del_CargoKey'];
		// delete cargo keys
		mysqli_query($db, "DELETE FROM cargokeys WHERE id = $keyId ");
		// delete cargokey related vessels
		mysqli_query($db, "DELETE FROM vessels_cargo WHERE cargo_key = '$keyId' ");
		header('location: others_adds.php?page=cargoKeys');
	}


	//add users
	if (isset($_POST['addUsers'])) {
		$contact = "";
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$office_position = mysqli_real_escape_string($db, $_POST['office_position']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$contact = mysqli_real_escape_string($db, $_POST['contact']);
		$password_1 = "000000"; $password = md5($password_1);
		$sql = "INSERT INTO users(name, image, email, password, contact, office_position, status, activation, registration_date) VALUES('$name', 'user-1.jpg', '$email', '$password', '$contact', '$office_position', 'offline', 'off', NOW())";
		$run=mysqli_query($db,$sql);if($run){$msg=alertMsg("User Added Successfully!","success");}
		else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
	}

	// user action || user acitvation
	if (isset($_GET['useraction'])) {
		$action = $_GET['useraction']; $id = $_GET['userid'];

		if($action=="on"){mysqli_query($db,"UPDATE users SET activation='on' WHERE id='$id' ");}
		if($action=="off"){mysqli_query($db,"UPDATE users SET activation='off' WHERE id='$id' ");}
		if($action=="delete"){mysqli_query($db,"UPDATE users SET activation='delete' WHERE id='$id' ");}
		header('location: users.php');
	}
	

	//add consignee
	if (isset($_POST['addConsignee'])) {
		$consigneeName = mysqli_real_escape_string($db, $_POST['consigneeName']);
		$binnumber = mysqli_real_escape_string($db, $_POST['binnumber']);
		$sql="INSERT INTO bins(name,type,bin)VALUES('$consigneeName','IMPORTER','$binnumber')";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Consignee Added Successfully!", "danger");}
		else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
	}

	//edit consignee
	if (isset($_POST['editConsignee'])) {
		$consigneeId = mysqli_real_escape_string($db, $_POST['consigneeId']);
		$consigneeName = mysqli_real_escape_string($db, $_POST['consigneeName']);
		$binnumber = mysqli_real_escape_string($db, $_POST['binnumber']);
		$sql="UPDATE bins SET name='$consigneeName',bin='$binnumber' WHERE id='$consigneeId' ";
		$run = mysqli_query($db, $sql); 
		if($run){ $msg = alertMsg("Consignee Updated Successfully!", "success"); } 
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
	}

	// delete consignee
	if (isset($_GET['delConsignee'])) {
		$delConsignee = $_GET['delConsignee'];
		$sql = "DELETE FROM bins WHERE id = $delConsignee ";
		if(mysqli_query($db,$sql)){$msg=alertMsg("Consignee Deleted Successfully!","success");}
	}

	//add cnf
	if (isset($_POST['addCnf'])) {
		$cnfName = mysqli_real_escape_string($db, $_POST['cnfName']);
		$sql = "INSERT INTO cnf(name) VALUES('$cnfName')";
		$run=mysqli_query($db,$sql);if($run){$msg=alertMsg("Cnf Added Successfully!","success");}
		else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
		// header('location: 3rd_parties.php?page=consignee');
	}

	//edit cnf
	if (isset($_POST['editCnf'])) {
		$cnfId = mysqli_real_escape_string($db, $_POST['cnfId']);
		$cnfName = mysqli_real_escape_string($db, $_POST['cnfName']);
		$sql = "UPDATE cnf SET name = '$cnfName' WHERE id = '$cnfId' ";
		if(mysqli_query($db, $sql)){ $msg = alertMsg("CNF Updated Successfully!", "success");}
		else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
	}

	// delete cnf
	if (isset($_GET['delCnf'])) {
		$delCnf = $_GET['delCnf'];
		$sql = "DELETE FROM cnf WHERE id = $delCnf ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("CNF Deleted Successfully!", "success");}
	}

	//add stevedore
	if (isset($_POST['addStevedore'])) {
		$stevedoreName = strtoupper(mysqli_real_escape_string($db, $_POST['stevedoreName']));
		$sql = "INSERT INTO stevedore(name) VALUES('$stevedoreName')";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Stevedore Added Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	//edit stevedore
	if (isset($_POST['editStevedore'])) {
		$stevedoreId = mysqli_real_escape_string($db, $_POST['stevedoreId']);
		$stevedoreName = strtoupper(mysqli_real_escape_string($db, $_POST['stevedoreName']));
		$sql = "UPDATE stevedore SET name = '$stevedoreName' WHERE id = '$stevedoreId' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Stevedore Updated Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	// delete stevedore
	if (isset($_GET['delStevedore'])) {
		$delStevedore = $_GET['delStevedore'];
		$sql = "DELETE FROM stevedore WHERE id = $delStevedore ";
		if(mysqli_query($db, $sql)){$msg=alertMsg("Deleted Successfully!", "success");}
		header('location: 3rd_parties.php?page=stevedore');
	}




	//add agent
	if (isset($_POST['addAgent'])) {
		$c_name = strtoupper(mysqli_real_escape_string($db, $_POST['company_name']));
		$contact_person = strtoupper(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_1 = mysqli_real_escape_string($db, $_POST['contact_1']);
		$contact_2 = mysqli_real_escape_string($db, $_POST['contact_2']);

		$check = mysqli_query($db, "SELECT * FROM agent WHERE company_name = '$c_name' ");

		if(!empty($c_name)&& mysqli_num_rows($check)>0){$msg=alertMsg('Already Exist!','danger');}
		else{
			$sql = "INSERT INTO agent(company_name, contact_person, contact_1, contact_2) 
			VALUES('$c_name','$contact_person', '$contact_1', '$contact_2')";
			if(mysqli_query($db, $sql)){ $msg = alertMsg("Agent Added Successfully!", "success"); }
			else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
		}
	}

	//edit agent
	if (isset($_POST['editAgent'])) {
		$agentId = mysqli_real_escape_string($db, $_POST['agentId']);
		$company_name = strtoupper(mysqli_real_escape_string($db, $_POST['company_name']));
		$contact_person = strtoupper(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_1 = mysqli_real_escape_string($db, $_POST['contact_1']);
		$contact_2 = mysqli_real_escape_string($db, $_POST['contact_2']);
		$sql = "UPDATE agent SET company_name = '$company_name', contact_person = '$contact_person', contact_1 = '$contact_1', contact_2 = '$contact_2' WHERE id = '$agentId' ";
		if(mysqli_query($db,$sql)){$msg=alertMsg("Agent Updated Successfully!","success");}
		else{$msg=alertMsg("Something went wrong, Couldn't incert data!","danger");}
	}

	// delete agent
	if (isset($_GET['delAgent'])) {
		$delAgent = $_GET['delAgent'];
		$sql = "DELETE FROM agent WHERE id = $delAgent ";
		if(mysqli_query($db, $sql)){$msg=alertMsg("Agent Deleted Successfully!","success");}
		header('location: 3rd_parties.php?page=agents');
	}


	//add surveyor
	if (isset($_POST['addSurveyor'])) {
		$surveyor_name = mysqli_real_escape_string($db, $_POST['surveyor_name']);
		$contact_1 = mysqli_real_escape_string($db, $_POST['contact_1']);
		$contact_2 = mysqli_real_escape_string($db, $_POST['contact_2']);
		$sql = "INSERT INTO surveyors(surveyor_name, contact_1, contact_2) VALUES('$surveyor_name', '$contact_1', '$contact_2')"; if(mysqli_query($db, $sql)){
			$msg = alertMsg("Surveyor Added Successfully!", "success");
		} else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
	}

	//edit surveyor
	if (isset($_POST['editSurveyor'])) {
		$surveyorId = mysqli_real_escape_string($db, $_POST['surveyorId']);
		$surveyor_name = mysqli_real_escape_string($db, $_POST['surveyor_name']);
		$contact_1 = mysqli_real_escape_string($db, $_POST['contact_1']);
		$contact_2 = mysqli_real_escape_string($db, $_POST['contact_2']);
		$sql = "UPDATE surveyors SET surveyor_name = '$surveyor_name', contact_1 = '$contact_1', contact_2 = '$contact_2' WHERE id = '$surveyorId' ";if(mysqli_query($db, $sql)){
			$msg = alertMsg("Surveyor Updated Successfully!", "success");
		} else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
	}

	// delete surveyor
	if (isset($_GET['delSurveyor'])) {
		$delSurveyor = $_GET['delSurveyor'];
		$sql = "DELETE FROM surveyors WHERE id = $delSurveyor ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Surveyor Deleted Successfully!", "danger");}
		header('location: 3rd_parties.php?page=surveyors');
	}



	//add cnf contact
	if (isset($_POST['addCnfContacts'])) {
		$company = mysqli_real_escape_string($db, $_POST['cnfcompanyId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check = mysqli_query($db, "SELECT * FROM cnf_contacts WHERE name = '$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "INSERT INTO cnf_contacts(company, name, contact, status) VALUES('$company', '$contact_person', '$contact_number', '')";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Added Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	//edit cnf contact
	if (isset($_POST['editCnfContact'])) {
		$rowId = mysqli_real_escape_string($db, $_POST['rowId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check = mysqli_query($db, "SELECT * FROM cnf_contacts WHERE name = '$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "UPDATE cnf_contacts SET name = '$contact_person', contact = '$contact_number' WHERE id = '$rowId' ";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Updated Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't Update data!", "danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	// delete cnf contact
	if (isset($_GET['delCnfContact'])) {
		$delCnfContact = $_GET['delCnfContact']; $cnfview = $_GET['cnfview'];
		mysqli_query($db, "DELETE FROM cnf_contacts WHERE id = $delCnfContact ");
		header("location: 3rd_parties.php?cnfview=$cnfview");
	}


	//add consignee contact
	if (isset($_POST['addConsigneeContacts'])) {
		$company = mysqli_real_escape_string($db, $_POST['consigneecompanyId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check=mysqli_query($db,"SELECT * FROM consignee_contacts WHERE name = '$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "INSERT INTO consignee_contacts(company, name, contact, status) VALUES('$company', '$contact_person', '$contact_number', '')";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Added Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	//edit consignee contact
	if (isset($_POST['editConsigneeContact'])) {
		$rowId = mysqli_real_escape_string($db, $_POST['rowId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check=mysqli_query($db, "SELECT * FROM consignee_contacts WHERE name='$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "UPDATE consignee_contacts SET name = '$contact_person', contact = '$contact_number' WHERE id = '$rowId' ";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Updated Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't Update data!", "danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	// delete consignee contact
	if (isset($_GET['delConsigneeContact'])) {
		$delConsigneeContact = $_GET['delConsigneeContact']; $consigneeview = $_GET['consigneeview'];
		mysqli_query($db, "DELETE FROM consignee_contacts WHERE id = $delConsigneeContact ");
		header("location: 3rd_parties.php?consigneeview=$consigneeview");
	}


	//add stevedore contact
	if (isset($_POST['addStevedoreContacts'])) {
		$company = mysqli_real_escape_string($db, $_POST['stevedorecompanyId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check=mysqli_query($db,"SELECT * FROM stevedore_contacts WHERE name = '$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "INSERT INTO stevedore_contacts(company, name, contact, status) VALUES('$company', '$contact_person', '$contact_number', '')";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Added Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	//edit stevedore contact
	if (isset($_POST['editStevedoreContact'])) {
		$rowId = mysqli_real_escape_string($db, $_POST['rowId']);
		$contact_person = strtolower(mysqli_real_escape_string($db, $_POST['contact_person']));
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$check=mysqli_query($db, "SELECT * FROM stevedore_contacts WHERE name='$contact_person' ");
		if (mysqli_num_rows($check) > 0) { $msg = alertMsg("Exist Already!", "danger"); }
		else{
			$sql = "UPDATE stevedore_contacts SET name = '$contact_person', contact = '$contact_number' WHERE id = '$rowId' ";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Updated Successfully!", "success");}
			else{$msg = alertMsg("Something went wrong, Couldn't Update data!", "danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
		}
	}

	// delete stevedore contact
	if (isset($_GET['delStevedoreContact'])) {
		$delStevedoreContact = $_GET['delStevedoreContact']; $stevedoreview = $_GET['stevedoreview'];
		mysqli_query($db, "DELETE FROM stevedore_contacts WHERE id = $delStevedoreContact ");
		header("location: 3rd_parties.php?stevedoreview=$stevedoreview");
	}


	//add loadport
	if (isset($_POST['addLoadport'])) {
		$port_name = mysqli_real_escape_string($db, $_POST['port_name']);
		$port_code = mysqli_real_escape_string($db, $_POST['port_code']);
		$sql = "INSERT INTO loadport(port_name, port_code) VALUES('$port_name', '$port_code')";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Port Added Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	//edit loadport
	if (isset($_POST['editLoadport'])) {
		$loadportId = mysqli_real_escape_string($db, $_POST['loadportId']);
		$port_name = mysqli_real_escape_string($db, $_POST['port_name']);
		$port_code = mysqli_real_escape_string($db, $_POST['port_code']);
		$sql = "UPDATE loadport SET port_name = '$port_name', port_code = '$port_code' WHERE id = '$loadportId' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Port Updated Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	// delete loadport
	if (isset($_GET['delLoadport'])) {
		$delLoadport = $_GET['delLoadport'];
		$sql = "DELETE FROM loadport WHERE id = $delLoadport ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Port Deleted Successfully!", "success");}
		header('location: 3rd_parties.php?page=loadport');
	}

	//add surveycompany
	if (isset($_POST['addSurveycompany'])) {
		$company_name = mysqli_real_escape_string($db, $_POST['company_name']);
		$contact_person = mysqli_real_escape_string($db, $_POST['contact_person']);
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$sql = "INSERT INTO surveycompany(company_name, contact_person, contact_number) VALUES('$company_name', '$contact_person', '$contact_number')";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Company Added Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}// header('location: 3rd_parties.php?page=stevedore');	
	}

	//edit surveycompany
	if (isset($_POST['editSurveycompany'])) {
		$surveycompanyId = mysqli_real_escape_string($db, $_POST['surveycompanyId']);
		$company_name = mysqli_real_escape_string($db, $_POST['company_name']);
		$contact_person = mysqli_real_escape_string($db, $_POST['contact_person']);
		$contact_number = mysqli_real_escape_string($db, $_POST['contact_number']);
		$sql = "UPDATE surveycompany SET company_name = '$company_name', contact_person = '$contact_person', contact_number = '$contact_number' WHERE id = '$surveycompanyId' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Company Updated Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	// delete surveycompany
	if (isset($_GET['delSurveycompany'])) {
		$delSurveycompany = $_GET['delSurveycompany'];
		$sql = "DELETE FROM surveycompany WHERE id = $delSurveycompany ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Company Deleted Successfully!", "success");}
		header('location: 3rd_parties.php?page=surveycompany');
	}



	//add consignee to vessel
	if (isset($_POST['addConsigneetovessel'])) {
		// $msl_num = $_GET['vesselId']; $cnf = $_GET['addCnftovessel'];
		$msl_num = mysqli_real_escape_string($db, $_POST['vesselId']);
		$consignee = mysqli_real_escape_string($db, $_POST['consigneeId']);
		$sql = "INSERT INTO vessels_consignee(msl_num, consignee) VALUES('$msl_num', '$consignee')";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Consignee Added Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: vessel_details.php?msl_num=$msl_num');	
	}

	//delete consignee to vessel
	if (isset($_POST['delConsigneetovessel'])) {
		$id = mysqli_real_escape_string($db, $_POST['consigneeId']);
		$sql = "DELETE FROM vessels_consignee WHERE id = '$id' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Consignee Added Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		// header('location: vessel_details.php?msl_num=$msl_num');	
	}


	if (isset($_POST['addVesselsCnf'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
		$importer = mysqli_real_escape_string($db, $_POST['importer']);
		$cnf = mysqli_real_escape_string($db, $_POST['cnfId']);
		$check = mysqli_query($db, "
			SELECT * FROM vessels_cnf WHERE msl_num = '$msl_num' AND importer = '$importer' 
		");
		if (mysqli_num_rows($check)>0) { $msg = alertMsg("Cnf already Exist here!", "danger");}
		else{
			$sql = "INSERT INTO vessels_cnf(msl_num, importer, cnf) VALUES('$msl_num', '$importer', '$cnf')";
			if(mysqli_query($db, $sql)){ $msg = alertMsg("CNF Added Successfully!", "success"); }
			else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
			// header('location: vessel_details.php?msl_num=$msl_num');	
		}
	}

	// update vessels_cnf
	if (isset($_POST['update_vessels_cnf'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
		$thisrowId = mysqli_real_escape_string($db, $_POST['thisrowId']);
		$cnfId = mysqli_real_escape_string($db, $_POST['cnfId']);

		if (isset($_POST['importers'])) {
			$importers = $_POST['importers'];
			// Convert the importer list to a comma-separated string for SQL query
			$importerListString = "'" . implode("', '", $importers) . "'";
			// SQL query to delete importers not in the importer list
			$delsql = "UPDATE vessels_importer SET cnf = 0 WHERE msl_num = '$msl_num' AND cnf = '$cnfId' AND importer NOT IN ($importerListString)"; mysqli_query($db,$delsql);

			// indest vessels consignee
			foreach ($importers as $key =>  $importerId) {
		    	$importer_name = allData('consignee', $importerId, 'name');
		    	$update = mysqli_query($db, "UPDATE vessels_importer SET cnf = '$cnfId' WHERE importer = '$importerId' AND msl_num = '$msl_num' ");
		    }
		}$msg = alertMsg('Cnf Updated Successfully!', 'success');
	}

	//delete cnf to vessel ** not completed yet
	if (isset($_GET['delVesselsCnf'])) {
		$id = $_GET['delVesselsCnf']; $msl_num = allData('vessels_cnf', $id, 'msl_num');
		$delete = mysqli_query($db, "DELETE FROM vessels_cnf WHERE id = '$id' "); 
		if($delete){$msg = alertMsg("CNF removed Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
		header("location: vessel_details.php?edit=$msl_num");
	}


	if (isset($_POST['addVesselsSurveyor'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['vesselId']);
		$vesselId = allDataUpdated('vessels', 'msl_num', $msl_num, 'id');
		$party = mysqli_real_escape_string($db, $_POST['party']);
		$survey_company = allData('vessels', $vesselId, $party);
		$surveyorId = mysqli_real_escape_string($db, $_POST['surveyorId']);
		$survey_purpose = mysqli_real_escape_string($db, $_POST['survey_purpose']);

		// check if same surveyor is working in another company on same load/light purpose
		// check same surveyor, same purpose (load/light), different company
		$run_1 = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND surveyor = '$surveyorId' AND survey_purpose = '$survey_purpose' AND survey_company != '$survey_company' ");

		// check if already added surveyor or survey purpose (load/light) in same company
		// check: same company, same purpose(load/light), whatever surveyor
		$run_2 = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND survey_company = '$survey_company' AND survey_purpose = '$survey_purpose' AND surveyor != 0 ");
		if (mysqli_num_rows($run_1) > 0) {
			// $exist_surveyor = allDataUpdated('vessels_surveyor');
			$msg = alertMsg("Surveyor already exist in other party! can't add as $party.", "danger");
		}
		elseif (mysqli_num_rows($run_2) > 0) {
			$msg = alertMsg("Surveyor already exist! can't add new.", "danger");
		}
		else{
			$sql = "
				INSERT INTO vessels_surveyor(msl_num, survey_party, survey_company, surveyor, survey_purpose) 
				VALUES('$msl_num', '$party', '$survey_company', '$surveyorId', '$survey_purpose')
			";
			if(mysqli_query($db,$sql)){$msg=alertMsg("Surveyor Added Successfully!","success");}
			else{ $msg = alertMsg("Something went wrong, Couldn't incert data!", "danger"); }
		} // header('location: vessel_details.php?msl_num=$msl_num');	
	}


	// update vessels_surviour
	if (isset($_POST['update_vessels_surveyor'])) {
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
		$thisrowId = mysqli_real_escape_string($db, $_POST['thisrowId']);
		$prevSurveyor = allData('vessels_surveyor', $thisrowId, 'surveyor');
		$survey_party = mysqli_real_escape_string($db, $_POST['party']);
		$surveyorId = mysqli_real_escape_string($db, $_POST['surveyorId']);
		$survey_company = allDataUpdated('vessels', 'msl_num', $msl_num, $survey_party);
		$survey_purpose = mysqli_real_escape_string($db, $_POST['survey_purpose']);

		// check if same surveyor is working in another party on same load/light purpose
		// check: same surveyor, same purpose, same party
		$run_1 = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND surveyor = '$surveyorId' AND survey_purpose = '$survey_purpose' AND survey_party = '$survey_party' ");

		// check if already added surveyor or survey purpose (load/light) in same company
		// check: same party, same purpose, different surveyor.
		$run = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num' AND survey_party = '$survey_party' AND survey_purpose = '$survey_purpose' AND id != '$thisrowId' ");
		if (mysqli_num_rows($run_1) > 0) {
			$delete = mysqli_query($db, "DELETE FROM vessels_surveyor WHERE msl_num = '$msl_num' AND surveyor = '$surveyorId' AND survey_purpose = '$survey_purpose' AND survey_party = '$survey_party' ");

			$update = mysqli_query($db, "UPDATE vessels_surveyor SET survey_party = '$survey_party', survey_company = '$survey_company', surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowId' ");
		}
		elseif (mysqli_num_rows($run) > 0) {
			$delete = mysqli_query($db, "DELETE FROM vessels_surveyor WHERE msl_num = '$msl_num' AND survey_party = '$survey_party' AND survey_purpose = '$survey_purpose' AND id != '$thisrowId' ");
			$update = mysqli_query($db, "UPDATE vessels_surveyor SET survey_party = '$survey_party', survey_company = '$survey_company', surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowId' ");
		}
		else{
			$update = mysqli_query($db, "UPDATE vessels_surveyor SET survey_party = '$survey_party', survey_company = '$survey_company', surveyor = '$surveyorId', survey_purpose = '$survey_purpose' WHERE id = '$thisrowId' ");
		}
	}
	// delete vessels surveyor
	if (isset($_GET['delVesselSurveyors'])) {
		$delVesselSurveyors = $_GET['delVesselSurveyors'];
		$run=mysqli_query($db,"DELETE FROM vessels_surveyor WHERE id=$delVesselSurveyors");
		if($run){$msg = alertMsg("Consignee Deleted Successfully!", "success"); }
		// header('location: vessel_details.php?edit=$delVesselSurveyors');
	}



	//add bins
	if (isset($_POST['addBankBin'])) {
		$bank_name = mysqli_real_escape_string($db, $_POST['bank_name']);
		$bank_type = mysqli_real_escape_string($db, $_POST['type']);
		$bin_num = mysqli_real_escape_string($db, $_POST['bin_num']);
		// check if member already sinked
    	$run1 = mysqli_query($db, "SELECT * FROM bins WHERE bin = '$bin_num' ");
    	if(empty($bank_type)){$msg = alertMsg("Please select bin type!", "danger");}
    	elseif(mysqli_num_rows($run1)>0){$msg=alertMsg("Bin Number Already Exist!", "danger");}
    	else{
    		$sql = "INSERT INTO bins(name, type, bin) VALUES('$bank_name', '$bank_type', '$bin_num')";
			if(mysqli_query($db, $sql)){$msg = alertMsg("Bin added successfully!", "success");}
			else{$msg = alertMsg("Please select bin type!", "danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
    	}
	}

	//edit bins
	if (isset($_POST['editBankBin'])) {
		$binId = mysqli_real_escape_string($db, $_POST['binId']);
		$bank_name = mysqli_real_escape_string($db, $_POST['bank_name']);
		$bank_type = mysqli_real_escape_string($db, $_POST['type']);
		$bin_num = mysqli_real_escape_string($db, $_POST['bin_num']);
		$sql = "UPDATE bins SET name = '$bank_name', bin = '$bin_num' WHERE id = '$binId' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("Updated Successfully!", "success");}
		else{$msg = alertMsg("Couldn't update data!", "danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	// delete bins
	if (isset($_GET['del_bin'])) {
		$del_bin = $_GET['del_bin'];
		$sql = "DELETE FROM bins WHERE id = $del_bin ";
		if(mysqli_query($db, $sql)){$msg=alertMsg("Deleted Successfully!","success");} 
		header('location: bin_numbers.php');
	}


	//add cargo
	if (isset($_POST['addCargoConsigneewise'])) {
		$vesselId = mysqli_real_escape_string($db, $_POST['vesselId']);
		$msl_num = allData("vessels", $vesselId, "msl_num");
		$loadport = mysqli_real_escape_string($db, $_POST['loadport']);
		$quantity = mysqli_real_escape_string($db, $_POST['quantity']);
		$cargokey = mysqli_real_escape_string($db, $_POST['cargokey']);
		$cargo_bl_name = mysqli_real_escape_string($db, $_POST['cargo_bl_name']);
		
    	if(empty($vesselId)||empty($loadport)||empty($quantity)||empty($cargokey)||empty($cargo_bl_name)){$msg=alertMsg("Some field is empty!","danger");}
    	else{
    		$sql = "INSERT INTO vessels_cargo(msl_num, cargo_key, loadport, quantity, cargo_bl_name) VALUES('$msl_num', '$cargokey', '$loadport', '$quantity', '$cargo_bl_name')";
			if(mysqli_query($db, $sql)){$msg=alertMsg("Added Successfully!","success");}
			else{$msg=alertMsg("Couldn't incert data!","danger");}
			// header('location: 3rd_parties.php?page=stevedore');	
    	}
	}

	//edit 
	if (isset($_POST['updateCargoConsigneewise'])) {
		$id = mysqli_real_escape_string($db, $_POST['id']);
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
		$loadport = mysqli_real_escape_string($db, $_POST['loadport']);
		$quantity = mysqli_real_escape_string($db, $_POST['quantity']);
		$cargokey = mysqli_real_escape_string($db, $_POST['cargokey']);
		$cargo_bl_name = mysqli_real_escape_string($db, $_POST['cargo_bl_name']);
		$sql = "UPDATE vessels_cargo SET cargo_key = '$cargokey', loadport = '$loadport', quantity = '$quantity', cargo_bl_name = '$cargo_bl_name' WHERE id = '$id' ";
		if(mysqli_query($db, $sql)){$msg=alertMsg("Updated successfully!","success");}
		else{$msg=alertMsg("Couldn't update data!","danger");}
		// header('location: 3rd_parties.php?page=stevedore');	
	}

	// delete 
	if (isset($_GET['delVesselCargoCon'])) {
		$del = $_GET['delVesselCargoCon']; $msl_num = $_GET['edit'];
		$run = mysqli_query($db, "DELETE FROM vessels_cargo WHERE id = $del ");
		if($run){$msg=alertMsg("Deleted successfully!","success");} 
		header("location: vessel_details.php?edit=$msl_num");
	}



	// destroy all data
	if (isset($_GET['destroy']) && $_GET['destroy'] == 'destroy') {
		// get a backup of database
		$dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host=localhost;dbname=multiport', 'root', '');
		$file = 'databasebackup'.date("Y-m-d-H-i-s").'.sql';
		$dump->start('inc/db_backups/'.$file);
		$sql = "INSERT INTO backups(file,date)VALUES('$file', NOW())";
		$run = mysqli_query($db, $sql);

		if ($run) {
			// Delete process
			$sql = "
				TRUNCATE TABLE `agent`;
				/*TRUNCATE TABLE `bins`; */
				TRUNCATE TABLE `cargokeys`;  
				TRUNCATE TABLE `cnf`;  
				TRUNCATE TABLE `cnf_contacts`; 
				TRUNCATE TABLE `consignee_contacts`; 
				TRUNCATE TABLE `loadport`; 
				TRUNCATE TABLE `stevedore`; 
				TRUNCATE TABLE `stevedore_contacts`; 
				TRUNCATE TABLE `surveycompany`; 
				TRUNCATE TABLE `surveyors`; 
				/*TRUNCATE TABLE `users`; */
				TRUNCATE TABLE `vessels`; 
				TRUNCATE TABLE `vessels_cargo`;  
				TRUNCATE TABLE `vessels_importer`; 
				TRUNCATE TABLE `vessels_surveyor`; 
			";
			if (mysqli_multi_query($db,$sql)) {
				$msg=alertMsg("Data Destroyed!","success");
				header('location: index.php');
			}else{$msg=alertMsg("Couldn't destroy any data!","danger");}
		} else{$msg=alertMsg("Couldn't Backup data!","danger");}
	}


	// Backup database
	if (isset($_POST['backup_database'])) {
		// get a backup of database
		$dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host=localhost;dbname=multiport', 'root', '');
		$file = 'databasebackup'.date("Y-m-d-H-i-s").'.sql';
		$dump->start('inc/db_backups/'.$file);
		// INSERT SQL FILE NAME TO DATABASE
		$sql = "INSERT INTO backups(file,date)VALUES('$file', NOW())";
		$run = mysqli_query($db, $sql);
	}

	// Restore Database
	if (isset($_POST['restore_database'])) {
		// Delete all table before restore database
		$sql = "
			TRUNCATE TABLE `agent`;
			TRUNCATE TABLE `bins`;
			TRUNCATE TABLE `cargokeys`;  
			TRUNCATE TABLE `cnf`;  
			TRUNCATE TABLE `cnf_contacts`; 
			TRUNCATE TABLE `consignee_contacts`; 
			TRUNCATE TABLE `loadport`;
			TRUNCATE TABLE `passwords`;
			TRUNCATE TABLE `stevedore`; 
			TRUNCATE TABLE `stevedore_contacts`; 
			TRUNCATE TABLE `surveycompany`; 
			TRUNCATE TABLE `surveyors`; 
			TRUNCATE TABLE `users`;
			TRUNCATE TABLE `vessels`; 
			TRUNCATE TABLE `vessels_cargo`;
			TRUNCATE TABLE `vessels_importer`;  
			TRUNCATE TABLE `vessels_surveyor`; 
			TRUNCATE TABLE `vessel_details`; 
		";
		if ($db->multi_query($sql) === TRUE) {
			// $file = "databasebackup2023-01-21-13-16-00.sql";
			$file = lastData('backups', 'file');
			$filePath = "inc/db_backups/$file";
			$response = restoreMysqlDB($filePath);
		}else{$msg=alertMsg("Couldn't Destroy database before restore!","danger");}
	}

	if (isset($_GET['restore_database'])) {
		// Delete all table before restore database
		// List of tables to drop
		$tables = [
		    'agent', 'bins', 'cargokeys', 'cnf', 'cnf_contacts', 'consignee_contacts',
		    'loadport', 'passwords', 'stevedore', 'stevedore_contacts', 'surveycompany',
		    'surveyors', 'users', 'vessels', 'vessels_cargo', 'vessels_importer',
		    'vessels_surveyor', 'vessel_details'
		];

		$error = '';

		// Drop each table
		foreach ($tables as $table) {
		    $dropQuery = "DROP TABLE IF EXISTS `$table`";
		    if (!mysqli_query($db, $dropQuery)) {
		        $error .= "Error dropping table `$table`: " . mysqli_error($db) . "\n";
		    }
		}

		// Check if there were errors in dropping tables
		if ($error) {
		    $response = array("type" => "error", "message" => $error);
		} else {
			// $file = "databasebackup2024-07-24-17-30-25.sql";
			$id = $_GET['restore_database'];
		    $file = allData("backups",$id,"file");
		    $filePath = "inc/db_backups/$file";
		    if (restoreMysqlDB($filePath)) {
		    	$msg = alertMsg("Restored Successfully", "success");
		    }
		}
		// Output response
		// echo json_encode($response);

	}
	// Delete Backups
	if (isset($_GET['delbackups'])) {
		$id = $_GET['delbackups'];
		$file = allData('backups', $id, 'file'); $filePath = "inc/db_backups/$file";
		if (unlink($filePath) && mysqli_query($db, "DELETE FROM backups WHERE id = '$id' ")) {
			$msg = alertMsg("Data Deleted Successfully!", "success");
		}
	}


	// filter process
	if (isset($_POST['filtervsl'])) {
		// set all the variables to empty
		$fltrfrom = $fltrto = $fltrrepresentative = $fltrimporter = $fltrbank = $fltrcargo = $fltrstevedore = $dates = $fltrportcode = $fltrsurveyor = $fltrsurveycompany = $fltrseventyeight = $fltrkutubdia = $fltrouter = $fltrcustom = $fltrqurentine = $fltrlightdues = $fltrcrew = $fltrgrab = $fltrfender = $fltrwater = $fltrpiloting = $fltrpsc = $fltropa = "";

		// get all the variables from form
		$fltrrepresentative = mysqli_real_escape_string($db, $_POST['representative']);
		if (isset($_POST['importer'])) {$fltrimporter = $_POST['importer'];}
		if (isset($_POST['bank'])) {$fltrbank = $_POST['bank'];}
		if (isset($_POST['loadport'])) {$fltrportcode = $_POST['loadport'];}
		if (isset($_POST['cargo'])) {$fltrcargo = $_POST['cargo'];}
		if (isset($_POST['seventyeight'])) {$fltrseventyeight = $_POST['seventyeight'];}

		// checkbox start
		if (isset($_POST['kutubdia'])) {$fltrkutubdia = $_POST['kutubdia'];}
		if (isset($_POST['outer'])) {$fltrouter = $_POST['outer'];}
		if (isset($_POST['custom_visited'])) {$fltrcustom = $_POST['custom_visited'];}
		if (isset($_POST['qurentine_visited'])) {$fltrqurentine = $_POST['qurentine_visited'];}
		if (isset($_POST['psc_visited'])) {$fltrpsc = $_POST['psc_visited'];}
		if (isset($_POST['multiple_lightdues'])) {$fltrlightdues = $_POST['multiple_lightdues'];}
		if (isset($_POST['crew_change'])) {$fltrcrew = $_POST['crew_change'];}
		if (isset($_POST['has_grab'])) {$fltrgrab = $_POST['has_grab'];}
		if (isset($_POST['fender'])) {$fltrfender = $_POST['fender'];}
		if (isset($_POST['fresh_water'])) {$fltrwater = $_POST['fresh_water'];}
		if (isset($_POST['piloting'])) {$fltrpiloting = $_POST['piloting'];}
		// checkbox end

		$fltrstevedore = mysqli_real_escape_string($db, $_POST['stevedore']);
		$fltropa = mysqli_real_escape_string($db, $_POST['vsl_opa']);
		$fltrcnf = mysqli_real_escape_string($db, $_POST['cnf']);
		$fltrsurveyor = mysqli_real_escape_string($db, $_POST['surveyors']);
		$fltrsurveycompany = mysqli_real_escape_string($db, $_POST['surveycompanies']);
		if (isset($_POST['frm_date']) && isset($_POST['to_date'])) {
			$frm_date = mysqli_real_escape_string($db, $_POST['frm_date']);
			$to_date = mysqli_real_escape_string($db, $_POST['to_date']);
			$dates =  explode(",", $frm_date.",".$to_date);
		}

		// set the array tor filter
		$query = array(
			'fltrrepresentative' => $fltrrepresentative,
			'fltrimporter' => $fltrimporter,
			'fltrbank' => $fltrbank,
			'fltrportcode' => $fltrportcode,
			'fltrcargo' => $fltrcargo,
			'fltrseventyeight' => $fltrseventyeight,

			'fltrkutubdia' => $fltrkutubdia,
			'fltrouter' => $fltrouter,
			'fltrcustom' => $fltrcustom,
			'fltrqurentine' => $fltrqurentine,
			'fltrpsc' => $fltrpsc,
			'fltrlightdues' => $fltrlightdues,
			'fltrcrew' => $fltrcrew,
			'fltrgrab' => $fltrgrab,
			'fltrfender' => $fltrfender,
			'fltrwater' => $fltrwater,
			'fltrpiloting' => $fltrpiloting,

			'fltrstevedore' => $fltrstevedore,
			'fltropa' => $fltropa,
			'fltrcnf' => $fltrcnf,
			'fltrsurveyor' => $fltrsurveyor,
			'fltrsurveycompany' => $fltrsurveycompany,
			'dates' => $dates
		); // now the queries passes through "allvessels($key, $query)" function in index page
	}


	//update vessel_details || ship_perticular
	if (isset($_POST['ship_perticular_update'])) {

		$vsl_imo = $vsl_call_sign = $vsl_mmsi_number = $vsl_class = $vsl_nationality = 
		$vsl_registry = $vsl_official_number = $vsl_nrt = $vsl_grt = $vsl_dead_weight = 
		$vsl_breth = $vsl_depth = $vsl_loa = $vsl_pni = $vsl_owner_name = $vsl_owner_address = 
		$vsl_owner_email = $vsl_operator_name = $vsl_operator_address = $vsl_cargo_name = 
		$vsl_cargo = $shipper_name = $shipper_address = $last_port = $capt_name = 
		$number_of_crew = ""; 

		$ship_perticularId = mysqli_real_escape_string($db, $_POST['ship_perticularId']);
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);
		$vsl_imo = mysqli_real_escape_string($db, $_POST['vsl_imo']);
        $vsl_call_sign = mysqli_real_escape_string($db, $_POST['vsl_call_sign']);
        $vsl_mmsi_number = mysqli_real_escape_string($db, $_POST['vsl_mmsi_number']);
        $vsl_class = mysqli_real_escape_string($db, $_POST['vsl_class']);
        $vsl_nationality = mysqli_real_escape_string($db, $_POST['vsl_nationality']);
        $vsl_registry = mysqli_real_escape_string($db, $_POST['vsl_registry']);
        $vsl_official_number = mysqli_real_escape_string($db, $_POST['vsl_official_number']);
        $vsl_nrt = mysqli_real_escape_string($db, $_POST['vsl_nrt']);
        $vsl_grt = mysqli_real_escape_string($db, $_POST['vsl_grt']);
        $vsl_dead_weight = mysqli_real_escape_string($db, $_POST['vsl_dead_weight']);
        $vsl_breth = mysqli_real_escape_string($db, $_POST['vsl_breth']);
        $vsl_depth = mysqli_real_escape_string($db, $_POST['vsl_depth']);
        $vsl_loa = mysqli_real_escape_string($db, $_POST['vsl_loa']);
        $vsl_pni = mysqli_real_escape_string($db, $_POST['vsl_pni']);
        $vsl_owner_name = mysqli_real_escape_string($db, $_POST['vsl_owner_name']);
        $vsl_owner_address = mysqli_real_escape_string($db, $_POST['vsl_owner_address']);
        $vsl_owner_email = mysqli_real_escape_string($db, $_POST['vsl_owner_email']);
        $vsl_operator_name = mysqli_real_escape_string($db, $_POST['vsl_operator_name']);
        $vsl_operator_address = mysqli_real_escape_string($db, $_POST['vsl_operator_address']);
        $vsl_nature = mysqli_real_escape_string($db, $_POST['vsl_nature']);
        $vsl_cargo = mysqli_real_escape_string($db, $_POST['vsl_cargo']);
        $vsl_cargo_name = mysqli_real_escape_string($db, $_POST['vsl_cargo_name']);
        $shipper_name = mysqli_real_escape_string($db, $_POST['shipper_name']);
        $shipper_address = mysqli_real_escape_string($db, $_POST['shipper_address']);
        $last_port = mysqli_real_escape_string($db, $_POST['last_port']);
        $next_port = mysqli_real_escape_string($db, $_POST['next_port']);
        $with_retention = mysqli_real_escape_string($db, $_POST['with_retention']);
        $capt_name = mysqli_real_escape_string($db, $_POST['capt_name']);
        $number_of_crew = mysqli_real_escape_string($db, $_POST['number_of_crew']);

        if (empty($with_retention)) { $with_retention = "IN-BALAST"; }
        if (empty($vsl_nature)) { $vsl_nature = "BULK"; }


		$sql = "UPDATE vessel_details SET vsl_imo = '$vsl_imo', vsl_call_sign = '$vsl_call_sign', vsl_mmsi_number = '$vsl_mmsi_number', vsl_class = '$vsl_class', vsl_nationality = '$vsl_nationality', vsl_registry = '$vsl_registry', vsl_official_number = '$vsl_official_number', vsl_nrt = '$vsl_nrt', vsl_grt = '$vsl_grt', vsl_dead_weight = '$vsl_dead_weight', vsl_breth = '$vsl_breth', vsl_depth = '$vsl_depth', vsl_loa = '$vsl_loa', vsl_pni = '$vsl_pni', vsl_owner_name = '$vsl_owner_name', vsl_owner_address = '$vsl_owner_address', vsl_owner_email = '$vsl_owner_email', vsl_operator_name = '$vsl_operator_name', vsl_operator_address = '$vsl_operator_address', vsl_nature = '$vsl_nature', vsl_cargo = '$vsl_cargo', vsl_cargo_name = '$vsl_cargo_name', shipper_name = '$shipper_name', shipper_address = '$shipper_address', last_port = '$last_port', next_port = '$next_port', with_retention = '$with_retention', capt_name = '$capt_name', number_of_crew = '$number_of_crew' WHERE id = '$ship_perticularId' ";
		if(mysqli_query($db, $sql)){$msg = alertMsg("ship_perticular Successfully!", "success");}
		else{$msg = alertMsg("Something went wrong, Couldn't incert data!", "danger");}
	}


	//export forwadings
	if (isset($_POST['export_vsl_forwadings'])) {
		$year = date("Y"); $month = date("m"); $day = date("d");
		$btnVal = $_POST['export_vsl_forwadings']; $filename = "";
		$ship_perticularId = mysqli_real_escape_string($db, $_POST['ship_perticularId']);
		$msl_num = mysqli_real_escape_string($db, $_POST['msl_num']);

		$run = mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ");
        $row = mysqli_fetch_assoc($run);
        $ship_perticularId = $row['id']; 
        $vsl_imo = $row['vsl_imo'];
        $vsl_call_sign = $row['vsl_call_sign'];
        $vsl_mmsi_number = $row['vsl_mmsi_number'];
        $vsl_class = $row['vsl_class'];
        $vsl_nationality = $row['vsl_nationality'];
        $vsl_registry = $row['vsl_registry'];
        $vsl_official_number = $row['vsl_official_number'];
        $vsl_nrt = $row['vsl_nrt'];
        $vsl_grt = $row['vsl_grt'];
        $vsl_dead_weight = $row['vsl_dead_weight'];
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


        $run1 = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
        $row1 = mysqli_fetch_assoc($run1); $vessel = $row1['vessel_name']; 
        $rep_id = $row1['representative']; 

        if(!empty($row1['arrived'])){$arrived = date('d.m.Y', strtotime($row1['arrived']));}
        else{$arrived = "";}
        if(!empty($row1['sailing_date'])){$sailing_date=date('d.m.Y',strtotime($row1['sailing_date']));}
        else{$sailing_date = "";}

        $rotation = $row1['rotation'];
        $rotation_2 = substr($rotation,7)." / ".substr($rotation,0,-7);
        if (empty($rotation)) { $rotation = date("Y")." / "; $rotation_2 = " -_____/".date("Y"); }

        $lstdaynextmonth = date('t',strtotime('next month'));
        $nextmonth = date('m', strtotime('+1 month', strtotime($row1['arrived'])));
        $inctxsaildate = $lstdaynextmonth.".".$nextmonth.".".$year;



        // export forwadings
		// file covers
		if($btnVal == "main_file_cover"){ mainfilecover($msl_num); }
		elseif($btnVal == "accounts_file_cover"){ accfilecover($msl_num); }
		elseif($btnVal=="file_covers"){mainfilecover($msl_num);accfilecover($msl_num);}

		// export vessel details
		elseif($btnVal=="export_vsl_details"){
			if(empty($vessel)){$msg=alertMsg("Vessel Name Is Missing", "danger");}
	        else{ export_vsl_details($msl_num); }
		}

		// before arrive
		// 1.prepartique
		elseif($btnVal=="prepartique"){
			if(empty($vsl_nationality)){$msg=alertMsg("Vessel Nationality Missing", "danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Lastport Missing","danger");}
	    	elseif(empty($vsl_cargo)){$msg=alertMsg("Cargo qty Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Cargo name Missing","danger");}
	        else{ prepartique($msl_num); }
		}
		// 2.vessel_declearation
		elseif($btnVal=="vsl_declearation"){
			if(empty($vsl_cargo)){$msg=alertMsg("Cargo qty Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Cargo name Missing","danger");}
	    	elseif(empty($vsl_imo)){$msg=alertMsg("Imo Number Missing","danger");}
	    	elseif(empty($vsl_grt)){$msg=alertMsg("Grt Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("Nrt Missing","danger");}
	    	elseif(empty($vsl_loa)){$msg=alertMsg("Loa Missing","danger");}
	    	elseif(empty($vsl_owner_name)){$msg=alertMsg("Owner Name Missing","danger");}
	    	elseif(empty($shipper_name)){$msg=alertMsg("Shipper name","danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Last port/Load port Missing","danger");}
	        else{ vsl_declearation($msl_num); }
		}
		// 3.portigm
		elseif($btnVal=="portigm"){
			if(empty($vsl_cargo)){$msg=alertMsg("Cargo qty Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Cargo name Missing","danger");}
	        else{ portigm($msl_num); }
		}
		// 4.plantq
		elseif($btnVal=="plantq"){
			if(empty($vsl_cargo)){$msg=alertMsg("Cargo Description Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Cargo name Missing","danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Last port/Load port Missing","danger");}
	        else{ plantq($msl_num); }
		}
		// 5.po_booking
		elseif($btnVal=="po_booking"){
			if(empty($vsl_cargo)){$msg=alertMsg("Cargo Description Missing","danger");}
	        else{ po_booking($msl_num); }
		}
		// 6.SURVEYOR BOOKING
		elseif($btnVal=="survey_booking"){
			if(empty($vsl_cargo)){$msg=alertMsg("Cargo Description Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Cargo name Missing","danger");}
	        else{ 
	        	survey_booking($msl_num);
	        	// survey_booking_bangla($msl_num); 
	        }
		}
		// export all before arrive
        elseif($btnVal == "before_arrive"){ 
        	if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	    	elseif(empty($vsl_cargo)){$msg=alertMsg("Vessel Cargo Description Missing","danger");}
	    	elseif(empty($vsl_cargo_name)){$msg=alertMsg("Vessel Name Missing","danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Loadport Missing!","danger");}
	    	else{
	    		prepartique($msl_num); vsl_declearation($msl_num); portigm($msl_num);
	    		plantq($msl_num); po_booking($msl_num); survey_booking($msl_num);
	    	}
        }

		// after arrive
		// final entry
		elseif($btnVal == "finalEntry"){ finalentryexport($msl_num); }
		// pc_forwading
		elseif ($btnVal == "pcForwading") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Vessel Flag Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("Vessel Nrt Missing","danger");}
	        else{ pcforwadingexport($msl_num); }
		}
		// pc_stamp
		elseif ($btnVal == "Stamp_PC") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Vessel Flag Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("Vessel Nrt Missing","danger");}
	        else{ pcstampexport($msl_num); }
		}
		// inc tax forwading
		elseif ($btnVal == "inctaxforwading") {
			if(empty($arrived)){$msg=alertMsg("Vessel Not Yet Received!","danger");}
    		else{inctaxforwading($msl_num);}
		}
		// inc tax stamp
		elseif($btnVal == "Stamp_inctax"){
        	if(empty($arrived)){$msg=alertMsg("Vessel Not Yet Received!","danger");}
        	else{ inctaxstamp($msl_num); }
        }
        // export all
        elseif($btnVal == "after_arrive"){ 
        	if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Vessel Flag Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("Vessel Nrt Missing","danger");}
	    	elseif(empty($arrived)){$msg=alertMsg("Vessel Not Yet Received!","danger");}
	    	else{
	    		finalentryexport($msl_num);
	    		pcforwadingexport($msl_num); pcstampexport($msl_num);
	    		inctaxforwading($msl_num); inctaxstamp($msl_num);
	    	}
        }

        // after sail
        elseif ($btnVal == "port_health") { 
	    	if(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
	    	elseif(empty($capt_name)){$msg=alertMsg("Capt Name Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("GRT Missing","danger");}
	    	elseif(empty($vsl_grt)){$msg=alertMsg("NRT Name Missing","danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Nationality/Flag Missing","danger");}
	    	elseif(empty($vsl_registry)){$msg=alertMsg("Port of Registry Missing","danger");}
	    	elseif(empty($vsl_imo)){$msg=alertMsg("IMO Number Missing","danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Load Port Missing","danger");}
	    	// elseif(empty($next_port)){$msg=alertMsg("Next Port Missing","danger");}
	    	elseif(empty($arrived)){$msg=alertMsg("Vessel Not Received Yet","danger");}
	    	elseif(empty($sailing_date)){$msg=alertMsg("Vessel Not Sailed Yet","danger");}
	    	elseif(empty($vsl_dead_weight)){$msg=alertMsg("Dead Weight Missing","danger");}
	    	elseif(empty($number_of_crew)){$msg=alertMsg("Number of Crew Missing","danger");}
	    	// elseif(empty($with_retention)){$msg=alertMsg("With Retention field empty","danger");}
	        else{ port_health($msl_num); }
		}

		// PSC_SUBMISSION
        elseif ($btnVal == "psc_submission") { 
			if(empty($sailing_date)){$msg=alertMsg("Vessel Not Sailed Yet", "danger");}
	    	elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
	        else{ psc_submission($msl_num); }
		}
		// EGM_Forwading
        elseif ($btnVal == "egm_forwading") { 
			if(empty($sailing_date)){$msg=alertMsg("Sailing Date Missing", "danger");}
	    	elseif(empty($arrived)){$msg=alertMsg("Receving Date Missing","danger");}
	    	elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
	    	elseif(empty($with_retention)){$msg=alertMsg("With Retention Data Missing","danger");}
	        else{ egm_forwading($msl_num); }
		}
		// EGM_Format
        elseif ($btnVal == "egm_format") { 
			if(empty($vsl_grt)){$msg=alertMsg("GRT Missing", "danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("NRT Missing","danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Vessel Nationality Missing","danger");}
	    	elseif(empty($capt_name)){$msg=alertMsg("Capt Name Missing","danger");}
	    	elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
	    	elseif(empty($with_retention)){$msg=alertMsg("With Retention Data Missing","danger");}
	    	elseif(empty($next_port)){$msg=alertMsg("Next Port Missing","danger");}
	        else{ egm_format($msl_num); }
		}
		// EGM_Format
        elseif ($btnVal == "after_sail") { 
			if(empty($vsl_grt)){$msg=alertMsg("GRT Missing", "danger");}
			elseif(empty($vsl_imo)){$msg=alertMsg("IMO Number Missing","danger");}
	    	elseif(empty($vsl_nrt)){$msg=alertMsg("NRT Missing","danger");}
	    	elseif(empty($vsl_nationality)){$msg=alertMsg("Vessel Nationality Missing","danger");}
	    	elseif(empty($vsl_registry)){$msg=alertMsg("Port of Registry Missing","danger");}
	    	elseif(empty($capt_name)){$msg=alertMsg("Capt Name Missing","danger");}
	    	elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
	    	elseif(empty($with_retention)){$msg=alertMsg("With Retention Data Missing","danger");}
	    	elseif(empty($next_port)){$msg=alertMsg("Next Port Missing","danger");}
	    	elseif(empty($sailing_date)){$msg=alertMsg("Sailing Date Missing", "danger");}
	    	elseif(empty($arrived)){$msg=alertMsg("Receving Date Missing","danger");}
	    	elseif(empty($last_port)){$msg=alertMsg("Load Port Missing","danger");}
	    	elseif(empty($vsl_dead_weight)){$msg=alertMsg("Dead Weight Missing","danger");}
	    	elseif(empty($number_of_crew)){$msg=alertMsg("Number of Crew Missing","danger");}
	        else{ 
	        	port_health($msl_num); psc_submission($msl_num);
	        	egm_forwading($msl_num); egm_format($msl_num);
	        }
		}

		// Arrival Perticular
        elseif ($btnVal == "arrival_perticular") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	        else{ arrival_perticular($msl_num); }
		}
		elseif ($btnVal == "ship_required_docs") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	        else{ ship_required_docs($msl_num); }
		}
		elseif ($btnVal == "representative_letter") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
			elseif(empty($rep_id)){$msg=alertMsg("Representative Not assigned yet!","danger");}
	        else{ representative_letter($msl_num); }
		}
		elseif ($btnVal == "lightdues") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
			elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
			elseif(empty($capt_name)){$msg=alertMsg("Capt Name Missing","danger");}
			elseif(empty($vsl_nrt)){$msg=alertMsg("NRT Missing","danger");}
			elseif(empty($last_port)){$msg=alertMsg("Load Port Missing","danger");}
	        else{ lightdues($msl_num); }
		}
		elseif ($btnVal == "watchman_letter") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	        else{ watchman_letter($msl_num); }
		}
		elseif ($btnVal == "vendor_letter") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
	        else{ vendor_letter($msl_num); }
		}
		elseif ($btnVal == "export_rcv_docs") { 
			if(empty($vessel)){$msg=alertMsg("Vessel Name Missing", "danger");}
			elseif(empty($rep_id)){$msg=alertMsg("Representative Not assigned yet!","danger");}
			elseif(empty($rotation)){$msg=alertMsg("Rotation Number Missing","danger");}
			elseif(empty($capt_name)){$msg=alertMsg("Capt Name Missing","danger");}
			elseif(empty($vsl_nrt)){$msg=alertMsg("NRT Missing","danger");}
			elseif(empty($last_port)){$msg=alertMsg("Load Port Missing","danger");}
	        else{ 
	        	arrival_perticular($msl_num); ship_required_docs($msl_num);
	        	representative_letter($msl_num); lightdues($msl_num);
	        	watchman_letter($msl_num); vendor_letter($msl_num); 
	        }
		} else{$msg=alertMsg("btnVal: ".$btnVal,"danger");}
	}
?>