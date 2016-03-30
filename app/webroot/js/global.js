/**
 * Created by huongnx on 12/10/2015.
 */
var imagePreview = {
    initFancybox: function(){
        $('.fancybox').fancybox({
            openEffect	: 'none',
            closeEffect	: 'none'
        });
    }
};
var Global = {
    init: function(){
        this.updateLanguage();
        $.fn.datepicker.dates['vi'] = {
            days: ["Chủ nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"],
            daysShort: ["CN", "T.2", "T.3", "T.4", "T.5", "T.6", "T.7"],
            daysMin: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            monthsShort: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            today: "Hôm nay",
            clear: "Clear",
            format: "mm/dd/yyyy",
            titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
            weekStart: 0
        };
        $(document).ajaxStart(function() {
            $('#over-lay').show();
        });
        $(document).ajaxStop(function() {
            $('#over-lay').hide();
        });
    },
    updateLanguage: function() {
        var selectLang = $('.update-lang');
        var curParam = this.getUrlVars()['lang_code'];

        if (typeof curParam == 'undefined' && selectLang.length > 0) {
            var currentSelect = selectLang.find('option:selected').val();
            Global.addParameterToURL(currentSelect);
        }

        var currentVal = selectLang.val();
        selectLang.change(function(){
            var _that = $(this);
            var selected = _that.val();
            _that.val(currentVal);
            $.confirm({
                text: "Bạn có chắc là bạn muốn chọn " + $(this).find("[value = '"+ selected +"']").text() + " ?",
                title: "Xác nhận lựa chọn",
                confirm: function() {
                    _that.val(selected);
                    Global.addParameterToURL(_that.val());
                },
                confirmButton: "Đồng ý",
                cancelButton: "Cancel",
                confirmButtonClass: "btn-danger",
                cancelButtonClass: "btn-default",
            });
        })
    },
    addParameterToURL: function (param) {
        var _url = location.href;
        var curParam = _url.split('lang_code=')[1];
        if (typeof curParam == 'undefined') {
            _url += (_url.split('?')[1] ? '&' : '?') + 'lang_code=' + param;
        } else {
            _url = _url.replace("lang_code=" + curParam, "lang_code=" + param);
        }
        window.location.href = _url;
    },
    getUrlVars: function() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }
}
$(document).ready(function() {
    imagePreview.initFancybox();
    Global.init();
})
