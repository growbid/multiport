      <?php $id = $_SESSION['id']; ?>
      <nav id="sidebar">
        <!-- Sidebar Header-->
        <div class="sidebar-header d-flex align-items-center">
          <div class="avatar"><img src="img/userimg/<?php echo allData('users', $id, 'image'); ?>" alt="..." class="img-fluid rounded-circle"></div>
          <div class="title">
            <a href="profile.php?userid=<?php echo $id; ?>"><h1 class="h5"><?php echo allData('users', $id, 'name'); ?></h1></a>
            <p><?php echo allData('users', $id, 'office_position'); ?></p>
          </div>
        </div>
        <!-- Sidebar Navidation Menus--><span class="heading">Main</span>
        <ul class="list-unstyled">
          <li class="active"><a href="index.php"> <i class="icon-home"></i>Home </a></li>
          <li><a href="#exampledropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-windows"></i>3rd parties </a>
            <ul id="exampledropdownDropdown" class="collapse list-unstyled ">
              <li><a href="3rd_parties.php?page=cnf">CNF</a></li>
              <li><a href="3rd_parties.php?page=consignee">CONSIGNEE</a></li>
              <li><a href="3rd_parties.php?page=surveyors">SURVEYORS</a></li>
              <li><a href="3rd_parties.php?page=surveycompany">SURVEY COMPANY</a></li>
              <li><a href="3rd_parties.php?page=stevedore">STEVEDORE</a></li>
              <li><a href="3rd_parties.php?page=agents">AGENTS</a></li>
              <!-- <li><a href="3rd_parties.php?page=others">OTHERS</a></li> -->
            </ul>
          </li>

          <li><a href="#othersDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-padnote"></i>Others </a>
            <ul id="othersDropdown" class="collapse list-unstyled ">
              <!-- <li><a href="others_adds.php?page=remarks">Remarks</a></li> -->
              <li><a href="others_adds.php?page=binNumbers">Bin Numbers</a></li>
              <li><a href="others_adds.php?page=cargoKeys">Cargo Keys</a></li>
              <li><a href="3rd_parties.php?page=loadport">Load Port</a></li>
              <li><a href="others_adds.php?page=test">Test Page</a></li>
              <!-- <li><a href="3rd_parties.php?page=others">OTHERS</a></li> -->
            </ul>
          </li>

          <li><a href="#exampledropdownDropdownTWO" aria-expanded="false" data-toggle="collapse"> <i class="icon-windows"></i>Search Accordings </a>
            <ul id="exampledropdownDropdownTWO" class="collapse list-unstyled ">
              <li><a href="dataSearch.php?page=consigneeAndCargo">CONSIGNEE & CARGO WISE</a></li>
              <li><a href="dataSearch.php?page=stevedore">CARGO WISE</a></li>
              <li><a href="dataSearch.php?page=stevedore">STEVEDORE WISE</a></li>
              <li><a href="dataSearch.php?page=representative">REPRESENTATIVE WISE</a></li>
            </ul>
          </li>
          <li><a href="users.php"> <i class="icon-user"></i>Users </a></li>
          <li><a href="databackups.php"> <i class="icon-paper-and-pencil"></i>Data Backups </a></li>
          <li><a href="bin_numbers.php"> <i class="icon-settings-1"></i>Access Controls </a></li>
          <li><a href="remarks.php"> <i class="icon-padnote"></i>Remarks </a></li>
          <!-- <li><a href="tables.html"> <i class="icon-grid"></i>Tables </a></li> -->
          <!-- <li><a href="charts.html"> <i class="fa fa-bar-chart"></i>Charts </a></li> -->
          <!-- <li><a href="forms.html"> <i class="icon-padnote"></i>Forms </a></li> -->
          <!-- <li><a href="login.html"> <i class="icon-logout"></i>Login page </a></li> -->
        </ul>
        <span class="heading">Extras</span>
        <ul class="list-unstyled">
          <li>
            <a href="prizebond.php"> 
              <i class="icon-line-chart"></i>Prize Bonds 
            </a>
          </li>
          <?php if(isset($_SESSION['email']) && $_SESSION['email'] == 'skturan2405@gmail.com'){ ?>  
          <li>
            <a href="index.php"> 
              <i class="icon-line-chart"></i>Super Admin 
            </a>
          </li>
          <?php } ?>
          
          <li><a href="icons-reference/icons-reference.html"> <i class="icon-line-chart"></i>Icons </a></li>
          <!-- <li> <a href="#"> <i class="icon-settings"></i>Demo </a></li>
          <li> <a href="#"> <i class="icon-writing-whiteboard"></i>Demo </a></li>
          <li> <a href="#"> <i class="icon-chart"></i>Demo </a></li> -->
        </ul>
      </nav>