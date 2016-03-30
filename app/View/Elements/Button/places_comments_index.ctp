<?php
if (empty($controller)) {

        $controller = 'Comments';
}
if (empty($action)) {

        $action = 'index';
}
$action_path = Router::url(array(
            'action' => $action,
            'controller' => $controller,
            '?' => array('objectId' => $objectId, 'objectTypeId' => $objectTypeId)
        ));
if (empty($user)) {

        $user = CakeSession::read('Auth.User');
}
$permissions = $user['permissions'];
?>
<?php if (in_array($controller . '_' . $object_type_code . '_' . $action, $permissions)): ?>
        <a href="<?php echo $action_path ?>" class="btn btn-primary" title="<?php echo __('place_comments_index_btn') ?>">
                <i class="fa fa-comment"></i>
        </a>
<?php endif; ?>