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

        <title>Setting</title>

        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/navbar.css') ?>" rel="stylesheet">

        <script src="<?php echo base_url('assets/jquery/jquery-2.2.2.min.js') ?>"></script>
        <script>
            $(document).ready(function() {
               $('#channel').change(function() {
                   location.href = '<?php echo base_url().strtolower($this->router->fetch_class()) ?>/index/' + $('#channel').val();
               }); 
            });
        </script>
    </head>
    <body>

        <div class="container">

            <!-- Static navbar -->
            <?php $this->load->view ('navbar'); ?>

            <h3>Almighty - Setting Page</h3>

            <br/>
            <form class="form-horizontal" method="POST">
                <div class="form-group">
                    <label for="channel" class="col-sm-2 control-label" style="text-align: left;">Channel</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="channel" name="channel">
                            <?php
                            foreach ($arr_channel as $row) {
                                if ($selected_channel == $row['channel']) {
                                    echo "<option value=\"{$row['channel']}\" selected>{$row['channel']}</option>";
                                } else {
                                    echo "<option value=\"{$row['channel']}\">{$row['channel']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="arpu_limit" class="col-sm-2 control-label" style="text-align: left;">ARPU Lowest Limit</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="arpu_limit" name="arpu_limit" placeholder="ARPU Lowest Limit" value="<?php echo $row_setting['arpu_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cpi_limit" class="col-sm-2 control-label" style="text-align: left;">CPI Highest Limit ($)</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="cpi_limit" name="cpi_limit" placeholder="CPI Highest Limit ($)" value="<?php echo $row_setting['cpi_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="ppu_limit" class="col-sm-2 control-label" style="text-align: left;">PPU Lowest Limit (%)</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="ppu_limit" name="ppu_limit" placeholder="PPU Lowest Limit (%)" value="<?php echo $row_setting['ppu_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="d1_limit" class="col-sm-2 control-label" style="text-align: left;">D1 Lowest Limit (%)</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="d1_limit" name="d1_limit" placeholder="D1 Lowest Limit (%)" value="<?php echo $row_setting['d1_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="d3_limit" class="col-sm-2 control-label" style="text-align: left;">D3 Lowest Limit (%)</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="d3_limit" name="d3_limit" placeholder="D3 Lowest Limit (%)" value="<?php echo $row_setting['d3_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="d7_limit" class="col-sm-2 control-label" style="text-align: left;">D7 Lowest Limit (%)</label>
                    <div class="col-sm-4">
                        <input type="number" step="any" class="form-control" id="d7_limit" name="d7_limit" placeholder="D7 Lowest Limit (%)" value="<?php echo $row_setting['d7_limit'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-default">Save</button>
                    </div>
                </div>
            </form>

        </div>

        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>

    </body>
</html>