<?php

class EmailConfig {

    public $gmail = array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => 465,
        'username' => 'sujupro@gmail.com',
        'password' => '6z86zacC',
        'transport' => 'Smtp',
        'timeout' => 30,
    );
    public $namviet1 = array(
        'transport' => 'Smtp',
        'from' => array('hi-vn@namviet-corp.vn' => 'hi-vn@namviet-corp.vn'),
        'host' => 'ssl://namviet-corp.vn',
        'port' => 465,
        'timeout' => 60,
        'username' => 'hi-vn@namviet-corp.vn',
        'password' => 'Namviet!@#2015',
        'client' => null,
        'log' => false,
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
    );
//    public $namviet = array(
//        'transport' => 'Mail',
//        'from' => array('hi-vn@namviet-corp.vn' => 'hi-vn@namviet-corp.vn'),
//        'host' => 'mail.namviet-corp.vn',
//        'port' => 587,
//        'timeout' => 60,
//        'auth' => true,
//        'tls' => true,
//        'username' => 'hi-vn@namviet-corp.vn',
//        'password' => 'Namviet!@#2015',
//        'client' => null,
//        'log' => true,
//        'charset' => 'utf-8',
//        'headerCharset' => 'utf-8',
//    );
    public $hallo = array(
        'transport' => 'Smtp',
        'from' => array('admin@halovietnam.vn' => 'admin@halovietnam.vn'),
        'host' => 'ssl://smtp.zoho.com',
        'port' => 465,
        'timeout' => 60,
        'username' => 'admin@halovietnam.vn',
        'password' => 'halovietnam123',
        'client' => null,
        'log' => false,
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
        'context' => array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false,
                'allow_self_signed' => false,
                'verify_peer' => false,
            )
        )
    );
//    public $namviet = array(
//        'transport' => 'Smtp',
//        'from' => array('hi-vn@namviet-corp.vn' => 'hi-vn@namviet-corp.vn'),
//        'host' => 'ssl://namviet-corp.vn',
//        'port' => 465,
//        'timeout' => 60,
//        'username' => 'tungpt@namviet-corp.vn',
//        'password' => 'Phamtung123@',
//        'client' => null,
//        'log' => false,
//        'charset' => 'utf-8',
//        'headerCharset' => 'utf-8',
//        'context' => array(
//            'ssl' => array(
//                'verify_peer' => false,
//                'verify_host' => false,
//                'allow_self_signed' => false,
//                'verify_peer' => false,
//            )
//        )
//    );
//    public $hivietnam = array(
//        'transport' => 'Mail',
//        'from' => array('hi-vietnam@hi-vietnam.vn' => 'hi-vietnam@hi-vietnam.vn'),
//        'host' => 'mail.hi-vietnam.vn',
//        'port' => 587,
//        'timeout' => 60,
//        'auth' => true,
//        'tls' => true,
//        'username' => 'hi-vietnam@hi-vietnam.vn',
//        'password' => 'hivietnam@2015',
//        'client' => null,
//        'log' => true,
//        'charset' => 'utf-8',
//        'headerCharset' => 'utf-8',
//    );

    public $hivietnam = array(
        'transport' => 'Smtp',
        'from' => array('hi-vietnam@hi-vietnam.vn' => 'hi-vietnam@hi-vietnam.vn'),
        'host' => 'ssl://smtp.yandex.com',
        'port' => 465,
        'timeout' => 60,
        'username' => 'hi-vietnam@hi-vietnam.vn',
        'password' => 'hivietnam@2015',
        'client' => null,
        'log' => false,
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
        'context' => array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false,
                'allow_self_signed' => false,
                'verify_peer' => false,
            )
        )
    );
    public $hivietnamssl = array(
        'transport' => 'Smtp',
        'from' => array('hi-vietnam@hi-vietnam.vn' => 'hi-vietnam@hi-vietnam.vn'),
        'host' => 'ssl://mail.hi-vietnam.vn',
        'port' => 465,
        'timeout' => 60,
        'username' => 'hi-vietnam@hi-vietnam.vn',
        'password' => 'hivietnam@2015',
        'client' => null,
        'log' => false,
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
        'context' => array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false,
                'allow_self_signed' => false,
                'ssl_cafile' => false,
            )
        )
    );
    public $halovietnam = array(
        'transport' => 'Smtp',
        'from' => array('hi-vietnam@hi-vietnam.vn' => 'hi-vietnam@hi-vietnam.vn'),
        'host' => 'ssl://smtp.yandex.com',
        'port' => 465,
        'timeout' => 60,
        'username' => 'hi-vietnam@hi-vietnam.vn',
        'password' => 'hivietnam@2015',
        'client' => null,
        'log' => false,
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
        'context' => array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_host' => false,
                'allow_self_signed' => false,
                'verify_peer' => false,
            )
        )
    );

}
