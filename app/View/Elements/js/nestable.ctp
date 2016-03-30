<?php

echo $this->start('script');
echo $this->Html->script('plugins/nestable/jquery.nestable');
?>
<script>
        $(function () {

            var onChange = function (e) {
                var list = e.length ? e : $(e.target);
                var serialize = JSON.stringify(list.nestable('serialize'));
                $('.serialize').val(serialize);
            };
            // activate Nestable for list 1
            $('#nestable2').nestable({
                group: 1,
                maxDepth: 2
            }).on('change', onChange);
            $('#nestable2').trigger('change');

            $('#nestable-menu').on('click', function (e) {
                var target = $(e.target),
                        action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });
        });
</script>
<?php

echo $this->end();
