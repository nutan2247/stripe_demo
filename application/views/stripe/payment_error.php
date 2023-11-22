
    <!-- Begin page content -->
    <div class="container">
        <div class="row mt-4">
            <!-- <div class="col-sm-4"></div> -->
            <div class="col-4 mx-auto">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                    	<h4 class="card-text">Oops! Payment failed</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $this->session->flashdata('response'); ?>
                    	Transaction has failed. Click here to navigate <a href="<?php echo site_url('/'); ?>"> Homepage</a>
                    </div>
                </div>
            </div>
        </div>
    </div> 