<?php
if (!defined("DEFAULT_REGION_CODE")) {
    define("DEFAULT_REGION_CODE", "thudo");
}
$config['sysconfig'] = array(
    'App' => array(
        'base_package' => array(
            'XSMB'  =>  'XSMB ngày',
            'XSMB7' =>  'XSMB tuần',
            'XSMT'  =>  'XSMT ngày',
            'XSMT7' =>  'XSMT tuần',
            'XSMN'  =>  'XSMN ngày',
            'XSMN7' =>  'XSMN tuần'
        ),
        'winner_status' => array(
            0 => __('unknown'),
            1 => __('activity_nav_title'),
            2 => 'Hủy',
            3 =>'Hủy',
            4 => 'Hủy',
            5 => 'Hủy',
            6 => 'Hủy',
        ),
        'type_winner' => array(
            0 => __('win_of_day'),
            1 => __('win_of_week'),
            2 => __('win_of_month'),
        ),
        'default_package_search'    =>  'XSMB',
        'status' => array(
            -1 => __('status_rejected'),
            0 => __('status_hidden'),
            1 => __('status_wait_review'),
            2 => __('status_approved'),
        ),
        'status_full' => array(
            -1 => __('status_rejected'),
            0 => __('status_hidden'),
            1 => __('status_wait_review'),
            2 => __('status_approved'),
            3 => __('status_schedule'),
        ),
        'name' => 'XOSO VIETNAM',
        'max_file_size_upload' => 5 * 1000 * 1000,
        'constants' => array(
            'STATUS_APPROVED' => 2,
            'STATUS_FILE_UPLOAD_TO_TMP' => 0,
            'STATUS_FILE_UPLOAD_COMPLETED' => 1,
            'STATUS_ACTIVE' => 1,
            'STATUS_WAIT_REVIEW' => 1,
            'STATUS_HIDDEN' => 0,
            'STATUS_DELETE' => -1,
            'STATUS_SCHEDULE' => 3,
        ),
        'search_momt_status' => array(
            '0' => __('Không thành công'),
            '1' => __('Thành công'),
            '2' => __('Lỗi'),
        ),
        'search_action' => array(
            'DANG_KY' => __('charge_registered'),
            'DANG_KY_CMS' => __('Đăng ký qua CMS'),
            'DANG_KY_CSKH' => __('Đăng ký qua trang CSKH'),
            'HUY' => __('Hủy'),
            'HUY_CMS' => __('Hủy qua CMS'),
            'HUY_CSKH' => __('Hủy qua trang CSKH'),
            'MUA' => __('Mua'),
            'TRA_LOI' => __('Trả lời'),
            'CHOI' => __('Chơi'),
            'BO_QUA' => __('Bỏ qua'), //TIẾP
            'CHUYEN' => __('Chuyển'),
            'HUONG_DAN' => __('Hướng dẫn'),
            'XEM_KET_QUA' => __('Xem kết quả'),
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => __('Khác'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('Tra điểm'),
//          'CH' => __('charge_get_question'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
        // move from wap
        ),
        'charge_status' => array(
            0 => __('charge_fail'),
            1 => __('charge_success'),
            2 => __('Lỗi nhà mạng'),
        ),
        'charge_action' => array(
            'DK' => __('charge_registered'),
            'DANG_KY' => __('charge_registered'),
            'DANG_KY_CMS' => __('charge_registered'),
            'GH' => __('charge_renew'),
            'HUY' => __('charge_unregistered'),
            'HUY_CMS' => __('charge_unregistered'),
            'HUY_CSKH' => __('Hủy từ CSKH'),
            'MUA' => __('charge_buy_question'),
            'GIA_HAN' => __('Gia hạn'),
            'TL' => __('charge_answer_question'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('charge_view_point'),
//          'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
        ),
        'file_types' => array(
            'banner',
            'logo',
            'icon',
            'thumbnails',
            'screen_shots',
            'binary',
            'text',
            'video',
            'audio',
            'trailer',
        ),
        'data_file_root' => 'data_files',
        'video_types' => array(
            'mp4',
            'webm',
            'ogv',
        ),
        'currency_code' => array(
            'VND' => 'VND',
            'USD' => 'USD'
        ),
        'video_upload_types' => 'mp4|ogg',
        'max_video_file_size_upload' => 200 * 1000 * 1000, // 200mb
        'GeoJSON_type' => 'Point', // ki?u GeoJson m?c d?nh du?c s? d?ng trong "loc" field
        'languages' => ['vi' => __('Vietnamese'), 'en' => __('English')],
        'token_key' => '',
        'token_iss' => '',
        'token_aud' => '',
        'token_exp' => '+2 months',
        'standard_rate' => array(
            '' => '---',
            1 => '1 sao',
            2 => '2 sao',
            3 => '3 sao',
            4 => '4 sao',
            5 => '5 sao',
        ),
    ),
    'REGION'=>array(
        'status' => array(
            -1 => __('status_rejected'),
            1 => __('status_wait_review'),
            2 => __('status_approved'),
        ),
        'weekly'=>array(
            1=>'Thứ 2',
            2=>'Thứ 3',
            3=>'Thứ 4',
            4=>'Thứ 5',
            5=>'Thứ 6',
            6=>'Thứ 7',
            0=>'Chủ nhật',

        ),
        'code'=>array(
            'mienbac'=>'mienbac',
            'miennam'=>'miennam',
            'mientrung'=>'mientrung',
        )
    ),
    'Sub'=>array(
      'status'=>array(
          2=> 'Chưa được so sánh',
          0=> 'Không khớp với kết quả',
          1=> 'Trùng khớp với kết quả'
      )  ,
    ),
    'Menus'=>array(
        'status'=>array(
          '1'=>'Bật',
            '0'=>'Tắt'
        ),
    ),
    'KPI_EMAIL' => array(
        'chart_base_url' => 'data_files/kpi_report/',
        'img_name' => array(
            'revenue' => 'monthly_kpi_report_revenue_DATE.png',
            'user' => 'monthly_kpi_report_user_DATE.png',
            'subscriber' => 'monthly_kpi_report_subscriber_DATE.png',
            'referer' => 'monthly_kpi_report_referer_DATE.png',
        ),
        'contacts' => array(
            'be.phan@mobifone.vn' => 'be.phan@mobifone.vn',
            'thu.hoangthanh@mobifone.vn' => 'thu.hoangthanh@mobifone.vn',
        ),
        'contacts_bcc' => array(
            'tungpt@namviet-corp.vn' => 'tungpt@namviet-corp.vn',
//            'nhatpk@namviet-corp.vn' => 'nhatpk@namviet-corp.vn',
//            'kieutb@namviet-corp.vn' => 'kieutb@namviet-corp.vn',
        ),
        'subject' => array(
            'day' => 'Báo cáo sản lượng và doanh thu hàng ngày dịch vụ Gamequiz Halovietnam ngày DATE',
            'month' => 'Báo cáo sản lượng và doanh thu tháng DATE dịch vụ Gamequiz Halovietnam',
        ),
        'mail_template' => array(
            'daily' => 'kpi_daily_mail_report',
            'day_by_channel' => 'kpi_day_by_channel_mail_report',
            'monthly' => 'kpi_monthly_mail_report',
        ),
    ),
    'Console' => array(
        'FORCE_READ_ALL_TAG' => 0,
    ),
    'Common' => array(
        'GOOGLE_API_STATICMAP_URL' => 'http://maps.googleapis.com/maps/api/staticmap?zoom=13&size=600x480&key=AIzaSyA6-uZnKtpMLssHYdu4b-s4S0OTvl86LiM&center=',
        'number_question_1package' => 5,
        'status' => array(
            -1 => __('status_rejected'),
            0 => __('status_hidden'),
            1 => __('status_wait_review'),
            2 => __('status_approved'),
        ),
    ),
    //====================================== GAME QUIZ =======================================
    'QuestionGroup' => array(
        'questions_in_group' => 5,
        'status' => array(
            0 => __('status_not_ready'),
            1 => __('status_ready'),
            2 => __('status_played'),
        ),
        'file_import_type' => '.xlsx,.xls',
        'default_qes_gro_name' => 'Bộ câu hỏi thứ %s được import từ file %s',
        'default_qes_gro_description' => 'Bộ câu hỏi được import từ file %s thuộc danh mục có mã danh mục là %s',
    ),
    'Configurations' => array(
        'default_types' => array(
            'sms', 'mt',
        ),
        'codes' => array(
            'gameshowviet' => 'Gameshow Việt',
        ),
        'number_question_1package' => 5,
        'point_question_default' => 100,
    ),
    'Subscribers' => array(
        'point_action_new' => array(
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => 'Chơi game',
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => 'KHAC',
        ),
        'score_status' => array(
            1 => __('score_status_success'),
            0 => __('score_status_fail'),
        ),
        'status' => array(
            0 => __('Chưa đăng ký'),
            1 => __('Đã đăng ký'),
            2 => __('Chưa ĐK gói cước'),
            3 => __('Đã hủy'),
            4 => __('Gia hạn không thành công'),
            5 => __('Hệ thống hủy'),
            6 => __('Bị hủy từ telco'),
        ),
        'status_class' => array(
            0 => "label label-primary",
            1 => "label label-success",
            2 => "label label-warning",
            3 => "label label-default",
            4 => "label label-info",
            5 => "label label-danger",
            6 => "label label-yellow",
        ),
    ),
    'SubscriberImports' => array(
        'data_file_root' => 'subscriber_imports_files',
        'default_file' => 'default/register_deactive_mau.csv'
    ),
    'Players' => array(
        'limit' => 10,
        'country_code' => 84,
        'status' => array(
            0 => __('status_not_register'),
            1 => __('status_registered'),
            2 => __('status_pending_not_charged'),
            3 => __('status_pending_locked'),
            4 => __('status_unregistered'),
            5 => __('status_unregistered'),
            6 => __('status_unregistered'),
        ),
        'play_status' => array(
            0 => __('status_stop_play'),
            1 => __('status_playing'),
        ),
        'STATUS' => [
            'CANCEL' => 0,
            'REGISTER' => 1,
            'NOT_CHARGE' => 2,
        ],
        'ACTION' => [
            'DANG_KY' => 'DANG_KY',
            'HUY' => 'HUY',
            'MUA' => 'MUA',
            'TRA_LOI' => 'TRA_LOI',
            'CHOI' => 'CHOI',
            'BO_QUA' => 'BO_QUA', //TIẾP
            'CHUYEN' => 'CHUYEN',
            'HUONG_DAN' => 'HUONG_DAN',
            'XEM_KET_QUA' => 'XEM_KET_QUA',
            'GIA_HAN' => 'GIA_HAN',
            'KHAC' => 'KHAC',
            'HUY_CMS' => 'HUY_CMS',
        ],
        'PLAYER_STATUS' => array(
            'PLAYER_STATUS_NOT_REGIST' => 0,
            'PLAYER_STATUS_REGISTED' => 1,
            'PLAYER_STATUS_PENDING_NOT_CHARGE' => 2,
            'PLAYER_STATUS_PENDING_LOCK' => 3,
            //  PLAYER_STATUS_UNREGISTER = 4,
            'PLAYER_PLAY_STATUS_STOP' => 0,
            'PLAYER_PLAY_STATUS_PLAYING' => 1,
            'PLAYER_DAILY_QUESTION_STATUS_NOT_SENT_YET' => 0,
            'PLAYER_DAILY_QUESTION_STATUS_SENT' => 1,
        ),
        'point_action' => array(
//			'DK' => __('point_register'),
//			'MUA' => __('point_buy_question'),
//			'GH' => __('point_renew'),
//			'TL' => __('point_answer'),
//			'UN' => __('point_other'),
            'DK' => __('charge_registered'),
            'GH' => __('charge_renew'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TL' => __('charge_answer_question'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('charge_view_point'),
//			'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
// move from wap
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => 'CHOI',
            'BO_QUA' => 'BO_QUA', //TIẾP
            'CHUYEN' => 'CHUYEN',
            'HUONG_DAN' => 'HUONG_DAN',
            'XEM_KET_QUA' => 'XEM_KET_QUA',
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => 'KHAC',
        ),
        'point_action_new' => array(
// move from wap
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => 'Chơi game',
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => 'KHAC',
        ),
        'charge_status' => array(
            0 => __('status_charge_fail'),
            1 => __('status_charge_success'),
        ),
        'default_charge_status' => 1,
        'charge_action' => array(
            'DK' => __('charge_registered'),
            'GH' => __('charge_renew'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TL' => __('charge_answer_question'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('charge_view_point'),
//			'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
        ),
        'mo_action' => array(
            'DK' => __('charge_registered'),
            'GH' => __('charge_renew'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TL' => __('charge_answer_question'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('mo_view_point'),
//			'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
            // move from wap
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => __('mo_play'),
            'BO_QUA' => __('mo_give_up'), //TIẾP
            'CHUYEN' => __('mo_change_channel'),
            'HUONG_DAN' => __('mo_intro'),
            'XEM_KET_QUA' => __('mo_view_result'),
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => __('mo_other'),
        ),
        'charge_action_new' => array(
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => __('mo_play'),
            'BO_QUA' => __('mo_give_up'), //TIẾP
            'CHUYEN' => __('mo_change_channel'),
            'HUONG_DAN' => __('mo_intro'),
            'XEM_KET_QUA' => __('mo_view_result'),
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => __('mo_other'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('mo_view_point'),
//          'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
        // move from wap
        ),
        'mo_action_new' => array(
            'DANG_KY' => __('charge_registered'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TRA_LOI' => __('charge_answer_question'),
            'CHOI' => __('mo_play'),
            'BO_QUA' => __('mo_give_up'), //TIẾP
            'CHUYEN' => __('mo_change_channel'),
            'HUONG_DAN' => __('mo_intro'),
            'XEM_KET_QUA' => __('mo_view_result'),
            'GIA_HAN' => __('charge_renew'),
            'KHAC' => __('mo_other'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('mo_view_point'),
//          'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
        // move from wap
        ),
        'mt_action' => array(
            'DK' => __('charge_registered'),
            'GH' => __('charge_renew'),
            'HUY' => __('charge_unregistered'),
            'MUA' => __('charge_buy_question'),
            'TL' => __('charge_answer_question'),
            'UN' => __('charge_unknown'),
            'DIEM' => __('charge_view_point'),
//			'CH' => __('charge_get_question'),
            'HUY_CMS' => __('charge_unregistered_by_cms'),
            'CH' => __('charge_buy_question'),
            'MK' => __('charge_password'),
            'WINNER_DAILY_CMS' => __('charge_winner_daily_by_cms'),
            'WINNER_MONTHLY_CMS' => __('charge_winner_monthly_by_cms'),
        ),
        'mt_status' => array(
            0 => __('status_not_send'),
            1 => __('status_sent'),
        ),
        'mo_status' => array(
            0 => 'Không thành công',
            1 => 'Thành công',
            'CHECK' => 'CHECK',
            'REAL' => 'REAL',
        ),
        'score_status' => array(
            1 => __('score_status_success'),
            0 => __('score_status_fail'),
        ),
        'channel' => array(
            'SMS' => 'SMS',
            'WAP' => 'WAP'
        ),
    ),
    'Winner' => array(
        'type' => array(
            0 => __('win_of_day'),
            1 => __('win_of_week'),
            2 => __('win_of_month'),
//			3 => __('win_of_other'),
        ),
    ),
    'ChargingVMS' => array(
        'key' => '9ABxlwWpn6mGzdlU',
        'sp_id' => '035',
        //ungnv 27/11 set giá = 0
        'MUA_price' => 20,
        'G1_price' => 20,
        'G7_price' => 90,
        /* 'MUA_price' => 2000,
          'G1_price' => 2000,
          'G7_price' => 9000, */
        //end ungnv 27/11 set giá = 0
        'RESULT_CHARGE_OK' => "CPS-0000",
        'RESULT_CHARGE_NOK' => "CPS-1001",
        'url_charge' => "http://dangky.mobifone.com.vn/wap/html/sp/confirm.jsp",
        'url_return' => "http://124.158.5.134:8083/Players/registresult",
        'diameter' => 'http://10.54.3.181:8002/diameter/charge?msisdn=%s&amount=%s',
//        'url_charge' => "http://localhost/2vietnam_wap/Tests/fakeMobifoneGateWay?trans_id=%s&msisdn=%s&status=%s",
        'diameter_test' => 'http://localhost/2vietnam_wap/Tests/fakeMobifoneCharge?code=%s',
        'G1_free1day_information' => '2,000đ/1 ngày||Miễn phí 1 ngày', // nội dung hiện thị trang wap của mobi với trường hợp thuê bao đăng ký lần đầu tiên
        'G7_free1day_information' => '9,000đ/7 ngày||Miễn phí 1 ngày', // nội dung hiện thị trang wap của mobi với trường hợp thuê bao đăng ký lần đầu tiên
        'G1_information' => '1 ngày', // nội dung hiện thị trang wap của mobi với trường hợp thuê bao đăng ký lần 2 trở đi
        'G7_information' => '7 ngày', // nội dung hiện thị trang wap của mobi với trường hợp thuê bao đăng ký lần 2 trở đi
    ),
    'SmsSender' => array(
        'api_key' => '5dc2eb7f9d609aeedb6f302560f48cc820649c1a',
        'service_url' => 'http://10.54.3.181:9091/cgi-bin/sendsms',
        'password_length' => 4,
        'status' => 1,
        'get_password_content' => 'Mat khau de su dung dich vu HaloVietNam la %s. Ban su dung mat khau nay cung so dien thoai %s de dang nhap va su dung dich vu.',
        'get_password_action' => 'GET_PASSWORD',
        'reset_password_content' => 'Mat khau de su dung dich vu HaloVietNam la %s. Ban su dung mat khau nay cung so dien thoai %s de dang nhap va su dung dich vu.',
        'reset_password_action' => 'RESET_PASSWORD',
        'sms_hello' => 'Xin chao ',
        'sms_conten_2' => '. Ban dang co ',
    ),
    //END ====================================== GAME QUIZ =======================================
    'Blogs' => array(
        'data_file_root' => 'blog_files',
    ),
    'Themes' => array(
        'data_file_root' => 'theme_files',
        'list_type' => array('WAP' => 'WAP', 'WEB' => 'WEB')
    ),
    'Tests' => array(
        'data_file_root' => 'test_files',
    ),
    'News' => array(
        'data_file_root' => 'news_files',
        'collections' => [__('New'), __('Hot'), __('Top')],
        'categories' => [__('New'), __('Hot'), __('Top')],
    ),
    'Guides' => array(
        'data_file_root' => 'guides_files',
    ),
    'Emergencies' => array(
        'data_file_root' => 'emergencies_files',
    ),
    'SplashScreens' => array(
        'data_file_root' => 'splash_screens_files',
    ),
    'Tips' => array(
        'data_file_root' => 'tips_files',
    ),
    'Events' => array(
        'data_file_root' => 'events_files',
    ),
    'ServiceProviders' => array(
        'data_file_root' => 'service_providers_files',
    ),
    'Distributors' => array(
        'data_file_root' => 'distributors_files',
    ),
    'DistributionChannels' => array(
        'data_file_root' => 'distribution_channels_files',
    ),
    'Hotels' => array(
        'data_file_root' => 'hotels_files',
    ),
    'Restaurants' => array(
        'data_file_root' => 'restaurants_files',
        'constants' => array(
            'RESTAURANT_FOOD_OBJECT_TYPE_ID' => '55629754c4a160e196840d48',
        ),
    ),
    'Places' => array(
        'data_file_root' => 'places_files',
    ),
    'Topics' => array(
        'data_file_root' => 'topics_files',
    ),
    'Activities' => array(
        'data_file_root' => 'activities_files',
    ),
    'Promotions' => array(
        'data_file_root' => 'promotions_files',
    ),
    'Coupons' => array(
        'vipcodes' => [
            'ab12sfADf31' => __('Code 1'),
            'loit76aByT5' => __('Code 2')
        ],
        'data_file_root' => 'coupons_files',
    ),
    'RegionActivities' => array(
        'data_file_root' => 'region_activities_files',
    ),
    'Hospitals' => array(
        'data_file_root' => 'hospitals_files',
    ),
    'Tours' => array(
        'data_file_root' => 'tours_files',
    ),
    'Regions' => array(
        'data_file_root' => 'regions_files',
    ),
    'Taxi' => array(
        'data_file_root' => 'Taxi_files',
    ),
    'Trains' => array(
        'data_file_root' => 'trains_files',
    ),
    'Airlines' => array(
        'data_file_root' => 'airlines_files',
    ),
    'Buses' => array(
        'data_file_root' => 'buses_files',
    ),
    'BusStations' => array(
        'data_file_root' => 'bus_stations_files',
    ),
    'Ships' => array(
        'data_file_root' => 'ships_files',
    ),
    'Banks' => array(
        'data_file_root' => 'banks_files',
    ),
    'Atms' => array(
        'data_file_root' => 'atms_files',
    ),
    'WeatherDescriptions' => array(
        'data_file_root' => 'weather_descriptions_files',
    ),
    'ObjectIcons' => array(
        'data_file_root' => 'object_icons_files',
    ),
    'Facilities' => array(
        'data_file_root' => 'Facilities_files',
    ),
    'Visitors' => array(
        'data_file_root' => 'visitors_files',
    ),
    'SubscriberBlacklists' => array(
        'data_file_root' => 'subscriber_blacklists_files',
        'default_file' => 'default/blacklist_mau.csv'
    ),
    'PlayerImports' => array(
        'data_file_root' => 'player_imports_files',
        'default_file' => 'default/register_deactive_mau.csv'
    ),
    'StreamingServers' => array(
        'data_file_root' => 'streaming_servers_files',
    ),
    'Streamings' => array(
        'data_file_root' => 'streamings_files',
    ),
    'VodAssets' => array(
        'data_file_root' => 'vod_assets',
    ),
    'UserGroups' => array(
        'status' => array(
            0 => __('status_inactive'),
            1 => __('status_active'),
        ),
    ),
    'UserChannels' => array(
        'prefix_name' => 'distributor_',
        'type' => 'GAMEQUIZ_DISTRIBUTION_CHANNEL',
    ),
    'Users' => array(
        'status' => array(
            0 => __('status_inactive'),
            1 => __('status_active'),
        ),
        'type' => array(
            'BACKEND' => 'Backend user',
            'CONTENT_EDITOR' => 'Content editor',
            'CONTENT_REVIEWER' => 'Content reviewer',
            'CONTENT_ADMIN' => 'Content admin',
            'GAMEQUIZ_DISTRIBUTION_CHANNEL' => 'GameQuiz distribution channel',
        ),
        'home_url' => array(
            'BACKEND' => array('controller' => 'Tours', 'action' => 'index'),
            'CONTENT_EDITOR' => array('controller' => 'Tours', 'action' => 'index'),
            'CONTENT_REVIEWER' => array('controller' => 'Tours', 'action' => 'index'),
        ),
        'gender' => array(
            '0' => __('Female'),
            '1' => __('Male'),
        ),
        'time_zone' => array(
            1 => "UTC−12:00",
            2 => "UTC−11:00",
            3 => "UTC−10:00",
            4 => "UTC−09:30",
            5 => "UTC−09:00",
            6 => "UTC−08:00",
            7 => "UTC−07:00",
            8 => "UTC−06:00",
            9 => "UTC−05:00",
            10 => "UTC−04:30",
            11 => "UTC−04:00",
            12 => "UTC−03:30",
            13 => "UTC−03:00",
            14 => "UTC−02:00",
            15 => "UTC−01:00",
            16 => "UTC±00:00",
            17 => "UTC+01:00",
            18 => "UTC+02:00",
            19 => "UTC+03:00",
            20 => "UTC+03:30",
            21 => "UTC+04:00",
            22 => "UTC+04:30",
            23 => "UTC+05:00",
            24 => "UTC+05:30",
            25 => "UTC+05:45",
            26 => "UTC+06:00",
            27 => "UTC+06:30",
            28 => "UTC+07:00",
            29 => "UTC+08:00",
            30 => "UTC+08:45",
            31 => "UTC+09:00",
            32 => "UTC+09:30",
            33 => "UTC+10:00",
            34 => "UTC+10:30",
            35 => "UTC+11:00",
            36 => "UTC+11:30",
            37 => "UTC+12:00",
            38 => "UTC+12:45",
            39 => "UTC+13:00",
            40 => "UTC+14:00",
        )
    ),
    'NavBar' => array(),
    'ObjectTypes' => array(
        'data_file_root' => 'object_types_files',
    ),
    'Slogans' => array(
        'data_file_root' => 'slogans_files',
    ),
    'Streamings' => array(
        'data_file_root' => 'streamings_files',
        'bitrates' => array(
            144 => __('bitrate_low'),
            360 => __('bitrate_medium'),
            720 => __('bitrate_high'),
        ),
        'servers' => array(
            'HTTP' => 'http://streaming.mplace.vn',
            'RTSP' => 'rtsp://streaming.mplace.vn:1935',
            'HLS' => 'http://streaming.mplace.vn:1935',
        ),
    ),
    'ReportDailyAccessLogin' => array(
        'offset_date_start' => '-7 days',
    ),
    'Shell' => array(
        'CdrFileCreationShell' => array(
            'ftp_info' => array(
// test
                'ftp_server' => '103.18.6.56',
                'ftp_user_name' => 'uploader@phutx.info',
                'ftp_user_pass' => '123456',
                'ftp_port' => 21,
                'ftp_timeout' => 30,
            // vms
//                'ftp_host' => '10.50.9.248',
//                'ftp_user' => 'mplace',
//                'ftp_pass' => 'mp1711',
//                'ftp_port' => 21,
//                'ftp_timeout' => 100,
            )
        )
    ),
    'DailyReport' => array(
        'package' => array(
            'G1' => 'G1',
            'G7' => 'G7',
            //'G30' => 'G30',
            'MUA' => 'MUA'
        ),
        'channel' => array(
            'SMS' => 'SMS',
            'WAP' => 'WAP'
        ),
    ),
    'TrafficSigns' => array(
        'data_file_root' => 'traffic_sign_files',
    ),
    'TrafficLibraries' => array(
        'data_file_root' => 'traffic_libraries_files',
    ),
    'GameQuiz' => array(
        'Report' => array(
            'Daily' => array(
                'Email_template' => 'gamequiz_report_daily',
                'Receiver' => array(
                    'phutx@namviet-corp.vn' => 'PHUTX', // Email debug
                    'hoangnn@namviet-corp.vn' => 'A Hoàng', // Email debug
                    'nhatpk@namviet-corp.vn' => 'Nhật', // Email Kinh Doanh
                )
            ),
            'Hourly' => array(
                'Email_template' => 'gamequiz_report_hourly',
                'Receiver' => array(
                    'phutx@namviet-corp.vn' => 'PHUTX', // Email debug
                    'hoangnn@namviet-corp.vn' => 'A Hoàng', // Email debug
                    'nhatpk@namviet-corp.vn' => 'Nhật', // Email Kinh Doanh
                )
            ),
        )
    ),
    'SERVICE_CODE' => array(
        'GAME_QUIZ' => 'GAME_QUIZ'
    ),
    'TrackingReport' => array(
        'package' => array(
            'G1' => 'G1',
            'G7' => 'G7',
            //'G30' => 'G30',
            'MUA' => 'MUA'
        ),
        'channel' => array(
            'WAP' => 'WAP',
            'WEB' => 'WEB',
            'APP' => 'APP',
        ),
        'default_channel' => 'WAP'
    ),
    'TraditionalReport' => array(
        'package' => array(
            'G1' => 'G1',
            'G7' => 'G7',
            //'G30' => 'G30',
            'MUA' => 'MUA'
        ),
        'channel' => array(
            'SMS' => 'SMS',
            'WAP' => 'WAP',
            'WEB' => 'WEB',
            'APP' => 'APP',
        ),
        'default_channel' => 'WAP'
    ),
    'AppStartYear' => 2015,
    'status_class' => array(
        0 => 'label label-danger',
        1 => 'label label-primary',
        2 => 'label label-warning',
        3 => 'label label-warning',
        4 => 'label label-danger',
    ),
    'play_status_clss' => array(
        0 => 'label label-danger',
        1 => 'label label-primary',
    ),
    'special_region' => 'thudo',
    'total_result_prize' => array(
        'thudo' => 27,
        'other' => 18
    ),
    'amplitudesOblique' => array( // Biên độ thống kê xiên
        2, 3, 4, 5, 6
    )
);

