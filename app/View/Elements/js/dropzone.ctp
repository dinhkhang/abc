<?php

echo $this->start('script');
echo $this->Html->script('plugins/dropzone/dropzone');
echo $this->end();

echo $this->start('css');
echo $this->Html->css('plugins/dropzone/basic');
echo $this->Html->css('plugins/dropzone/dropzone');
echo $this->end();