<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> 
                    <span>
                        <img src="<?php echo Router::url('/', true) ?>/img/icon-user-default.png" class="img-circle" alt="image">
                    </span>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="clear"> 
                            <span class="block m-t-xs"> 
                                <strong class="font-bold"><?php echo AuthComponent::user('username') ?></strong>
                            </span> 
                        </span> 
                    </a>
                </div>
                <div class="logo-element">
                    <?php echo Configure::read('sysconfig.App.name') ?>
                </div>
            </li>
            <?php
            if(isset($menus)){
                foreach ($menus as $name => $menu):
                    $arrow = !is_array($menu['url']) ? '<span class="fa arrow"></span>' : null;
                ?>
                    <li>
                        <?= $this->Html->link('<i class="'.$menu['icon'].'"></i><span class="nav-label">'.$name.'</span>'.$arrow, $menu['url'], ['escape' => false]) ?>
                        <?php if(isset($menu['child'])):?>
                            <ul class="nav nav-second-level collapse">
                                <?php
                                foreach ($menu['child'] as $nameChild => $child):
                                    $classActive = '';
                                    if (isset($this->request->query)) {
                                        if($this->request->controller == $child['url']['controller'] && $this->request->action == $child['url']['action'] && ($this->request->query == $child['url']['?'] || !$child['url']['?'])) {
                                            $classActive = 'active';
                                        }
                                    } else {
                                        if($this->request->controller == $child['url']['controller'] && $this->request->action == $child['url']['action']) {
                                            $classActive = 'active';
                                        }
                                    }
                                    ?>
                                    <li class="<?= $classActive?>">
                                        <?= $this->Html->link($nameChild, $child['url']) ?>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        <?php endif;?>
                    </li>
                <?php
                endforeach;
            }?>
        </ul>
    </div>
</nav>
<script>
    $(document).ready(function(){
        $('ul.nav-second-level').each(function(){
            if($(this).find('.active').length) {
                $(this).addClass('in').parents('li').addClass('active');
            }
        })
    });
</script>