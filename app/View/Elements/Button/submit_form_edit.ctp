<?php
if (empty($controller)) {

        $controller = Inflector::pluralize($model_name);
}
if (empty($action)) {

        $action = 'reqEdit';
}
$action_path = Router::url(array('action' => $action, 'controller' => $controller, $id));
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
        <button class="btn btn-info disabled" 
                data-model_name="<?php echo $model_name ?>" 
                data-id="<?php echo $id ?>"
                data-action="<?php echo $action_path ?>"
                >
            <i class="fa fa-save"></i>
        </button>
<?php elseif (in_array($controller . '/' . $action, $permissions)): ?>
        <button class="btn btn-info submit-form-edit" 
                data-model_name="<?php echo $model_name ?>" 
                data-id="<?php echo $id ?>"
                data-action="<?php echo $action_path ?>"
                >
            <i class="fa fa-save"></i>
        </button>
<?php endif; ?>