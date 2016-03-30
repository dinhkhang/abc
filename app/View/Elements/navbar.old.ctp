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
            echo $this->element('nav-third-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'active_action' => $active_action,
                'level' => array(
                    'controller' => array(
                        'Regions', 'Categories',
                    ),
                    'header' => array(
                        'controller' => 'Regions',
                        'action' => 'index',
                        'icon' => 'fa fa-pencil-square-o',
                        'title' => __('region_nav_title'),
                    ),
                    'items' => array(
                        array(
                            'controller' => 'Regions',
                            'action' => 'index',
                            'title' => __('index_action_title'),
                        ),
                        array(
                            'controller' => 'Regions',
                            'action' => 'add',
                            'title' => __('add_action_title'),
                        ),
                        array(
                            'controller' => array(
                                'Categories',
                            ),
                            'header' => array(
                                'controller' => 'Categories',
                                'action' => 'index',
                                'title' => __('category_nav_title'),
                                '?' => array(
                                    'object_type_id' => $object_types['regions'],
                                ),
                            ),
                            'items' => array(
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'index',
                                    'title' => __('index_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['regions'],
                                    ),
                                ),
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'add',
                                    'title' => __('add_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['regions'],
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-third-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'active_action' => $active_action,
                'level' => array(
                    'controller' => array(
                        'Places', 'Categories',
                    ),
                    'header' => array(
                        'controller' => 'Places',
                        'action' => 'index',
                        'icon' => 'fa fa-pencil-square-o',
                        'title' => __('place_nav_title'),
                    ),
                    'items' => array(
                        array(
                            'controller' => 'Comments',
                            'action' => 'index',
                            'title' => __('comment_action_title'),
                            '?' => array(
                                'objectTypeId' => $object_types['places'],
                            ),
                        ),
                        array(
                            'controller' => 'Places',
                            'action' => 'index',
                            'title' => __('index_action_title'),
                        ),
                        array(
                            'controller' => 'Places',
                            'action' => 'add',
                            'title' => __('add_action_title'),
                        ),
                        array(
                            'controller' => array(
                                'Categories',
                            ),
                            'header' => array(
                                'controller' => 'Categories',
                                'action' => 'index',
                                'title' => __('category_nav_title'),
                                '?' => array(
                                    'object_type_id' => $object_types['places'],
                                ),
                            ),
                            'items' => array(
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'index',
                                    'title' => __('index_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['places'],
                                    ),
                                ),
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'add',
                                    'title' => __('add_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['places'],
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-third-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'active_action' => $active_action,
                'level' => array(
                    'controller' => array(
                        'Tours', 'Categories',
                    ),
                    'header' => array(
                        'controller' => 'Tours',
                        'action' => 'index',
                        'icon' => 'fa fa-pencil-square-o',
                        'title' => __('tour_nav_title'),
                    ),
                    'items' => array(
                        array(
                            'controller' => 'Comments',
                            'action' => 'index',
                            'title' => __('comment_action_title'),
                            '?' => array(
                                'objectTypeId' => $object_types['tours'],
                            ),
                        ),
                        array(
                            'controller' => 'Tours',
                            'action' => 'index',
                            'title' => __('index_action_title'),
                        ),
                        array(
                            'controller' => 'Tours',
                            'action' => 'add',
                            'title' => __('add_action_title'),
                        ),
                        array(
                            'controller' => array(
                                'Categories',
                            ),
                            'header' => array(
                                'controller' => 'Categories',
                                'action' => 'index',
                                'title' => __('category_nav_title'),
                                '?' => array(
                                    'object_type_id' => $object_types['tours'],
                                ),
                            ),
                            'items' => array(
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'index',
                                    'title' => __('index_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['tours'],
                                    ),
                                ),
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'add',
                                    'title' => __('add_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['tours'],
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Restaurants',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('restaurant_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            echo $this->element('nav-third-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'active_action' => $active_action,
                'level' => array(
                    'controller' => array(
                        'Restaurants', 'Categories',
                    ),
                    'header' => array(
                        'controller' => 'Restaurants',
                        'action' => 'index',
                        'icon' => 'fa fa-pencil-square-o',
                        'title' => __('restaurant_nav_title'),
                    ),
                    'items' => array(
                        array(
                            'controller' => 'Comments',
                            'action' => 'index',
                            'title' => __('comment_action_title'),
                            '?' => array(
                                'objectTypeId' => $object_types['restaurants'],
                            ),
                        ),
                        array(
                            'controller' => 'Restaurants',
                            'action' => 'index',
                            'title' => __('index_action_title'),
                        ),
                        array(
                            'controller' => 'Restaurants',
                            'action' => 'add',
                            'title' => __('add_action_title'),
                        ),
                        array(
                            'controller' => array(
                                'Categories',
                            ),
                            'header' => array(
                                'controller' => 'Categories',
                                'action' => 'index',
                                'title' => __('category_nav_title'),
                                '?' => array(
                                    'object_type_id' => $object_types['restaurants'],
                                ),
                            ),
                            'items' => array(
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'index',
                                    'title' => __('index_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['restaurants'],
                                    ),
                                ),
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'add',
                                    'title' => __('add_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['restaurants'],
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Restaurants',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('restaurant_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            echo $this->element('nav-third-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'active_action' => $active_action,
                'level' => array(
                    'controller' => array(
                        'Restaurants', 'Categories',
                    ),
                    'header' => array(
                        'controller' => 'Hotels',
                        'action' => 'index',
                        'icon' => 'fa fa-pencil-square-o',
                        'title' => __('hotel_nav_title'),
                    ),
                    'items' => array(
                        array(
                            'controller' => 'Comments',
                            'action' => 'index',
                            'title' => __('comment_action_title'),
                            '?' => array(
                                'objectTypeId' => $object_types['hotels'],
                            ),
                        ),
                        array(
                            'controller' => 'Hotels',
                            'action' => 'index',
                            'title' => __('index_action_title'),
                        ),
                        array(
                            'controller' => 'Hotels',
                            'action' => 'add',
                            'title' => __('add_action_title'),
                        ),
                        array(
                            'controller' => array(
                                'Categories',
                            ),
                            'header' => array(
                                'controller' => 'Categories',
                                'action' => 'index',
                                'title' => __('category_nav_title'),
                                '?' => array(
                                    'object_type_id' => $object_types['hotels'],
                                ),
                            ),
                            'items' => array(
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'index',
                                    'title' => __('index_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['hotels'],
                                    ),
                                ),
                                array(
                                    'controller' => 'Categories',
                                    'action' => 'add',
                                    'title' => __('add_action_title'),
                                    '?' => array(
                                        'object_type_id' => $object_types['hotels'],
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Banks',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('bank_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Airlines',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('airline_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Buses',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('bus_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Taxi',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('taxi_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Trains',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('train_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Ships',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('ship_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Activities',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('activity_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Promotions',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('promotion_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Coupons',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('coupon_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Hospitals',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('hospital_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Countries',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('country_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Locations',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('location_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'News',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('new_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Events',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('event_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Guides',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('guide_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Tips',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('tip_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Topics',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('topic_nav_title'),
                    '?' => array(
                        'object_type_id' => '5550246fa37d73a40b000029',
                    ),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                        '?' => array(
                            'object_type_id' => '5550246fa37d73a40b000029',
                        ),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                        '?' => array(
                            'object_type_id' => '5550246fa37d73a40b000029',
                        ),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Emergencies',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('emergency_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Faqs',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('faq_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'SplashScreens',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('splash_screen_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?> 
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Slogans',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('slogan_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?> 
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Facilities',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('facility_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?> 
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Notifications',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('notification_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'Visitors',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('visitor_nav_title')
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title')
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title')
                    ),
                ),
            ));
            ?>
            <?php
            echo $this->element('nav-second-level', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
                'controller' => 'ContentProviders',
                'level1' => array(
                    'action' => 'index',
                    'icon' => 'fa fa-pencil-square-o',
                    'title' => __('content_provider_nav_title'),
                ),
                'level2' => array(
                    array(
                        'action' => 'index',
                        'title' => __('index_action_title'),
                    ),
                    array(
                        'action' => 'add',
                        'title' => __('add_action_title'),
                    ),
                ),
            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'Distributors',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('distributor_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'DistributionChannels',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('distribution_channel_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'ServiceProviders',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('service_provider_nav_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
//            echo $this->element('nav-second-level', array(
//                'allow_controllers' => $allow_controllers,
//                'permissions' => $permissions,
//                'active_controller' => $active_controller,
//                'controller' => 'PlatformProviders',
//                'level1' => array(
//                    'action' => 'index',
//                    'icon' => 'fa fa-pencil-square-o',
//                    'title' => __('platform_provider_title'),
//                ),
//                'level2' => array(
//                    array(
//                        'action' => 'index',
//                        'title' => __('index_action_title'),
//                    ),
//                    array(
//                        'action' => 'add',
//                        'title' => __('add_action_title'),
//                    ),
//                ),
//            ));
            ?>
            <?php
            echo $this->element('NavBar/report', array(
                'allow_controllers' => $allow_controllers,
                'permissions' => $permissions,
                'active_controller' => $active_controller,
            ));
            ?>
        </ul>
    </div>
</nav>