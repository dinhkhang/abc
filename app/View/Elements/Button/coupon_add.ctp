<?php
if (empty($controller)) {
    $controller = 'Coupons';
}
if (empty($action)) {
    $action = 'add';
}
$path = array(
    'controller' => $controller,
    'action'     => $action,
    '?'          => array('objectId' => $objectId, 'objectTypeId' => $objectTypeId, 'lang_code' => $langCode)
);
$class = 'btn-primary';
if (isset($id)) {
    $class = 'btn-info';
    $path[] = $id;
}
?>
<?php if (in_array($controller . '/' . $action, AuthComponent::user('permissions'))): ?>
    <a href="<?= $this->Html->url($path) ?>" class="btn <?= $class ?>" title="<?php echo __('coupon_'.$action) ?>">
        <i class="fa fa-gift"></i>
    </a>
<?php endif; ?>