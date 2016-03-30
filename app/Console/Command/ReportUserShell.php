<?php

class ReportUserShell extends AppShell
{

        public $date;

        public function startup()
        {
                parent::startup();
        }

        public function main()
        {
                $this->out('Hello');
                $this->date = isset($this->args[0]) ? $this->args[0] : date('Y-m-d');
                $this->reportActive()->reportRegister()->ftpPush();
        }

        private function reportActive()
        {
                $from_date = strtotime($this->date . ' 00:00:00');
                $to_date = strtotime($this->date . ' 23:59:59');
                $file_register = APP . WEBROOT_DIR . DS . 'report' . DS . 'gamequiz_register_' . date('Ymd', $from_date) . '.txt';
                $file_unregister = APP . WEBROOT_DIR . DS . 'report' . DS . 'gamequiz_unregister_' . date('Ymd', $from_date) . '.txt';
                $this->out(sprintf('File %s from %s to %s.', $file_register, date('Y-m-d H:i:s', $from_date), date('Y-m-d H:i:s', $to_date)));
                $this->out(sprintf('File %s from %s to %s.', $file_unregister, date('Y-m-d H:i:s', $from_date), date('Y-m-d H:i:s', $to_date)));
                // get user from db, điều kiện giao dịch thành công, ngày tạo trong khoảng from và to
                // nếu get ko ra return luôn
                // nếu có tạo vòng lặp, ghi vào file tương ứng, nếu khách hàng mua mới và gia hạn thì lưu vào file register, nếu khách hàng hủy hoặc hệ thống hủy thì ghi vào file unregister
                // đóng file
                return $this;
        }

        private function reportRegister()
        {
                $from_date = strtotime($this->date . ' 00:00:00');
                $file_name = APP . WEBROOT_DIR . DS . 'report' . DS . 'gamequiz_active_' . date('Ymd') . '.txt';
                // lấy user active = 1, delete = 0
                // nếu ko có thì return
                // nếu có thì lần lượt ghi dữ liệu vào file
                // đóng file
                return $this;
        }

        private function ftpPush()
        {
                $conn_id = $this->ftpLogin();
                // data folder gồm report/data, report/backup
                // scan file trong data, đẩy lên FTP
                // chuyển file data => backup
                // đóng FTP
        }
}
