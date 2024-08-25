<?php
	// my info
	// logic should be "isset($_SESSION['id'])" so none could update profile except his
	if (isset($_GET['userid'])) { 
		$id = $_GET['userid']; 
		$rusr = mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM users WHERE id = '$id' "));
		$user = array(
			'id' => $id,
			'name' => $rusr['name'],
			'contact' => $rusr['contact'],
			'email' => $rusr['email'],
			'image' => $rusr['image'],
			'password' => $rusr['password']
		);
	}else{ $user = array( 'id' => '', 'name' => '', 'contact' => '', 'email' => '', 'image' => '', 'password' => ''); }

	// vessel info
	if(isset($_GET['msl_num'])&& !empty($_GET['msl_num'])||isset($_GET['edit'])&& !empty($_GET['edit'])){ 
		if(isset($_GET['msl_num']) && !empty($_GET['msl_num'])){$msl_num=$_GET['msl_num']; }
		else{$msl_num = $_GET['edit'];}
		$rvsl=mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM vessels WHERE msl_num = '$msl_num' "));
		$vessel = array(
			'id' => $msl_num,
			'vessel_name' => $rvsl['vessel_name'],
			'rotation' => $rvsl['rotation'],
			'arrived' => $rvsl['arrived'],
			'rcv_date' => $rvsl['rcv_date'],
			'sailing_date' => $rvsl['sailing_date'],
			'stevedore' => $rvsl['stevedore'],
			'kutubdia_qty' => $rvsl['kutubdia_qty'],
			'outer_qty' => $rvsl['outer_qty'],
			'retention_qty' => $rvsl['retention_qty'],
			'seventyeight_qty' => $rvsl['seventyeight_qty'],
			'com_date' => $rvsl['com_date'],
			'fender_off' => $rvsl['fender_off'],
			'received_by' => $rvsl['received_by'],
			'sailed_by' => $rvsl['sailed_by'],
			'anchor' => $rvsl['anchor'],
			'representative' => $rvsl['representative'],
			'survey_consignee' => $rvsl['survey_consignee'],
			'survey_custom' => $rvsl['survey_custom'],
			'survey_supplier' => $rvsl['survey_supplier'],
			'survey_pni' => $rvsl['survey_pni'],
			'survey_chattrer' => $rvsl['survey_chattrer'],
			'survey_owner' => $rvsl['survey_owner'],
			'vsl_opa' => $rvsl['vsl_opa'],
			'custom_visited' => $rvsl['custom_visited'],
			'qurentine_visited' => $rvsl['qurentine_visited'],
			'psc_visited' => $rvsl['psc_visited'],
			'multiple_lightdues' => $rvsl['multiple_lightdues'],
			'crew_change' => $rvsl['crew_change'],
			'has_grab' => $rvsl['has_grab'],
			'fender' => $rvsl['fender'],
			'fresh_water' => $rvsl['fresh_water'],
			'piloting' => $rvsl['piloting'],
			'remarks' => $rvsl['remarks'],
			'status' => $rvsl['status']
		);
	}else{
		$vessel = array(
			'id' => '','vessel_name' => '','rotation' => '','arrived' => '','rcv_date' => '','sailing_date' => '','stevedore' => '','kutubdia_qty' => '','outer_qty' => '','retention_qty' => '','seventyeight_qty' => '','com_date' => '','fender_off' => '','received_by' => '','sailed_by' => '','anchor' => '','representative' => '','survey_consignee' => '','survey_custom' => '','survey_supplier' => '','survey_pni' => '','survey_chattrer' => '','survey_owner' => '','vsl_opa' => '','custom_visited' => '','qurentine_visited' => '','psc_visited' => '','multiple_lightdues' => '','crew_change' => '','has_grab' => '','fender' => '','fresh_water' => '','piloting' => '','remarks' => '','status' => ''
		);
	}
	// echo "<h1>".$vessel['survey_custom']."</h1>";
?>