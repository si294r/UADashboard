            <!-- Static navbar -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">                        
                        <a class="navbar-brand navbar-hi-user" href="javascript:">Hi, <?php echo $_SESSION['signin']['username'] ?></a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <?php $class = strtolower($this->router->fetch_class()); ?>
<!--                            <li class="<?php echo $class == 'home' ? 'active' : '' ?>">
                                <a href="<?php echo base_url('home') ?>">Dashboard</a>
                            </li>
                            <li class="<?php echo $class == 'setting' ? 'active' : '' ?>">
                                <a href="<?php echo base_url('setting') ?>">Setting</a>
                            </li>-->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle <?php echo in_array($class,['home','subhome','setting']) ? 'menu-active' : '' ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Billionaire <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('home') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo base_url('setting') ?>">Setting</a></li>
                                    <li><a href="<?php echo base_url('daily_report') ?>">Daily Report</a></li>
                                </ul>
                            </li>    
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle <?php echo in_array($class,['home2','subhome2','setting2']) ? 'menu-active' : '' ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Jelly Pop <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('home2') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo base_url('setting2') ?>">Setting</a></li>
                                </ul>
                            </li>    
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle <?php echo in_array($class,['home3','subhome3','setting3']) ? 'menu-active' : '' ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Almighty <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('home3') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo base_url('setting3') ?>">Setting</a></li>
                                    <li><a href="<?php echo base_url('daily_report3') ?>">Daily Report</a></li>
                                </ul>
                            </li>    
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle <?php echo in_array($class,['home4','subhome4','setting4']) ? 'menu-active' : '' ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Almighty 1.5<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('home4') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo base_url('setting4') ?>">Setting</a></li>
                                    <li><a href="<?php echo base_url('daily_report4') ?>">Daily Report</a></li>
                                </ul>
                            </li>    
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <?php if (isset($_SESSION['signin']['username']) && $_SESSION['signin']['username'] != 'admin') { ?>
                            <li class="<?php 
                                if ($class == 'signin' && $this->router->fetch_method() == 'change_password') { 
                                    echo 'active'; 
                                }
                                ?>">
                                <a href="<?php echo base_url('signin/change_password') ?>">Change Password</a>
                            </li>
                            <?php } ?>
                            <li><a href="<?php echo base_url('signin/out') ?>">Signout</a></li>
                        </ul>                        
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>

            <?php if (isset($alert) && $alert != '') { ?>
            <div class="alert alert-<?php echo $alert_type; ?>" role="alert"><?php echo $alert; ?></div>
            <?php } ?>
            
