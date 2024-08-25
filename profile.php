<?php include('inc/header.php'); ?>
    <div class="d-flex align-items-stretch">
      <!-- Sidebar Navigation-->
      <?php include('inc/sidebar.php'); ?>
      <!-- Sidebar Navigation end-->
      <div class="page-content">

        <div class="page-header">
          <div class="container-fluid">
            <h2 class="h5 no-margin-bottom">
              <span>Dashboard</span>
            </h2>
          </div>
        </div>
        <?php echo $msg; //include('inc/errors.php'); ?>

        <!-- add consignee & CNF -->
        <section class="no-padding-top no-padding-bottom">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="block">

                  <!-- <div class="title">
                    <strong>All Users</strong>
                  </div> -->

                  <div class="table-responsive"> 
                    <form method="post" action="<?php echo pagename().pageurl(); ?>" enctype="multipart/form-data">
                    <table id="example" class="table table-striped table-dark">
                      <thead>
                        <tr>
                          <th scope="col" class="col-4"></th>
                          <th scope="col" class="col-1"></th>
                          <th scope="col" class="col-7"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td rowspan="3" >
                            <img src="img/userimg/<?php echo $user['image']; ?>" alt="..." class="img-fluid rounded-circle">
                          </td>
                          <td>Name</td>
                          <td><input type="text" name="name" value="<?php echo $user['name']; ?>"></td>
                        </tr>
                        <tr>
                          <td>Email</td>
                          <td><input type="text" name="email" value="<?php echo $user['email']; ?>"></td>
                        </tr>
                        <tr>
                          <td>Contact</td>
                          <td><input type="text" name="contact" value="<?php echo $user['contact']; ?>"></td>
                        </tr>

                        <tr>
                          <td>
                            <input type="file" name="pp">
                          </td>
                          <td><input type="password" name="oldpass" placeholder="Old Password"></td>
                          <td><input type="password" name="newpass" placeholder="New Password"></td>
                        </tr>
                        <tr>
                          <td colspan="4" style="text-align: right;">
                            <button type="submit" class="btn btn-success" name="updateProfile" value="<?php echo $user['id']; ?>">Update</button>
                            <!-- <button type="submit" class="btn btn-danger" name="cancel" value="cancel">Cancel</button> -->
                          </td>
                        </tr>
                        <?php// allUsers(); ?>
                      </tbody>
                    </table>
                    </form>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </section>


        <!-- Please do not remove the backlink to us unless you support us at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
        <!-- <footer class="footer">
          <div class="footer__block block no-margin-bottom">
            <div class="container-fluid text-center"> 
              <p class="no-margin-bottom">2020 &copy; Multiport. Design by <a href="https://bootstrapious.com/p/bootstrap-4-dark-admin">Tafsin Sanjid Turan</a>.</p>
            </div>
          </div>
        </footer> -->
        <?php include('inc/footercredit.php'); ?>
      </div>
    </div>
    <?php include('inc/footer.php'); ?>