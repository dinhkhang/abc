<?php
if (empty($controller)) {

        $controller = Inflector::pluralize($model_name);
}
if (empty($action)) {

        $action = 'reqDelete';
}
$action_path = Router::url(array('action' => $action, 'controller' => $controller, $id, '?' => $query));

// xác định nếu user nhập content, thì khi content ở trạng thái public thì ẩn đi nút xóa
if (empty($user)) {

        $user = CakeSession::read('Auth.User');
}
$user_type = $user['type'];
$permissions = $user['permissions'];
?>
<?php
if (
        isset($content_status) &&
        $content_status == Configure::read('sysconfig.App.constants.STATUS_APPROVED') &&
        $user_type == 'CONTENT_EDITOR'
):
        ?>
        <a href="#" class="btn btn-danger disabled" title="<?php echo __('delete_btn') ?>">
            <i class="fa fa-trash"></i>
        </a>
<?php elseif (in_array($controller . '/' . $action, $permissions)): ?>
        <a href="<?php echo $action_path ?>" class="btn btn-danger remove" title="<?php echo __('delete_btn') ?>">
            <i class="fa fa-trash"></i>
        </a>
<?php endif; ?>