<script>
    var ROOT_URL = '<?php echo Router::url('/'); ?>';

    $(function () {

        $('body').on('click', '.submit-form-edit', function () {

            var action = $(this).data('action');
            var $form_edit = $(this).closest('.form-edit').find(':input:not(.not-edit)');
            var form_data = $form_edit.serialize();

            var req = $.post(action, form_data, function (data) {

                if (data.error_code) {

                    alert(data.message);
                } else {

                    location.reload(true);
                }

            }, 'json');

            req.error(function (xhr, status, error) {

                alert("An AJAX error occured: " + status + "\nError: " + error + "\nError detail: " + xhr.responseText);
            });

            return false;
        });

        $('body').on('click', '.remove', function () {

            var $self = $(this).closest('tr');
            var $tbody = $(this).closest('tbody');
            var page = <?php echo!empty($this->params['named']['page']) ? $this->params['named']['page'] : 1 ?>;

            var choose = confirm('<?php echo __('confirm_before_delete') ?>');
            if (!choose) {

                return false;
            }
            var request = $(this).attr('href');
            $self.hide();
            var req = $.post(request, {}, function (data) {

                if (data.error_code) {

                    alert(data.message);
                    $self.show();
                } else {
                    location.reload();
                    if ($tbody.find('tr:visible').length <= 0) {

                        // thực hiện điều hướng tới trang page trước đó
                        if (page > 1) {

                            var redirect = location.href;
                            redirect = redirect.replace('page:' + page, 'page:' + (page - 1));
                            location.replace(redirect);
                        } else {

                            location.reload(true);
                        }
                    }
                }

            }, 'json');

            req.error(function (xhr, status, error) {

                alert("An AJAX error occured: " + status + "\nError: " + error + "\nError detail: " + xhr.responseText);
                $self.show();
            });

            return false;
        });

        $('.check-all').on('change', function () {

            if ($(this).prop('checked')) {

                $(this).closest('table').find('.check').prop('checked', true);
            }
            else {

                $(this).closest('table').find('.check').prop('checked', false);
            }
        });

        $('form.update-many').on('submit', function () {

            if ($('.check:checked').length <= 0) {

                return false;
            }

            var object_id = [];
            $('.check:checked').each(function () {

                object_id.push($(this).val());
            });

            $(this).find('.object_id').val(JSON.stringify(object_id));
        });

        $( ".validate-form" ).submit(function() {
            var from_date = $( "input[name=from_date]" ).val();
            var fromDate = convertToUnixTime(from_date);
            var to_date = $( "input[name=to_date]" ).val();
            var toDate = convertToUnixTime(to_date);
            var phone = $( "input[name=phone]" ).val();

            if(fromDate > toDate){
                alert("Thời gian kết thúc không được vượt quá thời gian bắt đầu.");
                return false;
            }

            var x = phone.trim('');
            if( !validateMobileNumber(x) ){
                alert("Hãy nhập số điện thoại của nhà mạng Mobifone.");
                return false;
            }

        });

        $( "#PlayerScoreHisEditFormx" ).submit(function() {
            var from_date = $( "input[name=date]" ).val();
            var fromDate = convertToUnixTime(from_date);

            if(fromDate > toDate){
                alert("Thời gian kết thúc không được vượt quá thời gian bắt đầu.");
                return false;
            }

            var x = phone.trim('');
            if( !validateMobileNumber(x) ){
                alert("Hãy nhập số điện thoại của nhà mạng Mobifone.");
                return false;
            }

        });
    });

    function convertToUnixTime(dateTimeStr){
        var arr = dateTimeStr.split(" ");
        var dateStr = arr[0];
        var timeStr = arr[1];

        var arr_2 = dateStr.split("-");
        var newDateStr = arr_2[2] + "-" + arr_2[1] + "-" + arr_2[0];
        var newStr = [newDateStr,timeStr].join(" ");

        return Date.parse(newStr);
    }

    function validateMobileNumber(mobile_number) {
        if (mobile_number.length == 0) {
            return true;
        } else {
            if(mobile_number == '' || mobile_number == '84' || mobile_number == '+84'){
                return true;
            }
            var _start = mobile_number.substring(0,1);
            var _end = mobile_number.substring(1,mobile_number.length);
            if(_start == '+'){  
                phone = _end;
            }else{
                phone = mobile_number;
            }

            if (phone.match(/^\d+/)) {
                if ( phone.match(/^(84|0)(90|93|120|121|122|126|128)\d{7}$/) ) {
                    return true;
                } 
            }

        }
        return false;
    }
</script>