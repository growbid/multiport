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

                  <div class="title">
                    <strong>All Users</strong>
                    <!-- modal add -->
                    <a href="#" class="btn btn-success" style="float: right;" data-toggle="modal" data-target="#addUsers">
                      +ADD
                    </a>
                    <?php// include('inc/errors.php'); ?>

                    <!-- Modal -->
                    <div class="modal fade" id="addUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Users info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>

                          <form method="post" action="users.php">
                            <div class="modal-body">
                                <div class="form-group">
                                  <label for="exampleInputEmail1">Name</label>
                                  <input type="text" name="name" class="form-control" placeholder="Name" required>
                                </div>

                                <div class="form-group">
                                  <label for="exampleInputEmail1">Designation</label>
                                  <select name="office_position" class="form-control mb-3 mb-3" required>
                                    <option value="">--SELECT--</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Assistant Manager">Assistant Manager</option>
                                    <option value="Accountant">Accountant</option>
                                    <option value="Shipping Exicutive">Shipping Exicutive</option>
                                    <option value="Junior Shipping Exicutive">Junior Exicutive</option>
                                    <option value="Representative">Representative</option>
                                  </select>
                                </div>

                                <div class="form-group">
                                  <label for="exampleInputEmail1">Email</label>
                                  <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>

                                <div class="form-group">
                                  <label for="exampleInputEmail1">Contact</label>
                                  <input type="text" name="contact" class="form-control" placeholder="Contact">
                                </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="addUsers" class="btn btn-primary">Submit</button>
                            </div>
                          </form>

                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive"> 
                    <table id="example" class="table table-striped table-dark">
                      <thead>
                        <tr>
                          <th scope="col">Img</th>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Contact</th>
                          <th scope="col" style="text-align: center;">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php allUsers(); ?>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
          </div>


          <?php
            $run3 = mysqli_query($db, "SELECT * FROM bins WHERE type = 'IMPORTER' ");
            while ($row3 = mysqli_fetch_assoc($run3)) {
              $id = $row3['id']; $name = $row3['name'];  $binnumber = $row3['bin'];
          ?>
          <!-- Consignee Edit Modal -->
          <div class="modal fade" id="<?php echo"editConsignee".$id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Edit Consignee info</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <form method="post" action="3rd_parties.php?page=consignee">
                  <input type="hidden" name="consigneeId" value="<?php echo $id; ?>">
                  <div class="modal-body">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Consignee</label>
                        <input type="text" name="consigneeName" value="<?php echo $name; ?>" class="form-control" placeholder="Consignee Name">
                      </div>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Bin Number</label>
                        <input type="text" name="binnumber" value="<?php echo $binnumber; ?>" class="form-control" placeholder="Bin Number">
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="editConsignee" class="btn btn-primary">Submit</button>
                  </div>
                </form>

              </div>
            </div>
          </div>
          <?php } ?>
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