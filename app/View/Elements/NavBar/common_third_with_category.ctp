<?php

$object_type_code = strtolower($object_type);
$object_type_alias = Inflector::underscore(Inflector::singularize($object_type_code));

echo $this->element('nav-third-level', array(
    'allow_permissions' => array(
        $object_type . '/index',
        $object_type . '/add',
        'Comments_' . $object_type_code . '_index',
        'Categories_' . $object_type_code . '_index',
        'Categories_' . $object_type_code . '_add',
    ),
    'header' => array(
        'icon' => 'fa fa-pencil-square-o',
        'title' => __($object_type_alias . '_nav_title'),
        'path' => $object_type . '/index',
    ),
    'items' => array(
        array(
            'path' => 'Comments/index?object_type_code=' . $object_type_code,
            'title' => __('comment_action_title'),
        ),
        array(
            'title' => __('index_action_title'),
            'path' => $object_type . '/index',
        ),
        array(
            'title' => __('add_action_title'),
            'path' => $object_type . '/add',
        ),
        array(
            'allow_permissions' => array(
                'Categories_' . $object_type_code . '_index',
                'Categories_' . $object_type_code . '_add',
            ),
            'header' => array(
                'path' => 'Categories/index?object_type_code=' . $object_type_code,
                'title' => __('category_nav_title'),
                'permission_code' => 'Categories_' . $object_type_code . '_index',
            ),
            'items' => array(
                array(
                    'path' => 'Categories/index?object_type_code=' . $object_type_code,
                    'title' => __('index_action_title'),
                    'permission_code' => 'Categories_' . $object_type_code . '_index',
                ),
                array(
                    'path' => 'Categories/add?object_type_code=' . $object_type_code,
                    'title' => __('add_action_title'),
                    'permission_code' => 'Categories_' . $object_type_code . '_add',
                ),
            ),
        ),
    ),
));
?>