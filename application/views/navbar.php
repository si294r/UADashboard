            <!-- Static navbar -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">                        
                        <a class="navbar-brand" href="#">UA</a>
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
                                </ul>
                            </li>    
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle <?php echo in_array($class,['home2','subhome2','setting2']) ? 'menu-active' : '' ?>" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Jelly Pop <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo base_url('home2') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo base_url('setting2') ?>">Setting</a></li>
                                </ul>
                            </li>    
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?php echo base_url('signin/out') ?>">Signout</a></li>
                        </ul>                        
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>
