<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/favicon-32x32.png') ?>">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Manager - Change Password</title>

        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/navbar.css') ?>" rel="stylesheet">

    </head>
    <body>

        <div class="container">

            <!-- Static navbar -->
            <?php $this->load->view('navbar'); ?>

            <h3>Change Password</h3>
            <br />
                        
            <form class="form-horizontal" method="POST">
                <div class="form-group">
                    <label for="arpu_limit" class="col-sm-2 control-label" style="text-align: left;">Current Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="curr_password" name="curr_password" 
                               placeholder="Current Password" 
                               value="<?php if (isset($_POST['curr_password'])) echo $_POST['curr_password']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="arpu_limit" class="col-sm-2 control-label" style="text-align: left;">New Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               placeholder="New Password"
                               value="<?php if (isset($_POST['new_password'])) echo $_POST['new_password']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="arpu_limit" class="col-sm-2 control-label" style="text-align: left;">Confirm New Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="new_password2" name="new_password2" 
                               placeholder="Confirm New Password"
                               value="<?php if (isset($_POST['new_password2'])) echo $_POST['new_password2']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <br/>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </div>
            </form>
            
        </div>

        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        
    </body>
</html>