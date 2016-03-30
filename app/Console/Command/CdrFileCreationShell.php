<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class CdrFileCreationShell extends AppShell {

    const SERVICE_NAME = 'mplace_';
    const FOLDER_DATA = 'cdr-data';
    const FOLDER_BACKUP = 'cdr-backup';
    const FOLDER_VERIFY = 'cdr-verify';
    const STATUS_SUCCESS = 1;

    public $start_time; // thời gian hiện tại - 1 khoảng 30 phút
    public $end_time; // thời gian hiện tại
    public $counter; // mỗi file cdr tạo ra sẽ + thêm 1

    public function getCounter() {
        // lấy counter từ db
        $this->loadModel('CdrCounter');
        $data = $this->CdrCounter->find('first');
        // gán cho thuộc tính counter
        $this->counter = isset($data['CdrCounter']['count']) ? $data['CdrCounter']['count'] : 0;
    }

    public function setCounter() {
        // lưu thuộc tính counter xuống db
        $this->loadModel('CdrCounter');
        $data = $this->CdrCounter->find('first');
        if ($data) {
            $data['CdrCounter']['count'] += $this->counter;
        } else {
            $data['CdrCounter']['count'] = $this->counter;
        }
        $this->CdrCounter->save($data);
    }

    public function verify() {
        // lấy ngày hiện tại kiểu Ymd
        $date = date('Ymd', time());
        $this->out('Date verify (pull from FTP): ' . $date);
        $verifydir = APP . WEBROOT_DIR . DS . self::FOLDER_VERIFY;
        // lay file cdr tu server ve
        $this->ftpPull($date);
        $cdr_files = scandir($verifydir);
        $count = $amounts = $statistics = 0;
        foreach ($cdr_files as $cdr_file) {
            if (strpos($cdr_file, ".") === 0) { // dung cai nay de loai bo ca hidden files
                continue;
            }

            $count++;
            $handle = fopen("$verifydir/$cdr_file", 'r') or $this->out("CDR Error: Cannot open CDR verify file for reading");

            echo "\nreading cdr file: '$cdr_file'";
            while (!feof($handle)) {
                $cdr_rec = fgets($handle);
                $cdr_rec = trim($cdr_rec);
                $tokens = explode(":", $cdr_rec);
                if (count($tokens) < 2) { // loai bo dong trong
                    continue;
                }
                if (count($tokens) != 10) {
                    $this->out("CDR Error: some fields missing in CDR record: $cdr_rec");
                    continue;
                }

                foreach ($tokens as $token) {
                    if (strlen($token) < 1) {
                        $this->out("CDR Error: some fields empty in CDR record: $cdr_rec");
                        continue;
                    }
                }
                $datetime = $tokens[0];
                $msisdn = $tokens[1];
                $shortcode = $tokens[2];
                $eventId = $tokens[3];
                $cpid = $tokens[4];
                $content_id = $tokens[5];
                $status = $tokens[6];
                $cost = $tokens[7];
                $channel = $tokens[8];
                $info = $tokens[9];

                if (strlen($datetime) != 14) {
                    $this->out("CDR Error: datetime invalid in CDR record: $cdr_rec");
                    continue;
                }

                if (strlen($msisdn) != 11 && strlen($msisdn) != 12) {
                    $this->out("CDR Error: msisdn invalid in CDR record: $cdr_rec");
                    continue;
                }

//                if ($shortcode != "049144") {
//                    $this->out("CDR Error: shortcode invalid in CDR record: $cdr_rec");
//                    continue;
//                }

//                if ($eventId != "000001" && $eventId != "000002") {
//                    $this->out("CDR Error: eventID (categoryID) invalid in CDR record: $cdr_rec");
//                    continue;
//                }

//                if ($cpid != "001001") {
//                    $this->out("CDR Error: CPID invalid in CDR record: $cdr_rec");
//                    continue;
//                }

//                if ($content_id != "0010010001" && $content_id != "0010010002" && $content_id != "0010010003" &&
//                        $content_id != "0010020001" && $content_id != "0010020002" && $content_id != "0010020003"
//                ) {
//                    $this->out("CDR Error: content_id invalid in CDR record: $cdr_rec");
//                    continue;
//                }

                if ($status != 0 && $status != 1 && $status != 2) { // 2 trong CDR cu, thuc te phai la 0 khi giao dich that bai
                    $this->out("CDR Error: status invalid in CDR record: $cdr_rec");
                    continue;
                }

                if (!is_numeric($cost)) {
                    $this->out("CDR Error: cost invalid in CDR record: $cdr_rec");
                    continue;
                }

                if ($channel != "SMS" && $channel != "WAP"/* && $channel != "APP" && $channel != "WEB"*/) {
                    $this->out("CDR Error: channel invalid in CDR record: $cdr_rec");
                    continue;
                }

                if ($status == 1) { // thong ke tren cac giao dich thanh cong
                    $statistics++;
                    $amounts += $cost;
                }

                // 				echo "\ncdr record: '$cdr_rec' - tokens: ".count($tokens);
            }

            fclose($handle);
        }

        $this->out("CDR verify: total files for $date: $count");

        $revenues = Revenue::model()->findAllBySql("select * from revenue where date(create_date) = '$date'");
        $totalRevenue = 0;
        foreach ($revenues as $rev) {
            if ($rev["view"] != NULL) {
                $totalRevenue += $rev["view"];
            }
            if ($rev["download"] != NULL) {
                $totalRevenue += $rev["download"];
            }
            if ($rev["gift"] != NULL) {
                $totalRevenue += $rev["gift"];
            }
            if ($rev["register"] != NULL) {
                $totalRevenue += $rev["register"];
            }
            if ($rev["extend"] != NULL) {
                $totalRevenue += $rev["extend"];
            }
            if (isset($rev["gift_sub"]) && $rev["gift_sub"] != NULL) {
                $totalRevenue += $rev["gift_sub"];
            }
            if (isset($rev["retry_extend"]) && $rev["retry_extend"] != NULL) {
                $totalRevenue += $rev["retry_extend"];
            }
        }
        // 		var_dump($revenues);

        $this->out("$date: $res; reported revenue: $totalRevenue; DELTA: " . ($totalAmount - $totalRevenue), 2);
        // Query get charge
        $model = new Model();
        $model->useTable = 'charge_' . date('Y_m_d');
        // truy vấn cơ sở dữ liệu lấy ra tài khoản người chơi có giao dịch
        $list_datas = $model->find('all', array('conditions' => array(
                'created' => array(
                    '$gte' => new MongoDate($this->start_time),
                    '$lte' => new MongoDate($this->end_time)
                )
        )));
        if ($totalAmount - $totalRevenue != 0) {
            $this->out("So lieu cuoc mfilm bi lech: " . ($totalAmount - $totalRevenue), 2);
        }
    }

    public function createCdrFileLast30Minutes() {
        // gọi hàm get counter
        $this->getCounter();
        // tính toàn thời gian hiện tại và thời gian 30 phút trước gán vào thuộc tính start time và end time
        $this->end_time = time();
        $this->start_time = strtotime('-30 minutes');
        // gọi hàm tạo CDR (createCdr)
        $this->createCdr();
        // gọi hàm upload ftp
        $this->ftpPush();
        // gọi hàm lưu couter
        $this->setCounter();
    }

    public function createCdrFileDaily() {
        // gọi hàm get counter
        $this->getCounter();
        // tính toàn thời gian hiện tại và thời gian 30 phút trước gán vào thuộc tính start time và end time
        $this->end_time = time();
        $this->start_time = strtotime('-30 minutes');
        if(isset($this->args[0])) {
            $this->end_time = strtotime($this->args[0] . ' 23:59:59');
            $this->start_time = strtotime($this->args[0] . ' 00:00:00');
        }
        // gọi hàm tạo CDR (createCdr)
        $this->createCdr();
        // gọi hàm upload ftp
        $this->ftpPush();
        // gọi hàm lưu couter
        $this->setCounter();
    }

    /**
     * Tạo file CDR
     */
    public function createCdr() {
        $from = date('Y-m-d H:i:s', $this->start_time);
        $to = date('Y-m-d H:i:s', $this->end_time);

        $file_name = $this->creatFileName();
        $this->out("File: $file_name from $from to $to.");

        // Query get charge
        $model = new Model();
        $model->useTable = 'charge_' . date('Y_m_d', $this->start_time);
        // truy vấn cơ sở dữ liệu lấy ra tài khoản người chơi có giao dịch
        $list_datas = $model->find('all', array('conditions' => array(
                'created' => array(
                    '$gte' => new MongoDate($this->start_time),
                    '$lte' => new MongoDate($this->end_time)
                )
        )));
        // hiển thị câu truy vấn
        $this->out('QUERY: ');
        pr($model->getDataSource()->getLog(false, false));
        // đếm số kết quả
        $total = count($list_datas);
        // hiển thị số kết quả
        $this->out('TOTAL: ' . $total);
        // kiểm tra két quả < 1 thì giữ nguyên counter, thoát function
        if ($total < 1) {
            return;
        }
        // tạo file, ghi dữ liệu, đóng file
        $file = new File($file_name, true);
        foreach ($list_datas as $tran) {
            // sử lý logic
            $datetime = date('YmdHis', $tran[$model->alias]['created']->sec);
            $msisdn = $tran[$model->alias]['phone'];
            $short_code = "049144";
            $CPID = "035";
            $content_id = "0010010001";
            $eventID = "000001";
            $status = $tran[$model->alias]['status'];
            $cost = $tran[$model->alias]['amount'];
            $channel_type = $tran[$model->alias]['channel'];
            // tạo dữ liệu
            $cdr_record = "$datetime:$msisdn:$short_code:$eventID:$CPID:$content_id:$status:$cost:$channel_type:1\n";
            // ghi ra file
            $file->append($cdr_record);
        }

        // kiểm tra couter lớn hơn 9999 thì cho = 0, ngược lại thì + thêm 1
        $this->counter += $this->counter > 9999 ? 0 : 1;
    }

    private function creatFileName() {
        $director = APP . WEBROOT_DIR . DS;
        // tên file crd theo chuẩn: tên dịch vụ_Năm tháng ngày_Số thứ tự file (4 ký tự).cdr
        $name = self::SERVICE_NAME . date('Ymd', $this->start_time) . "_" . str_pad($this->counter, 4, 0, STR_PAD_LEFT) . ".cdr";
        return $director . self::FOLDER_DATA . DS . $name;
    }

    /**
     * đẩy file lên ftp
     */
    public function ftpPush() {
        $conn_id = $this->ftpLogin();
        // gán thư mục
        $data_dir = new Folder(APP . WEBROOT_DIR . DS . self::FOLDER_DATA);
        $backup_dir = new Folder(APP . WEBROOT_DIR . DS . self::FOLDER_BACKUP);

        // lấy toàn bộ file trong thư mục data để đẩy lên ftp
        $cdr_files = $data_dir->find('.*\.cdr');
        $count = 1;
        foreach ($cdr_files AS $file) {
            $file = new File($data_dir->pwd() . DS . $file);
            $count++;
            if ($count % 10 == 0) {
                $this->out("Reconnect...");
                $conn_id = $this->ftpLogin($conn_id);
            }
            $this->out("File to transfer: " . $file->name);
            // lấy ra ngày tạo file từ tên file
            $cdr_date = explode('_', $file->name());
            $ftp_dir = $cdr_date[1];
            // kiểm tra folder ngày đã có trên ftp chưa, chưa có thì tạo mới
            if (@ftp_chdir($conn_id, $ftp_dir)) {
                $this->out("Ftp folder changed: " . $ftp_dir);
            } else {
                $ftp_dir = ftp_mkdir($conn_id, $ftp_dir);
                if ($ftp_dir) {
                    ftp_chdir($conn_id, $ftp_dir);
                    $this->out("Ftp folder created: " . $ftp_dir);
                } else {
                    $this->out("CDR ERROR: Cannot create/change FTP folder");
                    exit;
                }
            }
            // upload a file
            ftp_pasv($conn_id, true);
            if (ftp_put($conn_id, $file->name, $file->pwd(), FTP_BINARY)) {
                $this->out('File ' . $file->name . ' uploaded to FTP.');
            } else {
                $this->out('CDR ERROR: Cannot upload CDR file ' . $file->name . ' to FTP: ' . $ftp_dir . "/" . $file->name());
                exit;
            }
            // di chuyển file đó qua folder backup
            if ($file->copy($backup_dir->pwd() . DS . $file->name) && $file->delete()) {
                $this->out('File ' . $file->name . ' was moved to backup folder.');
            } else {
                $this->out('CDR ERROR: Cannot move ' . $file->name . ' to backup folder.');
                exit;
            }
            // chuyển thư mục ftp về root
            ftp_chdir($conn_id, '');
        }

        // close the connection
        ftp_close($conn_id);
    }

    /**
     * kéo file từ ftp về
     */
    public function ftpPull($date = null) {
        $date = isset($date) ? $date : (isset($this->args[0]) ? $this->args[0] : date('Ymd', time()));
        $ftp_info = Configure::read('sysconfig.Shell.CdrFileCreationShell.ftp_info');
        extract($ftp_info);

        $ftp_dir = "$date";

        // set up basic connection
        $conn_id = $this->ftpLogin();

        if (ftp_chdir($conn_id, $ftp_dir)) {
            $this->out('Ftp folder changed: ' . $ftp_dir);
        } else {
            $this->out("CDR ERROR: Cannot found CDR folder on FTP server: $ftp_dir");
            exit;
        }

        ftp_pasv($conn_id, true);
        $ftp_files = ftp_nlist($conn_id, '.');
        if ($ftp_files) {
            unset($ftp_files[0]); // remove .
            unset($ftp_files[1]); // remove ..
            $file_count = count($ftp_files);
            $this->out("Total CDR files for $date: $file_count");
        } else {
            $this->out("CDR ERROR: Cannot load CDR file in FTP folder: $ftp_dir");
            exit;
        }

        // xoa cac file cdr cu
        $verify_dir = new Folder(APP . WEBROOT_DIR . DS . self::FOLDER_VERIFY);
        $cdr_files = $verify_dir->find('.*\.cdr');
        foreach ($cdr_files AS $file) {
            $file = new File($verify_dir->pwd() . DS . $file);
            $file->delete();
        }
        $verify_date_dir = new Folder(APP . WEBROOT_DIR . DS . self::FOLDER_VERIFY . DS . $date, true);
        $count = 0;
        foreach ($ftp_files as $ftp_file) {
            $count++;
            if ($count % 10 == 0) { // ket noi lai sau moi 10 file upload ==> fix loi khi upload nhieu file trong cung 1 connection
                $this->out("Reconnect..");
                $conn_id = $this->ftpLogin($conn_id);
            }
            $filename = basename($ftp_file);
            if (ftp_get($conn_id, $verify_date_dir->pwd() . DS . $filename, $filename, FTP_BINARY)) {
                $this->out("FTP copied file: " . $verify_date_dir->pwd() . DS . $filename);
            } else {
                $this->out("CDR ERROR: Cannot download CDR file '$ftp_file' from FTP");
                exit;
            }
        }
        ftp_close($conn_id); // this can take long time???
    }

}
