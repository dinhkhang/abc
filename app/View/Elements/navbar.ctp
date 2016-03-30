<?php
$user = CakeSession::read('Auth.User');

// lấy ra allow_controllers của user, để ẩn hiện các phần tử menu cho đúng
$allow_controllers = $user['allow_controllers'];

// lấy về permissions của user
$permissions = $user['permissions'];
?>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> 
                    <span>
                        <img src="<?php echo Router::url('/', true) ?>/img/icon-user-default.png" class="img-circle" alt="image">
                    </span>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="clear"> 
                            <span class="block m-t-xs"> 
                                <strong class="font-bold"><?php echo $user['username'] ?></strong>
                            </span> 
                        </span> 
                    </a>
                </div>
                <div class="logo-element">
                    <?php echo Configure::read('sysconfig.App.name') ?>
                </div>
            </li>
            <?php
            echo $this->element('NavBar/common_third_with_category', array(
                'object_type' => 'Regions',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_third_with_category', array(
                'object_type' => 'Places',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_third_with_category', array(
                'object_type' => 'Tours',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_third_with_category', array(
                'object_type' => 'Restaurants',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_third_with_category', array(
                'object_type' => 'Hotels',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Banks',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Airlines',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Buses',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Taxi',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Trains',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Ships',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Hospitals',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Countries',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Events',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Guides',
            ));
            ?>
            <?php
//            echo $this->element('NavBar/common_second', array(
//                'object_type' => 'Topics',
//            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Emergencies',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Faqs',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'SplashScreens',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Slogans',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Facilities',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Notifications',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Visitors',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'ContentProviders',
            ));
            ?>
            <?php
            echo $this->element('NavBar/report');
            ?>
            
            <?php
            echo $this->element('NavBar/winner');
            ?>
            
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'QuestionCategories',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'QuestionGroups',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Players',
            ));
            ?>
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'Configurations',
            ));
            ?> 

            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'TrafficIntroductions',
            ));
            ?>
            
            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'TrafficSigns',
            ));
            ?>

            <?php
            echo $this->element('NavBar/common_second', array(
                'object_type' => 'TrafficLibraries',
            ));
            ?>

        </ul>
    </div>
</nav>