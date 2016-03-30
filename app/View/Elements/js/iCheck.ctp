<?php
echo $this->start('script');
echo $this->Html->script('plugins/iCheck/icheck.min.js');
echo $this->end();

echo $this->start('css');
echo $this->Html->css('plugins/iCheck/custom.css');
echo $this->end();
?>
<script>
    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
