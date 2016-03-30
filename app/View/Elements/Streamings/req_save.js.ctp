<script>

    $(function () {

        // khi model tắt thực hiện tắt player
        $('body').on('hidden.bs.modal', '.player', function () {

            var type = $(this).data('type');
            $(this).find(type).get(0).pause();
        });

        $('body').on('click', '.streaming-package-insert', function () {

            var request = '<?php echo $this->Html->url(array('controller' => 'Streamings', 'action' => 'reqSaveInput')) ?>';
            var req = $.get(request, {}, function (data) {

                $('.panel-package-container').append(data);
            });
            req.error(function (xhr, status, error) {

                alert("An AJAX error occured: " + status + "\nError: " + error + "\nError detail: " + xhr.responseText);
            });

        });

        $('body').on('click', '.panel-input-remove', function () {

            $(this).closest('.panel').remove();
        });

        $('body').on('change', '.streaming-file-path', function () {

            var $panel = $(this).closest('.panel');
            var unique = $panel.data('unique');
            var file_path = $(this).val();
            $panel.find('.streaming-panel-title').text(file_path);
            var request = '<?php echo $this->Html->url(array('controller' => 'Streamings', 'action' => 'reqFileUrls')) ?>';
            var req = $.get(request, {file_path: file_path, unique: unique}, function (data) {

                $panel.find('.streaming-file-url').html(data);
            });
            req.error(function (xhr, status, error) {

                alert("An AJAX error occured: " + status + "\nError: " + error + "\nError detail: " + xhr.responseText);
            });
        });

//        $('body').on('click', '.modal-ajax', function () {
//
//            var $target = $($(this).attr('href'));
//            $target.modal('show');
//        });
    });
</script>