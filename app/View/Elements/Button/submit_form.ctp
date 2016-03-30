<?php
if (empty($user)) {

        $user = CakeSession::read('Auth.User');
}
$user_type = $user['type'];

$default_content_status = isset($this->data[$model_name]['status']) ?
        $this->data[$model_name]['status'] : '';
$content_status = isset($content_status) ?
        $content_status : $default_content_status;
?>
<?php
if (
        $user_type == 'CONTENT_EDITOR' &&
        $content_status == Configure::read('sysconfig.App.constants.STATUS_APPROVED')
):
        ?>
        <button type="button" class="btn btn-primary disabled">
            <i class="fa fa-save"></i> <span><?php echo __('save_btn') ?></span> 
        </button>
<?php else: ?>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> <span><?php echo __('save_btn') ?></span> 
        </button>
<?php endif; ?>