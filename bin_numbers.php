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
    <?php echo $msg; ?>

    <section class="no-padding-top no-padding-bottom">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="block">

              <div class="title">
                <strong>All Bank Bin Numbers</strong>
                <!-- add vassel modal and btn -->
                <button class="btn btn-success btn-sm" style="float: right;" data-toggle="modal" data-target="#addBankBin">+ ADD</button>
                

                <!-- Modal -->
                <div class="modal fade" id="addBankBin" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Insert Bin Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <form method="post" action="bin_numbers.php">
                        <div class="modal-body">
                          
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <div class="row">
                                
                                <div class="col-md-6">
                                  <label for="exampleInputPassword1">Name</label>
                                  <input type="text" name="bank_name" class="form-control" required placeholder="BANK NAME">
                                </div>

                                <div class="col-md-6">
                                  <label for="exampleInputPassword1">Bin</label>
                                  <input type="text" name="bin_num" class="form-control" required placeholder="BIN NUMBER">
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-12">
                                  <label for="exampleInputPassword1">Type</label>
                                  <select name="type" class="form-control mb-3 mb-3">
                                    <option value="">SELECT BIN TYPE</option>
                                    <option value="BANK">BANK</option>
                                    <option value="IMPORTER">IMPORTER</option>
                                  </select>
                                </div>
                              </div><br/>

                            </div>
                          </div>
                          
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="submit" name="addBankBin" class="btn btn-primary">+ADD</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="table-responsive"> 
                <table id="example" class="table table-dark table-striped table-sm">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>BANK NAME</th>
                      <th>BIN NUMBERS</th>
                      <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php allBins(); ?>
                  </tbody>
                </table>
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


<?php
  $run3 = mysqli_query($db, "SELECT * FROM bins");
  while ($row3 = mysqli_fetch_assoc($run3)) {
    $id = $row3['id']; $name = $row3['name']; $bin = $row3['bin']; $type = $row3['type'];
?>
<div class="modal fade" id="<?php echo"editBankBin".$id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Insert Bin Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="bin_numbers.php">
        <div class="modal-body">
          
          <div class="form-group row">
            <div class="col-sm-12">
              <div class="row">
                <label for="exampleInputPassword1">Bank Name & Bin Number</label>
                <div class="col-md-6">
                  <input type="hidden" name="binId" value="<?php echo $id; ?>">
                  <input type="hidden" name="type" value="<?php echo $type; ?>">
                  <input type="text" name="bank_name" value="<?php echo $name; ?>" class="form-control" required placeholder="BANK NAME">
                </div>

                <div class="col-md-6">
                  <input type="text" name="bin_num" value="<?php echo $bin; ?>" class="form-control" required placeholder="BIN NUMBER">
                </div>
              </div><br/>

            </div>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="editBankBin" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>
<?php include('inc/footer.php'); ?>