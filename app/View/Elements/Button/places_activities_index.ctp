<?php
if (empty($controller)) {

        $controller = 'Activities';
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
<?php if (in_array($controller . '/' . $action, $permissions)): ?>
        <a href="<?php echo $action_path ?>" class="btn btn-primary" title="<?php echo __('place_activities_index_btn') ?>">
                <i class="fa fa-paper-plane-o"></i>
        </a>
<?php endif; ?>