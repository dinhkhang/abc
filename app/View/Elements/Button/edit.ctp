<?php
if (empty($controller)) {

        $controller = Inflector::pluralize($model_name);
}
if (empty($action)) {

        $action = 'edit';
}
$action_path = isset($location_id) 
             ? Router::url(array('action' => $action, 'controller' => $controller, $id, $location_id)) 
             : Router::url(array('action' => $action, 'controller' => $controller, $id));
// xác định nếu user nhập content, thì khi content ở trạng thái public thì ẩn đi nút xóa
if (empty($user)) {

        $user = CakeSession::read('Auth.User');
}
$permissions = $user['permissions'];
?>
<?php if (in_array($controller . '/' . $action, $permissions)): ?>
        <a href="<?php echo $action_path ?>" class="btn btn-primary" title="<?= __($action) ?>">
                <i class="fa fa-<?php echo $action == 'edit' ? $action : 'files-o' ?>"></i>
        </a>
<?php endif; ?>