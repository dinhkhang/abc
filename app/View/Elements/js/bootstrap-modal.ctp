<?php

$this->start('script');
echo $this->Html->script('plugins/bootstrap-modal/bootstrap-modal');
echo $this->Html->script('plugins/bootstrap-modal/bootstrap-modalmanager');
$this->end();

$this->start('css');
echo $this->Html->css('plugins/bootstrap-modal/bootstrap-modal');
echo $this->Html->css('plugins/bootstrap-modal/bootstrap-modal-bs3patch');
$this->end();
