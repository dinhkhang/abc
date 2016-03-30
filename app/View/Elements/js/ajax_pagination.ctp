<?php

$this->start('script');
?>
<script>
	$(function () {

		$('body').on('click', '.pagination-container span:not(.active)', function () {

			var $link = $(this).find('a');
			var request = $link.attr('href');
			var $ajax_target = $($(this).closest('.pagination-container').data('ajax_target'));
			$.get(request, {}, function (data) {

				$ajax_target.html(data);
			});

			return false;
		});
	});
</script>
<?php

$this->end();
