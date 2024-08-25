<?php include('inc/header.php'); ?>

    <div class="d-flex align-items-stretch">
      <!-- Sidebar Navigation-->
      <?php include('inc/sidebar.php'); ?>
      <!-- Sidebar Navigation end-->
      <div class="page-content">

        <div class="page-header">
          <div class="container-fluid">
            <h2 class="h5 no-margin-bottom" style="text-align: center;">
              <span style="float: left;">Dashboard</span>
              <!-- one line if else statement -->
              <?php //$msl_num = isset($_GET['edit']) ? $_GET['edit'] : $_GET['view']; ?>
              <?php 
                if(isset($_GET['msl_num'])){$msl_num = $_GET['msl_num'];}
                elseif(isset($_GET['edit'])){$msl_num = $_GET['edit'];} 
                else{$msl_num = $_GET['ship_perticular'];}

                $row0 = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ")); $vessel = $row0['vessel_name'];
              ?>
              <a href="vessel_details.php?ship_perticular=<?php echo $msl_num; ?>">
                FORWADINGS
              </a>
            </h2>
          </div>
        </div>
        <?php echo $msg; /*include('inc/errors.php');*/ ?>

        <?php if(isset($_GET['msl_num'])){ $msl_num = $_GET['msl_num'];$percen = percentage($msl_num); ?>
        <section class="no-padding-top no-padding-bottom">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">

                  <div class="title">
                    <strong>Vassel Details</strong>
                    <a href="vessel_details.php?edit=<?php echo $msl_num; ?>" class="btn btn-secondary btn-sm" style="float: right;">
                      <i class="icon-ink"></i> Edit
                    </a>

                  </div>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $percen; ?>%;" aria-valuenow="<?php echo $percen; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percen; ?>%</div>
                  </div>
                  <!-- <div class="progress">
                    <?php //percentage($msl_num); ?>
                  </div> -->
                  <div class="table-responsive"> 
                    <table class="table table-dark table-sm table-custom">
                      <thead>
                        <tr style="color: white; border: 1px solid white;">
                          <th>Msl</th>
                          <th colspan="2">Vessel</th>
                          <th>Rotaion</th>
                          <th>Rcv</th>
                          <th>Sail</th>
                          <!-- <th>Stevedore</th> -->
                          <th>Cargo</th>
                          <th>Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselDetailsNew($msl_num); ?>
                      </tbody>
                    </table>
                  </div>


                </div>
              </div>
            </div>
          </div>
        </section>





        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Load Draft Surveyors</strong>
                    <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addSurveyorLoad">+ Add Surveyor</button>
                

                    <!-- Modal -->
                    <div class="modal fade" id="addSurveyorLoad" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">
                              Insert Vessels Surveyors Info
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                            <input type="hidden" name="vesselId" value="<?php echo $msl_num; ?>">
                            <div class="modal-body">
                              
                              <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                              <div class="form-row">
                                <div class="form-group col-md-6">
                                  <!-- <label for="inputState">Survey Company</label>
                                  <select id="inputState" class="form-control search" name="survey_company" required>
                                    <option value="">--Select--</option>
                                    <?php // selectOptions('surveycompany', 'company_name'); ?>
                                  </select> -->
                                  <label for="inputState">Party</label>
                                  <select id="inputState" class="form-control search" name="party">
                                    <option value="">--Select--</option>
                                    <?php
                                    $run = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
                                    $row = mysqli_fetch_assoc($run); 
                                    $custom = $row['survey_custom'];
                                    $consignee = $row['survey_consignee'];
                                    $supplier = $row['survey_supplier'];
                                    $pni = $row['survey_pni'];
                                    $chattrer = $row['survey_chattrer'];
                                    $owner = $row['survey_owner'];
                                    if ($custom != 0) {
                                      echo "<option value=\"survey_custom\">Custom</option>";
                                    }if ($consignee != 0) {
                                      echo "<option value=\"survey_consignee\">Consignee</option>";
                                    }if ($supplier != 0) {
                                      echo "<option value=\"survey_supplier\">Supplier</option>";
                                    }if ($owner != 0) {
                                      echo "<option value=\"survey_owner\">Owner</option>";
                                    }if ($pni != 0) {
                                      echo "<option value=\"survey_pni\">PNI</option>";
                                    }if ($chattrer != 0) {
                                      echo "<option value=\"survey_chattrer\">Chattrer</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputState">Surviour</label>
                                  <select id="inputState" class="form-control search" name="surveyorId" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('surveyors', 'surveyor_name'); ?>
                                  </select>
                                </div>

                              </div>
                              <!-- <div class="form-row">
                                <div class="form-group col-md-12">
                                  <label for="inputState">Survey Purpose</label>
                                  <select id="inputState" class="form-control search" name="survey_purpose" required>
                                    <option value="">--Select--</option>
                                    <option value="Load Draft">Load Draft</option>
                                    <option value="Rob">Rob</option>
                                    <option value="Light Draft">Light Draft</option>
                                  </select>
                                </div>
                              </div> -->
                              <input type="hidden" name="survey_purpose" value="Load Draft">
                              
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addVesselsSurveyor" class="btn btn-success">
                                +ADD
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="block-body">
                    <table class="table table-dark">
                      <thead>
                        <tr>
                          <th class="col-2" scope="col">Party</th>
                          <th class="col-3" scope="col">Company</th>
                          <th class="col-2" scope="col">Purpose</th>
                          <th class="col-3" scope="col">Surveyor</th>
                          <th class="col-2" scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselSurveyors($msl_num, "load"); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>


        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Light Draft Surveyors</strong>
                    <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addSurveyorLight">+ Add Surveyor</button>
                

                    <!-- Modal -->
                    <div class="modal fade" id="addSurveyorLight" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Insert Bin Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                            <input type="hidden" name="vesselId" value="<?php echo $msl_num; ?>">
                            <div class="modal-body">
                              
                              <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                              <div class="form-row">
                                <div class="form-group col-md-6">
                                  <!-- <label for="inputState">Survey Company</label>
                                  <select id="inputState" class="form-control search" name="survey_company" required>
                                    <option value="">--Select--</option>
                                    <?php // selectOptions('surveycompany', 'company_name'); ?>
                                  </select> -->
                                  <label for="inputState">Party</label>
                                  <select id="inputState" class="form-control search" name="party">
                                    <option value="">--Select--</option>
                                    <?php
                                    $run = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
                                    $row = mysqli_fetch_assoc($run); 
                                    $custom = $row['survey_custom'];
                                    $consignee = $row['survey_consignee'];
                                    $supplier = $row['survey_supplier'];
                                    $pni = $row['survey_pni'];
                                    $chattrer = $row['survey_chattrer'];
                                    $owner = $row['survey_owner'];
                                    if ($custom != 0) {
                                      echo "<option value=\"survey_custom\">Custom</option>";
                                    }if ($consignee != 0) {
                                      echo "<option value=\"survey_consignee\">Consignee</option>";
                                    }if ($supplier != 0) {
                                      echo "<option value=\"survey_supplier\">Supplier</option>";
                                    }if ($owner != 0) {
                                      echo "<option value=\"survey_owner\">Owner</option>";
                                    }if ($pni != 0) {
                                      echo "<option value=\"survey_pni\">PNI</option>";
                                    }if ($chattrer != 0) {
                                      echo "<option value=\"survey_chattrer\">Chattrer</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputState">Surviour</label>
                                  <select id="inputState" class="form-control search" name="surveyorId" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('surveyors', 'surveyor_name'); ?>
                                  </select>
                                </div>

                              </div>
                              <div class="form-row">
                                <div class="form-group col-md-12">
                                  <label for="inputState">Survey Purpose</label>
                                  <select id="inputState" class="form-control search" name="survey_purpose" required>
                                    <option value="">--Select--</option>
                                    <!-- <option value="Load Draft">Load Draft</option> -->
                                    <option value="Rob">Rob</option>
                                    <option value="Light Draft">Light Draft</option>
                                  </select>
                                </div>
                              </div>
                              
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addVesselsSurveyor" class="btn btn-success">
                                +ADD
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="block-body">
                    <table class="table table-dark">
                      <thead>
                        <tr>
                          <th class="col-2" scope="col">Party</th>
                          <th class="col-3" scope="col">Company</th>
                          <th class="col-2" scope="col">Purpose</th>
                          <th class="col-3" scope="col">Surveyor</th>
                          <th class="col-2" scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselSurveyors($msl_num, "light"); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>


        <!-- <section class="no-padding-top no-padding-bottom">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">

                  <div class="title">
                    <strong>Vassel Details</strong>
                    <a href="vessel_details.php?edit=<?php echo $msl_num; ?>" class="btn btn-secondary btn-sm" style="float: right;">
                      <i class="icon-ink"></i> Edit
                    </a>
                  </div>

                  <div class="table-responsive"> 
                    <table id="example" class="table table-dark table-striped table-sm">
                      <tbody>
                        <?php //vesselDetails($msl_num); ?>
                      </tbody>
                    </table>
                  </div>


                </div>
              </div>
            </div>
          </div>
        </section> -->


        <!-- add consignee & CNF -->
        <!-- <section class="no-padding-top no-padding-bottom">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-6">
                <div class="block">

                  <div class="title">
                    <strong>Add Consignee To This Vessel</strong>
                  </div>

                  <div class="table-responsive"> 
                    <table id="example" class="table table-dark table-striped table-sm">
                      <tbody>
                        <?php //allConsignee("addBtn"); ?>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>

              <div class="col-lg-6">
                <div class="block">

                  <div class="title">
                    <strong>Add CNF To This Vessel</strong>
                  </div>

                  <div class="table-responsive"> 
                    <table id="example" class="table table-dark table-striped table-sm">
                      <tbody>
                        <?php //allCnf("addBtn"); ?>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </section> -->


        <?php } elseif(isset($_GET['edit'])){ ?>


        <!-- edit vessel -->
        <?php
            $msl_num = $_GET['edit']; 
            $run2 = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
            $row2 = mysqli_fetch_assoc($run2);

            $vesselId = $row2['id']; 
            $vessel_name = $row2['vessel_name'];
            $received_by = $row2['received_by'];
            $rotation = $row2['rotation'];
            $stevedore = $row2['stevedore'];
            $kutubdia_qty = $row2['kutubdia_qty'];
            $outer_qty = $row2['outer_qty'];
            $retention_qty = $row2['retention_qty'];
            $seventyeight_qty = $row2['seventyeight_qty'];
            $sailed_by = $row2['sailed_by'];
            $repId = $row2['representative'];
            $arrived = $row2['arrived'];
            $rcv_date = $row2['rcv_date'];
            $com_date = $row2['com_date'];
            $sailing_date = $row2['sailing_date'];

            if (!empty($arrived)) {
              if (!empty($rcv_date)&&$arrived!=$rcv_date){$ckstatusRcv = "";}
              else{$ckstatusRcv = "checked";}
            }else{$ckstatusRcv = "checked";}

            if (!empty($com_date)) {
              if (!empty($sailing_date)&&$com_date!=$sailing_date){$ckstatusSail = "";}
              else{$ckstatusSail = "checked";}
            }else{$ckstatusSail = "checked";}

            $anchor = $row2['anchor'];
            $survey_custom = $row2['survey_custom'];
            $survey_consignee = $row2['survey_consignee'];
            $survey_supplier = $row2['survey_supplier'];
            $survey_pni = $row2['survey_pni'];
            $survey_chattrer = $row2['survey_chattrer'];
            $survey_owner = $row2['survey_owner'];

            // not count in percentage start
            $vsl_opa = $row2['vsl_opa'];

            $custom_visited = $row2['custom_visited'];
            $qurentine_visited = $row2['qurentine_visited'];
            $psc_visited = $row2['psc_visited'];
            $multiple_lightdues = $row2['multiple_lightdues'];
            $crew_change = $row2['crew_change'];
            $has_grab = $row2['has_grab'];
            $fender = $row2['fender'];
            $fresh_water = $row2['fresh_water'];
            $piloting = $row2['piloting'];

            $custom_v = $qurentine_v = $psc_v = $multiple_l = $crew_c = $has_g = $fender_us = $fresh_w = $piloting_us = $obl_h = $obl_i = "";
            if($custom_visited == 1){$custom_v = "checked";}
            if($qurentine_visited == 1){$qurentine_v = "checked";}
            if($psc_visited == 1){$psc_v = "checked";}
            if($multiple_lightdues == 1){$multiple_l = "checked";}
            if($crew_change == 1){$crew_c = "checked";}
            if($has_grab == 1){$has_g = "checked";}
            if($fender == 1){$fender_us = "checked";}
            if($fresh_water == 1){$fresh_w = "checked";}
            if($piloting == 1){$piloting_us = "checked";}
            // not count in percentage end

            $ttlqtyplused = floatval($outer_qty) + floatval($kutubdia_qty) + floatval($retention_qty);
            $ttlctgqty = floatval($outer_qty) + floatval($kutubdia_qty);

            // stevedore select data
            // $stvdrnm = allData('stevedore', $stevedore, 'name');
            // received by select data
            
            if($received_by != 0){$rcvbynm = allData('users', $received_by, 'name');}
            // sailed by select data
            if($sailed_by != 0){$slbynm = allData('users', $sailed_by, 'name');}
            
            // representative select data
            $representative_name = allData('users', $repId, 'name');

            $cargo_srt_nm = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_key');
            if($cargo_srt_nm != 0){$cargo_short_name = allData('cargokeys', $cargo_srt_nm, 'name');}

            $cargo_bl_name = allDataUpdated('vessels_cargo', 'msl_num', $msl_num, 'cargo_bl_name');
            // $total_qty = gettotal('vessels_cargo', 'msl_num', $msl_num, 'quantity');
            $total_qty = ttlcargoqty($msl_num);

        ?>
        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              
              <!-- Form Elements -->
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Update Vessel </strong>
                    <a 
                      onClick="javascript: return confirm('Please confirm deletion');" 
                      href="index.php?del_msl_num=<?php echo $msl_num; ?>" 
                      class="btn btn-danger btn-sm"
                       style="float: right;"
                    ><i class="bi bi-trash"></i></a>
                    <a href="vessel_details.php?msl_num=<?php echo $msl_num; ?>" class="btn btn-secondary btn-sm" style="float: right; margin-right: 10px;">
                      <i class="icon-ink"></i> View
                    </a>
                  </div>

                  <?php if (percentage($msl_num) < 100) { ?>
                    <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#completepercentage">Complete</button>
                  <?php } ?>

                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo percentage($msl_num); ?>%;" aria-valuenow="<?php echo percentage($msl_num); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo percentage($msl_num); ?>%</div>
                  </div>

                  <div class="block-body">

                    <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                      <!-- 1st -->
                      <div class="form-row">
                        <div class="form-group col-md-1">
                          <label for="inputEmail4">Msl Num</label>
                          <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                          <input type="text" class="form-control" name="msl_num" disabled value="<?php echo $msl_num; ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label>Vessel Name</label>
                          <input type="text" class="form-control" name="vessel_name" required value="<?php echo $vessel_name ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Arrived</label>
                          <input type="text" class="form-control" name="arrived" value="<?php echo $arrived; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Received (same) <input type="checkbox" name="sameRcv" value="sameRcv" <?php echo $ckstatusRcv; ?>></label>
                          <input type="text" class="form-control" name="rcv_date" value="<?php echo $rcv_date; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Completd</label>
                          <!-- <label>(same</label>
                          <input type="checkbox" name=""> -->
                          <input type="text" class="form-control" name="com_date" value="<?php echo $com_date; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Sailed (same) <input type="checkbox" name="sameSail" value="sameSail" <?php echo $ckstatusSail; ?>></label>
                          <input type="text" class="form-control" name="sailing_date" value="<?php echo $sailing_date; ?>">
                        </div>
                      </div>

                      <!-- 2nd -->
                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Kutubdia Quantity</label>
                          <input type="number" step="any" class="form-control" name="kutubdia_qty" value="<?php echo $kutubdia_qty ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputPassword4">Outer Quantity</label>
                          <input type="number" step="any" class="form-control" name="outer_qty" value="<?php echo $outer_qty ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputPassword4">Retention Quantity</label>
                          <input type="number" step="any" class="form-control" name="retention_qty" value="<?php echo $retention_qty ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputState">78 Quantity</label>
                          <input type="number" step="any" class="form-control" name="seventyeight_qty" value="<?php echo $seventyeight_qty ?>">
                        </div>
                      </div>

                      <!-- 3rd -->
                      <div class="form-row">
                        <!-- <div class="form-group col-md-3">
                          <label for="inputState">Load Port</label>
                           <select name="loadport[]" class="form-control mb-3 mb-3 selectpicker" multiple style="background: transparent;" data-live-search="true">
                            <?php
                              // $run = mysqli_query($db, "SELECT * FROM loadport ");
                              // while ($row = mysqli_fetch_assoc($run)) {
                              //   $id = $row['id']; $value = $row['port_name'];
                              //   $getLoadPort = mysqli_query($db, "SELECT * FROM vessels_loadport WHERE loadport = '$id' AND msl_num = '$msl_num' ");
                              //   if (mysqli_num_rows($getLoadPort) > 0) { $selected = "selected"; }
                              //   else{$selected = "";}
                              //   echo"<option value=\"$id\" $selected>$value</option>";
                              // }
                            ?>
                          </select>
                        </div> -->

                        <div class="form-group col-md-3">
                          <label for="inputState">Impoter</label>
                          <select name="importer[]" class="form-control mb-3 mb-3 selectpicker" multiple style="background: transparent;" data-live-search="true">
                            <?php
                              $run = mysqli_query($db, "SELECT * FROM bins WHERE type = 'IMPORTER' ");
                              while ($row = mysqli_fetch_assoc($run)) {
                                $id = $row['id']; $value = $row['name'];
                                $getImporter = mysqli_query($db, "SELECT * FROM vessels_importer WHERE importer = '$id' AND msl_num = '$msl_num' ");
                                if (mysqli_num_rows($getImporter) > 0) { $selected = "selected"; }
                                else{$selected = "";}
                                echo"<option value=\"$id\" $selected>$value</option>";
                              }
                            ?>
                          </select>
                        </div>


                        <div class="form-group col-md-6">
                          <label for="inputState">Stevedore</label>
                          <select id="inputState" class="form-control search" name="stevedore">
                            <option value="<?php echo $stevedore ?>"><?php echo alldata('stevedore', $stevedore, 'name'); ?></option>
                            <?php
                              $run = mysqli_query($db, "SELECT * FROM stevedore ");
                              while ($row = mysqli_fetch_assoc($run)) {
                                $id = $row['id']; $value = $row['name'];
                                if ($id == $stevedore) { continue; }
                                echo"<option value=\"$id\">$value</option>";
                              }
                            ?>
                          </select>
                        </div>
                        
                        <div class="form-group col-md-3">
                          <label for="inputState">Representative</label>
                          <select id="inputState" class="form-control search" name="representative">
                            <option value="<?php echo $repId ?>"><?php echo $representative_name; ?></option>
                            <?php
                              $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position = 'Representative' OR office_position = 'Junior Shipping Exicutive' ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $rep_name = $row1['name'];
                                if ($repId == $id) { continue; }
                                echo"<option value=\"$id\">$rep_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>

                      <!-- 4th -->
                      <div class="form-row">
                        <!-- <div class="form-group col-md-6">
                          <label for="inputPassword4">Cargo full name</label>
                          <input type="text" class="form-control" name="cargo_bl_name" value="<?php echo $cargo_bl_name ?>" required>
                        </div> -->
                        <!--div class="form-group col-md-3">
                          <label for="inputEmail4">Fender On</label>
                          <input type="text" class="form-control" name="fender_on" value="<?php echo $fender_on; ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Fender Off</label>
                          <input type="text" class="form-control" name="fender_off" value="<?php echo $fender_off; ?>">
                        </div-->
                        <div class="form-group col-md-3">
                          <label for="inputState">Rotation</label>
                          <input type="text" class="form-control" name="rotation" value="<?php echo $rotation ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputState">Anchorage</label>
                          <select id="inputState" class="form-control" name="anchor">
                            <?php
                              if ($anchor == "Outer") {
                                echo"
                                  <option value=\"\">--Select----</option>
                                  <option value=\"Outer\" selected>Outer</option>
                                  <option value=\"Kutubdia\">Kutubdia</option>
                                ";
                              }
                              elseif ($anchor == "Kutubdia") {
                                echo"
                                  <option value=\"\">--Select----</option>
                                  <option value=\"Outer\">Outer</option>
                                  <option value=\"Kutubdia\" selected>Kutubdia</option>
                                ";
                              }
                              else{
                                echo"
                                  <option value=\"\">--Select----</option>
                                  <option value=\"Outer\">Outer</option>
                                  <option value=\"Kutubdia\">Kutubdia</option>
                                ";
                              }
                            ?>
                          </select>
                        </div>

                        <div class="form-group col-md-6">
                          <label for="inputState">Opa</label>
                          <select id="inputState" class="form-control search" name="vsl_opa">
                            <?php
                              $company_name = allData('agent', $vsl_opa, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $vsl_opa; ?>"><?php echo $company_name; ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM agent ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $vsl_opa) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>

                      <!-- 5th -->
                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="inputState">Custom Survey</label>
                          <select id="inputState" class="form-control search" name="survey_custom">
                            <?php
                              $company_name = allData('surveycompany', $survey_custom, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_custom; ?>"><?php echo $company_name; ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_custom) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="inputState">Consignee Survey</label>
                          <select id="inputState" class="form-control search" name="survey_consignee">
                            <?php
                              $company_name = allData('surveycompany', $survey_consignee, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_consignee; ?>"><?php echo $company_name ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_consignee) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }//echo"<option value=\"\">--Select--</option>";
                            ?>
                          </select>
                        </div>

                        <div class="form-group col-md-4">
                          <label for="inputState">Supplier Survey</label>
                          <select id="inputState" class="form-control search" name="survey_supplier">
                            <?php
                              $company_name = allData('surveycompany', $survey_supplier, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_supplier; ?>"><?php echo $company_name ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_supplier) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }//echo"<option value=\"\">--Select--</option>";
                            ?>
                          </select>
                        </div>
                      </div>

                      <!-- 6th -->
                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="inputState">Owner Survey</label>
                          <select id="inputState" class="form-control search" name="survey_owner">
                            <?php
                              $company_name = allData('surveycompany', $survey_owner, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_owner ?>"><?php echo $company_name; ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_owner) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="inputState">P&I Survey</label>
                          <select id="inputState" class="form-control search" name="survey_pni">
                            <?php
                              $company_name = allData('surveycompany', $survey_pni, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_pni ?>"><?php echo $company_name; ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_pni) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="inputState">Chattrer Survey</label>
                          <select id="inputState" class="form-control search" name="survey_chattrer">
                            <?php
                              $company_name = allData('surveycompany', $survey_chattrer, 'company_name');
                              if ($company_name == "") {
                                echo"<option value=\"\">--Select--</option>";
                              }else{
                            ?>
                            <option value="<?php echo $survey_chattrer ?>"><?php echo $company_name; ?></option>
                            <?php }
                              $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $company_name = $row1['company_name'];
                                if ($id == $survey_chattrer) { continue; }
                                echo"<option value=\"$id\">$company_name</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>

                      <!-- 7th -->
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="inputState">Received By</label>
                          <select id="inputState" class="form-control search" name="received_by">
                            <option value="<?php echo $received_by ?>"><?php echo $rcvbynm; ?></option>
                            <?php
                              $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position != 'Representative' ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $name = $row1['name'];
                                if ($received_by == $id) { continue; }
                                echo"<option value=\"$id\">$name</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputState">Sailed By</label>
                          <select id="inputState" class="form-control search" name="sailed_by">
                            <option value="<?php echo $sailed_by ?>"><?php echo $slbynm; ?></option>
                            <?php
                              $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position != 'Representative' ");
                              while ($row1 = mysqli_fetch_assoc($run1)) {
                                $id = $row1['id']; $name = $row1['name'];
                                if ($sailed_by == $id) { continue; }
                                echo"<option value=\"$id\">$name</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>












                      <!-- 8th -->
                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="custom_visited" value="1" <?php echo $custom_v; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Custom visited
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="qurentine_visited" value="1" <?php echo $qurentine_v; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Qurentine visited
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="psc_visited" value="1" <?php echo $psc_v; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Psc Visited
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fender" value="1" <?php echo $fender_us; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Fender
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="crew_change" value="1" <?php echo $crew_c; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Crew Change
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_grab" value="1" <?php echo $has_g; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Has Grab
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fresh_water" value="1" <?php echo $fresh_w; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Fresh Water
                            </label>
                          </div>
                        </div>

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="piloting" value="1" <?php echo $piloting_us; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Piloting
                            </label>
                          </div>
                        </div>
                      </div>


                      <div class="form-row">

                        <div class="form-group col-md-3">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="multiple_lightdues" value="1" <?php echo $multiple_l; ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Multiple Lightdues
                            </label>
                          </div>
                        </div>

                      </div>
















                      <!-- 9th -->
                      <div class="form-group">
                        <label for="exampleInputPassword1">Insert / Write Remarks</label>
                        <!-- <input type="text" name="bank_name" class="form-control" required placeholder="BANK NAME"> -->
                        <textarea name="remarks" class="form-control" rows="3"><?php echo allData('vessels', $vesselId, 'remarks'); ?></textarea>
                      </div>

                      <button type="submit" name="vslUpdate" class="btn btn-success">Update</button>
                      <a href="vessel_details.php?msl_num=<?php echo $msl_num; ?>" class="btn btn-primary">Cancel</a>
                    </form>


                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>


        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Vessels Cargo </strong>
                    <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addCargo">+ Add Cargo</button>
                    <?php $id = allDataUpdated('vessels', 'msl_num', $msl_num, 'id'); ?>

                    <!-- Modal -->
                    <div class="modal fade" id="addCargo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Insert Cargo Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form method="post" style="padding-left: 15px; padding-right: 15px;" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                            <div class="modal-body">
                              <input type="hidden" name="vesselId" value="<?php echo $id; ?>">
                              <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                              <div class="form-row">
                                <div class="form-group col-md-3">
                                  <label for="inputState">Select Cargo</label>
                                  <select id="inputState" name="cargokey" class="form-control search" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('cargokeys', 'name'); ?>
                                  </select>
                                </div>
                                <!-- <div class="form-group col-md-3">
                                  <label for="inputState">Select Vessel</label>
                                  <select id="inputState" name="vesselId" class="form-control search" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('vessels', 'vessel_name'); ?>
                                  </select>
                                </div> -->
                                <div class="form-group col-md-6">
                                  <label for="inputState">Select Loadport</label>
                                  <select id="inputState" name="loadport" class="form-control search" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('loadport', 'port_name'); ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-3">
                                  <label for="inputState">Quantity</label>
                                  <input type="number" step="any" class="form-control" name="quantity" required>
                                </div>
                              </div>
                              
                              <div class="form-row">
                                <div class="form-group col-md-12">
                                  <label for="inputState">Cargo Bl Name</label>
                                  <input type="text" class="form-control" name="cargo_bl_name" required>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addCargoConsigneewise" class="btn btn-success">
                                +ADD
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="block-body">
                    <table class="table table-dark">
                      <thead>
                        <tr>
                          <th class="col-1" scope="col">Cargo</th>
                          <th class="col-2" scope="col">Port</th>
                          <th class="col-2" scope="col">Qty</th>
                          <th class="col-5" scope="col">Cargo Name</th>
                          <th class="col-2" scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselCargo($msl_num); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>


        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Surveyors</strong>
                    <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addSurveyor">+ Add Surveyor</button>
                

                    <!-- Modal -->
                    <div class="modal fade" id="addSurveyor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Insert Bin Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                            <input type="hidden" name="vesselId" value="<?php echo $msl_num; ?>">
                            <div class="modal-body">
                              
                              <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                              <div class="form-row">
                                <div class="form-group col-md-6">
                                  <!-- <label for="inputState">Survey Company</label>
                                  <select id="inputState" class="form-control search" name="survey_company" required>
                                    <option value="">--Select--</option>
                                    <?php // selectOptions('surveycompany', 'company_name'); ?>
                                  </select> -->
                                  <label for="inputState">Party</label>
                                  <select id="inputState" class="form-control search" name="party">
                                    <option value="">--Select--</option>
                                    <?php
                                    $run = mysqli_query($db, "SELECT * FROM vessels WHERE msl_num = '$msl_num' ");
                                    $row = mysqli_fetch_assoc($run); 
                                    $custom = $row['survey_custom'];
                                    $consignee = $row['survey_consignee'];
                                    $supplier = $row['survey_supplier'];
                                    $pni = $row['survey_pni'];
                                    $chattrer = $row['survey_chattrer'];
                                    $owner = $row['survey_owner'];
                                    if ($custom != 0) {
                                      echo "<option value=\"survey_custom\">Custom</option>";
                                    }if ($consignee != 0) {
                                      echo "<option value=\"survey_consignee\">Consignee</option>";
                                    }if ($supplier != 0) {
                                      echo "<option value=\"survey_supplier\">Supplier</option>";
                                    }if ($owner != 0) {
                                      echo "<option value=\"survey_owner\">Owner</option>";
                                    }if ($pni != 0) {
                                      echo "<option value=\"survey_pni\">PNI</option>";
                                    }if ($chattrer != 0) {
                                      echo "<option value=\"survey_chattrer\">Chattrer</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputState">Surviour</label>
                                  <select id="inputState" class="form-control search" name="surveyorId" required>
                                    <option value="">--Select--</option>
                                    <?php selectOptions('surveyors', 'surveyor_name'); ?>
                                  </select>
                                </div>

                              </div>
                              <div class="form-row">
                                <div class="form-group col-md-12">
                                  <label for="inputState">Survey Purpose</label>
                                  <select id="inputState" class="form-control search" name="survey_purpose" required>
                                    <option value="">--Select--</option>
                                    <option value="Load Draft">Load Draft</option>
                                    <option value="Rob">Rob</option>
                                    <option value="Light Draft">Light Draft</option>
                                  </select>
                                </div>
                              </div>
                              
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addVesselsSurveyor" class="btn btn-success">
                                +ADD
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="block-body">
                    <table class="table table-dark">
                      <thead>
                        <tr>
                          <th class="col-2" scope="col">Survey Party</th>
                          <th class="col-3" scope="col">Survey Company</th>
                          <th class="col-2" scope="col">Purpose</th>
                          <th class="col-3" scope="col">Surveyor</th>
                          <th class="col-2" scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselSurveyors($msl_num, "all"); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>

        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              
              <!-- Form Elements -->
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Add C&F</strong>
                    <!-- <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addCNF">+ Add C&F</button>
                

                    
                    <div class="modal fade" id="addCNF" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Insert Cnf Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                            <div class="modal-body">
                              <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                              <div class="form-row">
                                <div class="form-group col-md-6">
                                  <label for="inputState">Select Importer</label>
                                  <select id="inputState" name="importer" class="form-control search">
                                    <option value="">--Select--</option>
                                    <?php 
                                      //selectOptions('cnf', 'name'); 
                                      $run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
                                      while ($row = mysqli_fetch_assoc($run)) {
                                        $impId = $row['importer']; $impName = allData('bins', $impId, 'name');
                                        echo "<option value=\"$impId\">$impName</option>";
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputState">Select CNF</label>
                                  <select id="inputState" name="cnfId" class="form-control search">
                                    <option value="">--Select--</option>
                                    <?php selectOptions('cnf', 'name'); ?>
                                  </select>
                                </div>
                              </div>
                              
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addVesselsCnf" class="btn btn-success">
                                +ADD
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div> -->
                  </div>
                  <div class="block-body">
                    <table class="table table-dark">
                      <thead>
                        <tr>
                          <th class="col-5" scope="col">Importer</th>
                          <th class="col-5" scope="col">Cnf</th>
                          <th class="col-2" scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php vesselsCnf($msl_num); ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        
        

        <?php
          // edit vessels_surveyor
          $run = mysqli_query($db, "SELECT * FROM vessels_surveyor WHERE msl_num = '$msl_num'");
          while ($row = mysqli_fetch_assoc($run)) {
            $id = $row['id']; $party = $row['survey_party'];
            $surveyor = $row['surveyor']; $survey_purpose = $row['survey_purpose'];
            $surveyor_name = allData('surveyors', $surveyor, 'surveyor_name');
        ?>
        <!-- Consignee Edit Modal -->
        <div class="modal fade" id="<?php echo"editVesselSurveyors".$id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Insert Surveyor Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                <div class="modal-body">
                  
                  <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                  <input type="hidden" name="thisrowId" value="<?php echo $id; ?>">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <!-- <label for="inputState">Survey Company</label>
                      <select id="inputState" class="form-control search" name="survey_company" required>
                        <option value="">--Select--</option>
                        <?php // selectOptions('surveycompany', 'company_name'); ?>
                      </select> -->
                      <label for="inputState">Party</label>
                      <input type="hidden" name="party" value="<?php echo $party ?>">
                      <select id="inputState" class="form-control search" name="party" disabled>
                        <option value="<?php echo $party ?>"><?php echo $party ?></option>
                        <option value="survey_custom">Custom</option>
                        <option value="survey_consignee">Consignee</option>
                        <option value="survey_owner">Owner</option>
                        <option value="survey_pni">PNI</option>
                        <option value="survey_chattrer">Chattrer</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputState">Surviour</label>
                      <select id="inputState" class="form-control search" name="surveyorId">
                        <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option>
                        <?php selectOptions('surveyors', 'surveyor_name'); ?>
                      </select>
                    </div>

                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label for="inputState">Survey Purpose</label>
                      <select id="inputState" class="form-control search" name="survey_purpose" required>
                        <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                        <option value="Load Draft">Load Draft</option>
                        <option value="Rob">Rob</option>
                        <option value="Light Draft">Light Draft</option>
                      </select>
                    </div>
                  </div>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="update_vessels_surveyor" class="btn btn-success">
                    +Update
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php } ?>


        <?php
          // edit vessel_cargo
          $run = mysqli_query($db, "SELECT * FROM vessels_cargo WHERE msl_num = '$msl_num'");
          while ($row = mysqli_fetch_assoc($run)) {
            if(isset($_GET['msl_num'])){$msl_num=$_GET['msl_num'];}else{$msl_num=$_GET['edit'];}
            $vessel=allDataUpdated("vessels","msl_num",$msl_num,"vessel_name");
            $id = $row['id']; $cargo_key = $row['cargo_key']; $loadport = $row['loadport']; 
            $quantity = $row['quantity']; $cargo_bl_name = $row['cargo_bl_name']; 
            $cargo = allData('cargokeys', $cargo_key, 'name');
            $loadportnm = allData('loadport', $loadport, 'port_name');
        ?>
        <!-- Consignee Edit Modal -->
        <div class="modal fade" id="<?php echo"editVesselCargo".$id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Insert Cargo Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" style="padding-left: 15px; padding-right: 15px;" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="inputState">Select Cargo</label>
                    <select id="inputState" name="cargokey" class="form-control search" required>
                      <option value="<?php echo $cargo_key ?>"><?php echo $cargo; ?></option>
                      <?php selectOptions('cargokeys', 'name'); ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputState">Select Loadport</label>
                    <select id="inputState" name="loadport" class="form-control search" required>
                      <option value="<?php echo $loadport; ?>"><?php echo $loadportnm; ?></option>
                      <?php selectOptions('loadport', 'port_name'); ?>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="inputState">Quantity</label>
                    <input type="number" step="any" class="form-control" value="<?php echo $quantity; ?>" name="quantity" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="inputState">Cargo Bl Name</label>
                    <input type="text" class="form-control" name="cargo_bl_name" value="<?php echo $cargo_bl_name; ?>" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="updateCargoConsigneewise" class="btn btn-success">
                    +Update
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php } ?>


        <?php
          // edit vessels_cnf
          $run = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num'");
          while ($row = mysqli_fetch_assoc($run)) {
            $id = $row['id']; 
            if(isset($_GET['msl_num'])){$msl_num=$_GET['msl_num'];}else{$msl_num=$_GET['edit'];} 
            $importerId = $row['importer']; 
            $cnfId = $row['cnf']; 
            $cnfName = allData('cnf', $cnfId, 'name');
            $importerName = allData('bins', $importerId, 'name');
        ?>
        <!-- Consignee Edit Modal -->
        <div class="modal fade" id="<?php echo"editVesselsCnf".$id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Insert CNF Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                <div class="modal-body">
                  
                  <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                  <input type="hidden" name="thisrowId" value="<?php echo $id; ?>">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputState">Importer</label>
                      <select id="inputState" class="form-control selectpicker" multiple name="importers[]" data-live-search="true" required>
                        <!-- <option value="<?php echo $importerId; ?>"><?php echo $importerName; ?></option> -->
                        <?php 
                          $run5 = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
                          while ($row5 = mysqli_fetch_assoc($run5)) {
                            $thisid = $row5['id']; $imid = $row5['importer']; $cn = $row5['cnf'];

                            // select importer
                            if($cn==$cnfId && $cnfId != 0){$selected="selected";}
                            else{$selected = "";}
                            if($imid==$importerId && $importerId != 0){$selected="selected";}
                            
                            $impId = $row5['importer']; $impName = allData('bins', $impId, 'name');
                            echo "<option value=\"$impId\" $selected>$impName</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputState">Cnf</label>
                      <select id="inputState" class="form-control search" name="cnfId" required>
                        <option value="<?php echo $cnfId ?>"><?php echo $cnfName; ?></option>
                        <?php selectOptions('cnf', 'name'); ?>
                      </select>
                    </div>
                  </div>
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="update_vessels_cnf" class="btn btn-success">
                    +Update
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php } ?>

        <!-- end elseif ship_perticular -->
        <?php }else{ 
          $msl_num = $_GET['ship_perticular'];
          $run3 = mysqli_query($db, "SELECT * FROM vessel_details WHERE msl_num = '$msl_num' ");
            $row3 = mysqli_fetch_assoc($run3);
            $ship_perticularId = $row3['id']; 
            $vsl_imo = $row3['vsl_imo'];
            $vsl_call_sign = $row3['vsl_call_sign'];
            $vsl_mmsi_number = $row3['vsl_mmsi_number'];
            $vsl_class = $row3['vsl_class'];
            $vsl_nationality = $row3['vsl_nationality'];
            $vsl_registry = $row3['vsl_registry'];
            $vsl_official_number = $row3['vsl_official_number'];
            $vsl_nrt = $row3['vsl_nrt'];
            $vsl_grt = $row3['vsl_grt'];
            $vsl_dead_weight = $row3['vsl_dead_weight'];
            $vsl_breth = $row3['vsl_breth'];
            $vsl_depth = $row3['vsl_depth'];
            $vsl_loa = $row3['vsl_loa'];
            $vsl_pni = $row3['vsl_pni'];
            $vsl_owner_name = $row3['vsl_owner_name'];
            $vsl_owner_address = $row3['vsl_owner_address'];
            $vsl_owner_email = $row3['vsl_owner_email'];
            $vsl_operator_name = $row3['vsl_operator_name'];
            $vsl_operator_address = $row3['vsl_operator_address'];
            $vsl_nature = $row3['vsl_nature'];
            $vsl_cargo = $row3['vsl_cargo'];
            $vsl_cargo_name = $row3['vsl_cargo_name'];
            $shipper_name = $row3['shipper_name'];
            $shipper_address = $row3['shipper_address'];
            $last_port = $row3['last_port'];
            $next_port = $row3['next_port'];
            $with_retention = $row3['with_retention'];
            $capt_name = $row3['capt_name'];
            $number_of_crew = $row3['number_of_crew'];
        ?>
          <style type="text/css">
            .table-custom td, .table-custom th{
              border: none;
            }
          </style>
          <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              
              <!-- Form Elements -->
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>Ship Perticular Of MV. <?php echo $vessel; ?> </strong>
                    <!-- <a 
                      onClick="javascript: return confirm('Please confirm deletion');" 
                      href="index.php?del_msl_num=<?php echo $msl_num; ?>" 
                      class="btn btn-danger btn-sm"
                       style="float: right;"
                    ><i class="bi bi-trash"></i></a> -->
                    <a href="vessel_details.php?edit=<?php echo $msl_num; ?>" class="btn btn-secondary btn-sm" style="float: right; margin-right: 10px;">
                      <i class="icon-ink"></i> <-Back
                    </a>
                  </div>

                  <div class="block-body">

                    <form method="post" action="vessel_details.php?ship_perticular=<?php echo $msl_num; ?>">
                      <!-- 1st -->
                      <div class="form-row">
                        <div class="form-group col-md-1">
                          <label for="inputEmail4">Msl Num</label>
                          <input type="hidden" name="ship_perticularId" value="<?php echo $ship_perticularId; ?>">
                          <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                          <input type="text" class="form-control" name="msl_num" disabled value="<?php echo $msl_num; ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Class </label>
                          <input type="text" class="form-control" name="vsl_class" value="<?php echo $vsl_class; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label>Imo</label>
                          <input type="text" class="form-control" name="vsl_imo" required value="<?php echo $vsl_imo ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Call Sign</label>
                          <input type="text" class="form-control" name="vsl_call_sign" value="<?php echo $vsl_call_sign; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">MMSI Number</label>
                          <!-- <label>(same</label>
                          <input type="checkbox" name=""> -->
                          <input type="text" class="form-control" name="vsl_mmsi_number" value="<?php echo $vsl_mmsi_number; ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputPassword4">Official No</label>
                          <input type="text" step="any" class="form-control" name="vsl_official_number" value="<?php echo $vsl_official_number ?>">
                        </div>
                      </div>

                      <!-- 2nd -->
                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Nationality</label>
                          <input type="text" step="any" class="form-control" name="vsl_nationality" value="<?php echo $vsl_nationality ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputPassword4">Port Of Registry</label>
                          <input type="text" step="any" class="form-control" name="vsl_registry" value="<?php echo $vsl_registry ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputState">NRT</label>
                          <input type="text" step="any" class="form-control" name="vsl_nrt" value="<?php echo $vsl_nrt ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputState">GRT</label>
                          <input type="text" step="any" class="form-control" name="vsl_grt" value="<?php echo $vsl_grt ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputState">Dead Weight</label>
                          <input type="text" step="any" class="form-control" name="vsl_dead_weight" value="<?php echo $vsl_dead_weight ?>">
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Breath</label>
                          <input type="text" step="any" class="form-control" name="vsl_breth" value="<?php echo $vsl_breth ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputPassword4">Depth</label>
                          <input type="text" step="any" class="form-control" name="vsl_depth" value="<?php echo $vsl_depth ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputState">LOA</label>
                          <input type="text" step="any" class="form-control" name="vsl_loa" value="<?php echo $vsl_loa ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputState">P&I</label>
                          <input type="text" step="any" class="form-control" name="vsl_pni" value="<?php echo $vsl_pni ?>">
                        </div>
                      </div>

                      <!-- 3rd -->
                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="inputState">Owner</label>
                          <input type="text" step="any" class="form-control" name="vsl_owner_name" value="<?php echo $vsl_owner_name ?>">
                        </div>
                        <div class="form-group col-md-8">
                          <label for="inputState">Owner Address</label>
                          <input type="text" step="any" class="form-control" name="vsl_owner_address" value="<?php echo $vsl_owner_address ?>">
                        </div>
                      </div>

                      <!-- 4th -->
                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Owner Email</label>
                          <input type="text" step="any" class="form-control" name="vsl_owner_email" value="<?php echo $vsl_owner_email ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputPassword4">Operator</label>
                          <input type="text" step="any" class="form-control" name="vsl_operator_name" value="<?php echo $vsl_operator_name ?>">
                        </div>
                        <div class="form-group col-md-6">
                          <label for="inputState">Operator Address</label>
                          <input type="text" step="any" class="form-control" name="vsl_operator_address" value="<?php echo $vsl_operator_address ?>">
                        </div>
                      </div>

                      <!-- 5th -->
                      <div class="form-row">
                        <div class="form-group col-md-9">
                          <label for="inputEmail4">Vessel Cargo (Qty + Name)</label>
                          <input type="text" step="any" class="form-control" name="vsl_cargo" value="<?php echo $vsl_cargo ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Next Port</label>
                          <!-- <input type="text" step="any" class="form-control" name="next_port" value="<?php echo $next_port ?>">

                          <label for="inputState">Select Loadport</label> -->
                          <select id="inputState" name="next_port" class="form-control search">
                            <option value="<?php echo $next_port; ?>"><?php echo $next_port; ?></option>
                            <?php 
                              $run4 = mysqli_query($db, "SELECT * FROM loadport ");
                              while ($row4 = mysqli_fetch_assoc($run4)) {
                                $id = $row4['id']; $next_port = $row4['port_name'];
                                echo"<option value=\"$next_port\">$next_port</option>";
                              }
                            ?>
                          </select>
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group col-md-9">
                          <label for="inputPassword4">Cargo Name</label>
                          <input type="text" step="any" class="form-control" name="vsl_cargo_name" value="<?php echo $vsl_cargo_name ?>">
                        </div>
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">With Retention</label>
                          <input type="text" step="any" class="form-control" name="with_retention" value="<?php echo $with_retention ?>">
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group col-md-4">
                          <label for="inputEmail4">Nature</label>
                          <input type="text" step="any" class="form-control" name="vsl_nature" value="<?php echo $vsl_nature ?>">
                        </div>
                        <div class="form-group col-md-8">
                          <label for="inputState">Shipper Name</label>
                          <input type="text" step="any" class="form-control" name="shipper_name" value="<?php echo $shipper_name ?>">
                        </div>
                      </div>

                      <!-- 6th -->
                      <div class="form-row">
                        <div class="form-group col-md-12">
                          <label for="inputEmail4">Shipper Address</label>
                          <input type="text" step="any" class="form-control" name="shipper_address" value="<?php echo $shipper_address ?>">
                        </div>
                      </div>
                      
                      <!-- 7th -->
                      <div class="form-row">
                        <div class="form-group col-md-3">
                          <label for="inputEmail4">Last Port</label>
                          <!-- <input type="text" step="any" class="form-control" name="last_port" value="<?php echo $last_port ?>"> -->

                          <select id="inputState" name="last_port" class="form-control search">
                            <option value="<?php echo $last_port; ?>"><?php echo $last_port; ?></option>
                            <?php 
                              $run4 = mysqli_query($db, "SELECT * FROM loadport ");
                              while ($row4 = mysqli_fetch_assoc($run4)) {
                                $id = $row4['id']; $last_port = $row4['port_name'];
                                echo"<option value=\"$last_port\">$last_port</option>";
                              }
                            ?>
                          </select>
                        </div>
                        <div class="form-group col-md-7">
                          <label for="inputEmail4">Capt Name</label>
                          <input type="text" step="any" class="form-control" name="capt_name" value="<?php echo $capt_name ?>">
                        </div>
                        <div class="form-group col-md-2">
                          <label for="inputEmail4">Number Of Crew</label>
                          <input type="text" step="any" class="form-control" name="number_of_crew" value="<?php echo $number_of_crew ?>">
                        </div>
                      </div>

                      <button type="submit" name="ship_perticular_update" class="btn btn-success">Update</button>
                      <a href="vessel_details.php?ship_perticular=<?php echo $msl_num; ?>" class="btn btn-primary">Cancel</a>
                    </form>


                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="no-padding-top">
          <div class="container-fluid">
            <div class="row">
              
              <!-- Form Elements -->
              <div class="col-lg-12">
                <div class="block">
                  <div class="title">
                    <strong>EXPORT FORWARDINGS </strong>
                    <!-- <a 
                      onClick="javascript: return confirm('Please confirm deletion');" 
                      href="index.php?del_msl_num=<?php echo $msl_num; ?>" 
                      class="btn btn-danger btn-sm"
                       style="float: right;"
                    ><i class="bi bi-trash"></i></a> -->
                    <a href="vessel_details.php?edit=<?php echo $msl_num; ?>" class="btn btn-secondary btn-sm" style="float: right; margin-right: 10px;">
                      <i class="icon-ink"></i> <-Back
                    </a>
                  </div>

                  <div class="block-body">

                    <div class="table-responsive"> 
                      <table class="table table-dark table-sm table-custom">
                        <thead>
                          <tr>
                            <th colspan="7">Vessel Details</th>
                          </tr>
                        </thead>
                        <tbody>
                          <form method="post" action="vessel_details.php?ship_perticular=<?php echo $msl_num; ?>">
                            <input type="hidden" name="ship_perticularId" value="<?php echo $ship_perticularId; ?>">
                            <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                            <!-- 1st -->
                            <tr style="border-bottom: 1px solid white;">
                              <td colspan="7">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "export_vsl_details" ?>" style="color: white">
                                  Export Vessel Details
                                </button>
                              </td>
                            </tr>

                            <tr>
                              <th colspan="7"> Before Arrive</th>
                            </tr>
                            <tr style="border-bottom: 1px solid white;">
                              <td>
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "prepartique" ?>" style="color: white">
                                  1.Prepartique
                                </button>
                              </td>

                              <td>
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "vsl_declearation" ?>" style="color: white">
                                  2.VSL DESC
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "portigm" ?>" style="color: white">
                                  3.Port IGM
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "plantq" ?>" style="color: white">
                                  4.Plant.Q
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "po_booking" ?>" style="color: white">
                                  5.P.O Booking
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "survey_booking" ?>" style="color: white">
                                  6.Survey Booking
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-info btn-sm" name="export_vsl_forwadings" value="<?php echo "before_arrive" ?>" style="color: white">
                                  Export All
                                </button>
                              </td>
                            </tr>




                            <tr>
                              <th colspan="7"> After Arrive</th>
                            </tr>
                            <tr style="border-bottom: 1px solid white;">


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "finalEntry" ?>" style="color: white">
                                  13.Final Entry
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "pcForwading" ?>" style="color: white">
                                  28.PC Forwading
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "Stamp_PC" ?>" style="color: white">
                                  28.Stamp_PC
                                </button>
                              </td>


                              <td colspan="2">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "inctaxforwading" ?>" style="color: white">
                                  29.Inc Tax Forwading
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "Stamp_inctax" ?>" style="color: white">
                                  29.Inc Tax Stamp
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-info btn-sm" name="export_vsl_forwadings" value="<?php echo "after_arrive" ?>" style="color: white">
                                  Export All
                                </button>
                              </td>
                            </tr>




                            <tr>
                              <th colspan="7"> After Sail</th>
                            </tr>
                            <tr style="border-bottom: 1px solid white;">
                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "port_health" ?>" style="color: white">
                                  20.Port Helth
                                </button>
                              </td>


                              <td colspan="2">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "psc_submission" ?>" style="color: white">
                                  21.PHC Submission
                                </button>
                              </td>


                              <td colspan="2">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "egm_forwading" ?>" style="color: white">
                                  22.EGM Forwading
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success btn-sm" name="export_vsl_forwadings" value="<?php echo "egm_format" ?>" style="color: white">
                                  23.EGM Format
                                </button>
                              </td>


                              <td>
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-info btn-sm" name="export_vsl_forwadings" value="<?php echo "after_sail" ?>" style="color: white">
                                  Export All
                                </button>
                              </td>
                            </tr>

                            <!-- file cover section -->
                            <tr>
                              <th colspan="7"> File Cover</th>
                            </tr>

                            <tr style="border-bottom: 1px solid white;">
                              <td colspan="3">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "main_file_cover" ?>" style="color: white">
                                  Main File Cover
                                </button>
                              </td>

                              <td colspan="3">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "accounts_file_cover" ?>" style="color: white">
                                  Accounts File Cover
                                </button>
                              </td>
                              <td colspan="1">
                                <!-- <label for="inputEmail4">Msl Num</label> -->
                                <button type="submit" class="form-control btn btn-info btn-sm" name="export_vsl_forwadings" value="<?php echo "file_covers" ?>" style="color: white">
                                  Export All
                                </button>
                              </td>
                            </tr>


                            <tr>
                              <th colspan="7">Receving Doc's</th>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "arrival_perticular" ?>" style="color: white">
                                  Arrival Perticular
                                </button>
                              </td>

                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "ship_required_docs" ?>" style="color: white">
                                  Ship Required Doc's
                                </button>
                              </td>

                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "representative_letter" ?>" style="color: white">
                                  Representative Letter
                                </button>
                              </td>
                              <td>
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "qurentine" ?>" style="color: white" disabled>
                                  Qurentine
                                </button>
                              </td>
                            </tr>

                            <tr style="border-bottom: 1px solid white;">
                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "lightdues" ?>" style="color: white">
                                  Lightdues
                                </button>
                              </td>

                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "watchman_letter" ?>" style="color: white">
                                  Watchman Letter
                                </button>
                              </td>

                              <td colspan="2">
                                <button type="submit" class="form-control btn btn-success" name="export_vsl_forwadings" value="<?php echo "vendor_letter" ?>" style="color: white">
                                  Vendor Letter
                                </button>
                              </td>

                              <td>
                                <button type="submit" class="form-control btn btn-info btn-sm" name="export_vsl_forwadings" value="<?php echo "export_rcv_docs" ?>" style="color: white">
                                  Export All
                                </button>
                              </td>
                            </tr>
                          </form>
                        </tbody>
                      </table>
                    </div>


                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>


        <!-- END SHIP PERTICULAR -->
        <?php } ?>





        <!-- percentage complete -->
        <div class="modal fade" id="completepercentage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><?php echo "MV. ".$vessel_name; ?> <?php echo $rcv_date; ?></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post" action="vessel_details.php?edit=<?php echo $msl_num; ?>">
                <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>">
                <input type="hidden" name="vesselId" value="<?php echo $vesselId; ?>">
                <input type="hidden" name="vessel_name" value="<?php $vessel_name; ?>">
                <div class="modal-body">
                  <!-- 1st -->
                  <div class="form-row">
                    <?php if(empty($arrived)){ ?>
                    <div class="form-group col-md-2">
                      <label for="inputEmail4">Arrived</label>
                      <input type="text" class="form-control" name="arrived" value="<?php echo $arrived; ?>">
                    </div>
                    <?php } ?>
                    <?php if(empty($rcv_date)){ ?>
                    <div class="form-group col-md-2">
                      <label for="inputEmail4">Received(sm) <input type="checkbox" name="sameRcv" value="sameRcv" <?php echo $ckstatusSail; ?>></label>
                      <input type="text" class="form-control" name="rcv_date" value="<?php echo $rcv_date; ?>">
                    </div>
                    <?php } ?>
                    <?php if(empty($com_date)){ ?>
                    <div class="form-group col-md-2">
                      <label for="inputEmail4">Complete Date</label>
                      <input type="text" class="form-control" name="com_date" value="<?php echo $com_date; ?>">
                    </div>
                    <?php } ?>
                    <?php if(empty($sailing_date)){ ?>
                    <div class="form-group col-md-2">
                      <label for="inputEmail4">Sailed(same) <input type="checkbox" name="sameSail" value="sameSail" <?php echo $ckstatusSail; ?>></label>
                      <input type="text" class="form-control" name="sailing_date" value="<?php echo $sailing_date; ?>">
                    </div>
                    <?php } ?>
                    <?php if($ttlqtyplused<ttlcargoqty($msl_num)){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputPassword4">Outer Quantity</label>
                      <input type="number" step="any" class="form-control" name="outer_qty" value="<?php echo $outer_qty ?>">
                    </div>
                    <?php } ?>

                    <?php if($ttlqtyplused<ttlcargoqty($msl_num)){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputEmail4">Kutubdia Quantity</label>
                      <input type="number" step="any" class="form-control" name="kutubdia_qty" value="<?php echo $kutubdia_qty; ?>">
                    </div>
                    <?php } ?>
                    
                    <?php if($ttlqtyplused<ttlcargoqty($msl_num)){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputEmail4">Retention Quantity</label>
                      <input type="number" step="any" class="form-control" name="retention_qty" value="<?php echo $retention_qty; ?>">
                    </div>
                    <?php } ?>

                    <!-- <?php// echo $msl_num.exist('vessels_importer','msl_num',$msl_num); ?> -->
                    <?php if(exist("vessels_importer","msl_num = ".$msl_num." ")==0){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Impoter</label>
                      <select name="importer[]" class="form-control mb-3 mb-3 selectpicker" multiple style="background: transparent;" data-live-search="true">
                        <?php
                          $run = mysqli_query($db, "SELECT * FROM bins WHERE type = 'IMPORTER' ");
                          while ($row = mysqli_fetch_assoc($run)) {
                            $id = $row['id']; $value = $row['name'];
                            $getImporter = mysqli_query($db, "SELECT * FROM vessels_importer WHERE importer = '$id' AND msl_num = '$msl_num' ");
                            if (mysqli_num_rows($getImporter) > 0) { $selected = "selected"; }
                            else{$selected = "";}
                            echo"<option value=\"$id\" $selected>$value</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if(empty($stevedore) || $stevedore == 0){ ?>
                    <div class="form-group col-md-6">
                      <label for="inputState">Stevedore</label>
                      <select id="inputState" class="form-control search" name="stevedore">
                        <option value="<?php echo $stevedore ?>"><?php echo alldata('stevedore', $stevedore, 'name'); ?></option>
                        <?php
                          $run = mysqli_query($db, "SELECT * FROM stevedore ");
                          while ($row = mysqli_fetch_assoc($run)) {
                            $id = $row['id']; $value = $row['name'];
                            if ($id == $stevedore) { continue; }
                            echo"<option value=\"$id\">$value</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>
                    
                    <?php if(empty($row2['representative']) || $row2['representative'] == 0){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Representative</label>
                      <select id="inputState" class="form-control search" name="representative">
                        <option value="0">--Select--</option>
                        <?php
                          $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position = 'Representative' OR office_position = 'Junior Shipping Exicutive' ");
                          while ($row1 = mysqli_fetch_assoc($run1)) {
                            $id = $row1['id']; $representative_name = $row1['name'];
                            if ($repId == $id) { continue; }
                            echo"<option value=\"$id\">$representative_name</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if(empty($row2['rotation'])){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Rotation</label>
                      <input type="text" class="form-control" name="rotation" value="<?php echo $rotation ?>">
                    </div>
                    <?php } ?>

                    <?php if(empty($row2['anchor'])){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Anchorage</label>
                      <select id="inputState" class="form-control" name="anchor">
                        <?php
                          if ($anchor == "Outer") {
                            echo"
                              <option value=\"\">--Select----</option>
                              <option value=\"Outer\" selected>Outer</option>
                              <option value=\"Kutubdia\">Kutubdia</option>
                            ";
                          }
                          elseif ($anchor == "Kutubdia") {
                            echo"
                              <option value=\"\">--Select----</option>
                              <option value=\"Outer\">Outer</option>
                              <option value=\"Kutubdia\" selected>Kutubdia</option>
                            ";
                          }
                          else{
                            echo"
                              <option value=\"\">--Select----</option>
                              <option value=\"Outer\">Outer</option>
                              <option value=\"Kutubdia\">Kutubdia</option>
                            ";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>


                    <?php if($row2['survey_custom']==0){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Custom Survey</label>
                      <select id="inputState" class="form-control search" name="survey_custom">
                        <?php
                          $company_name = allData('surveycompany', $survey_custom, 'company_name');
                          if ($company_name == "") {
                            echo"<option value=\"\">--Select--</option>";
                          }else{
                        ?>
                        <option value="<?php echo $survey_custom; ?>"><?php echo $company_name; ?></option>
                        <?php }
                          $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                          while ($row1 = mysqli_fetch_assoc($run1)) {
                            $id = $row1['id']; $company_name = $row1['company_name'];
                            if ($id == $survey_custom) { continue; }
                            echo"<option value=\"$id\">$company_name</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if($row2['survey_consignee']==0){ ?>
                    <div class="form-group col-md-3">
                      <label for="inputState">Consignee Survey</label>
                      <select id="inputState" class="form-control search" name="survey_consignee">
                        <?php
                          $company_name = allData('surveycompany', $survey_consignee, 'company_name');
                          if ($company_name == "") {
                            echo"<option value=\"\">--Select--</option>";
                          }else{
                        ?>
                        <option value="<?php echo $survey_consignee; ?>"><?php echo $company_name ?></option>
                        <?php }
                          $run1 = mysqli_query($db, "SELECT * FROM surveycompany ");
                          while ($row1 = mysqli_fetch_assoc($run1)) {
                            $id = $row1['id']; $company_name = $row1['company_name'];
                            if ($id == $survey_consignee) { continue; }
                            echo"<option value=\"$id\">$company_name</option>";
                          }//echo"<option value=\"\">--Select--</option>";
                        ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if($row2['received_by']==0){ ?>
                    <div class="form-group col-md-6">
                      <label for="inputState">Received By</label>
                      <select id="inputState" class="form-control search" name="received_by">
                        <option value="<?php echo $received_by ?>"><?php echo $rcvbynm; ?></option>
                        <?php
                          $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position != 'Representative' ");
                          while ($row1 = mysqli_fetch_assoc($run1)) {
                            $id = $row1['id']; $name = $row1['name'];
                            if ($received_by == $id) { continue; }
                            echo"<option value=\"$id\">$name</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if($row2['sailed_by']==0){ ?>
                    <div class="form-group col-md-6">
                      <label for="inputState">Sailed By</label>
                      <select id="inputState" class="form-control search" name="sailed_by">
                        <option value="<?php echo $sailed_by ?>"><?php echo $slbynm; ?></option>
                        <?php
                          $run1 = mysqli_query($db, "SELECT * FROM users WHERE office_position != 'Representative' ");
                          while ($row1 = mysqli_fetch_assoc($run1)) {
                            $id = $row1['id']; $name = $row1['name'];
                            if ($sailed_by == $id) { continue; }
                            echo"<option value=\"$id\">$name</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <?php } ?>
                  </div>


                  <?php if(exist("vessels_cargo","msl_num = ".$msl_num." ")==0){ ?>
                  <hr>
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      Cargo Section
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="inputState">Select Cargo</label>
                      <select id="inputState" name="cargokey" class="form-control search">
                        <option value="">--Select--</option>
                        <?php selectOptions('cargokeys', 'name'); ?>
                      </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                      <label for="inputState">Select Loadport</label>
                      <select id="inputState" name="loadport" class="form-control search">
                        <option value="">--Select--</option>
                        <?php selectOptions('loadport', 'port_name'); ?>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="inputState">Quantity</label>
                      <input type="number" step="any" class="form-control" name="quantity">
                    </div>
                  </div>
                  
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label for="inputState">Cargo Bl Name</label>
                      <input type="text" class="form-control" name="cargo_bl_name">
                    </div>
                  </div>
                  <?php } ?>











                  <!-- vessels surveyors -->
                  <?php $listsurveyor = 0; ?>
                  <?php if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND surveyor = 0 ") || exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND surveyor = 0 ")){ ?>
                    <hr>
                    <!-- survey custom -->
                    <div class="form-row">
                      <div class="form-group col-md-12">
                        Surveyor Section
                      </div>
                    </div>
                    <!-- <input type="hidden" name="msl_num" value="<?php echo $msl_num; ?>"> -->

                    <?php if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND surveyor = 0 ")){ ?>
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND surveyor = 0 ", "id"); $party = "survey_custom";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor; ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_custom' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                    <?php } ?>






                    <?php if(exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND surveyor = 0 ")==1){ ?>
                    <!-- survey consignee -->
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND surveyor = 0 ", "id"); $party = "survey_consignee";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_consignee' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                    <?php } ?>

                  <?php } ?>


                  <!-- survey supplier -->
                  <?php if($row2['survey_supplier'] != 0 && exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND surveyor = 0 ")==1){ ?>
                    <!-- survey consignee -->
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND surveyor = 0 ", "id"); $party = "survey_supplier";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_supplier' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                  <?php } ?>


                  <!-- survey owner -->
                  <?php if($row2['survey_owner'] != 0 && exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND surveyor = 0 ")==1){ ?>
                    <!-- survey consignee -->
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND surveyor = 0 ", "id"); $party = "survey_owner";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_owner' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                  <?php } ?>


                  <!-- survey pni -->
                  <?php if($row2['survey_pni'] != 0 && exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND surveyor = 0 ")==1){ ?>
                    <!-- survey consignee -->
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND surveyor = 0 ", "id"); $party = "survey_pni";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_pni' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                  <?php } ?>


                  <!-- survey chattrer -->
                  <?php if($row2['survey_chattrer'] != 0 && exist("vessels_surveyor","msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND surveyor = 0 ")==1){ ?>
                    <!-- survey consignee -->
                    <div class="form-row">
                      <?php
                        $listsurveyor++;
                        $id = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND surveyor = 0 ", "id"); $party = "survey_chattrer";
                      ?>
                      <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor; ?>">
                      <input type="hidden" name="thisrowIdsurveyor<?php echo $listsurveyor ?>" value="<?php echo $id; ?>">
                      <div class="form-group col-md-4">
                        <label for="inputState">Party</label>
                        <input type="hidden" name="party<?php echo $listsurveyor ?>" value="<?php echo $party ?>">
                        <select id="inputState" class="form-control search" name="party" disabled>
                          <option value="<?php echo $party ?>"><?php echo $party ?></option>
                          <option value="survey_custom">Custom</option>
                          <option value="survey_consignee">Consignee</option>
                          <option value="survey_owner">Owner</option>
                          <option value="survey_pni">PNI</option>
                          <option value="survey_chattrer">Chattrer</option>
                        </select>
                      </div>

                      <?php
                        $survey_purpose = getdata("vessels_surveyor", "msl_num = ".$msl_num." AND survey_party = 'survey_chattrer' AND surveyor = 0 ", "survey_purpose");
                      ?>
                      <div class="form-group col-md-4">
                        <label for="inputState">Survey Purpose</label>
                        <select id="inputState" class="form-control search" name="survey_purpose<?php echo $listsurveyor ?>">
                          <option value="<?php echo $survey_purpose; ?>"><?php echo $survey_purpose; ?></option>
                          <option value="both">Load & Light</option>
                          <option value="Load Draft">Load Draft</option>
                          <option value="Rob">Rob</option>
                          <option value="Light Draft">Light Draft</option>
                        </select>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputState">Surviour</label>
                        <select id="inputState" class="form-control search" name="surveyorId<?php echo $listsurveyor ?>">
                          <option value="">--Select--</option>
                          <!-- <option value="<?php echo $surveyor ?>"><?php echo $surveyor_name; ?></option> -->
                          <?php selectOptions('surveyors', 'surveyor_name'); ?>
                        </select>
                      </div>
                    </div>
                    <input type="hidden" name="listsurveyor" value="<?php echo $listsurveyor ?>">
                  <?php } ?>

































                  <!-- vessels cnfs -->
                  <?php if(exist("vessels_importer","msl_num = ".$msl_num." AND importer != 0 AND cnf = 0 ")){ ?>
                    <hr>
                    <!-- survey custom -->
                    <div class="form-row">
                      <div class="form-group col-md-12">
                        C&F Section
                      </div>
                    </div>

                    <?php 
                      $listimporter = 0;
                      $sql1 = "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' AND importer != 0 AND cnf = 0 "; $run3 = mysqli_query($db, $sql1); while ($row3 = mysqli_fetch_assoc($run3)) { 
                        $importerId=$row3['importer'];$cnfId=$row3['cnf'];$idthis=$row3['id'];
                        $listimporter++;
                    ?>
                    <input type="hidden" name="thisrowIdcnf<?php echo $listimporter ?>" value="<?php echo $idthis; ?>">
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="inputState">Importer</label>
                        <input type="hidden" name="listimporter" value="<?php echo $listimporter; ?>">
                        <input type="hidden" name="importerId<?php echo $listimporter ?>" value="<?php echo $importerId ?>">
                        <select id="inputState" class="form-control selectpicker" name="importers<?php echo $listimporter; ?>" disabled>
                          <!-- <option value="<?php echo $importerId; ?>"><?php echo $importerName; ?></option> -->
                          <?php 
                            $run5 = mysqli_query($db, "SELECT * FROM vessels_importer WHERE msl_num = '$msl_num' ");
                            while ($row5 = mysqli_fetch_assoc($run5)) {
                              $thisid = $row5['id']; $imid = $row5['importer']; $cn = $row5['cnf'];

                              // select importer
                              if($cn==$cnfId && $cnfId != 0){$selected="selected";}
                              else{$selected = "";}
                              if($imid==$importerId && $importerId != 0){$selected="selected";}
                              
                              $impId = $row5['importer']; $impName = allData('bins', $impId, 'name');
                              echo "<option value=\"$impId\" $selected>$impName</option>";
                            }
                          ?>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label for="inputState">Cnf</label>
                        <select id="inputState" class="form-control search" name="cnfId<?php echo $listimporter; ?>">
                          <option value="">--Select--</option>
                          <?php selectOptions('cnf', 'name'); ?>
                        </select>
                      </div>
                    </div>
                    <?php } ?>
                  <?php } ?>




                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="percentagecomplete" class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
















        <!-- Please do not remove the backlink to us unless you support us at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
        <!-- <footer class="footer">
          <div class="footer__block block no-margin-bottom">
            <div class="container-fluid text-center">
              <p class="no-margin-bottom">2020 &copy; Multiport. Design by <a href="https://bootstrapious.com/p/bootstrap-4-dark-admin">Bootstrapious</a>.</p>
            </div>
          </div>
        </footer> -->
        <?php //include('inc/footercredit.php'); ?>
      </div>
    </div>
    <?php include('inc/footer.php'); ?>