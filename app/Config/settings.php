<?php

$config['S'] = [
    'Menus' => [
        [ // Blog
            'name' => __('Blog'),
            'icon' => 'fa fa-newspaper-o',
            'child' => [
                [ // Topic
                    'name' => __('topic_title'),
                    'controller' => 'Topics',
                    'action' => 'index'
                ],
                [ // Blog
                    'name' => __('blog_title'),
                    'controller' => 'Blogs',
                    'action' => 'index'
                ],
            ]
        ],
        [ // user
            'name' => __('User Manager'),
            'icon' => 'fa fa-user',
            'child' => [
                [ // User
                    'name' => __('User'),
                    'controller' => 'Users',
                    'action' => 'index'
                ],
                [ // User Group
                    'name' => __('User Group'),
                    'controller' => 'UserGroups',
                    'action' => 'index'
                ],
            ]
        ],
    ],
];
