<?php
echo $this->start('script');
echo $this->Html->script('plugins/chosen/chosen.jquery');
echo $this->end();

echo $this->start('css');
echo $this->Html->css('plugins/chosen/chosen');
echo $this->end();
?>
<script>
        $(document).ready(function () {
                var config = {
                        '.chosen-select': {width: "99%"},
                        '.chosen-select-deselect': {allow_single_deselect: true, width: "99%"},
                        '.chosen-select-no-single': {disable_search_threshold: 10, width: "99%"},
                        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!', width: "99%"},
                        '.chosen-select-width': {width: "99%"}
                };
                for (var selector in config) {
                        $(selector).chosen(config[selector]);
                }
                
                
                $(".js-templating").select2({
                        tag: true,
                        templateResult: formatRepo
                });

                function formatRepo(state) {
                        if (!state.id) {
                                return state.text;
                        }
                        console.log(state.element.getAttribute('dataUrl'));
                        var $state = $(
                                '<span><img src="' + state.element.getAttribute('dataUrl') + '" class="img-thumbnail" /> ' + state.text + '</span>'
                                );
                        return $state;
                }
                ;
        });
</script>