<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->fetch('title'); ?></title>
    <style type="text/css">
        body {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin:0 !important;
            width: 100% !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            -webkit-font-smoothing: antialiased !important;
        }
        .tableContent img {
            border: 0 !important;
            display: block !important;
            outline: none !important;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        table thead {
            background: #E4E4E4;
        }
        a{
            color:#382F2E;
        }

        p, h1{
            color:#382F2E;
        }

        .bgBody{
            background: #dddddd;
        }
        .bgItem{
            background: #F7F7F7;
        }
        h2{
            color:#F34E32;
            font-size: 24px;
            margin:0;
            font-weight:normal;
            font-family:Georgia, serif;
        }
        p{
            color:#967B76;
            font-size: 14px;
            margin:0;
            line-height:20px;
            font-family:Georgia, serif;
        }
        .contentEditable p {
            color: #282780 !important;
        }
    </style>
</head>
<body paddingwidth="0" paddingheight="0" class='bgBody'  style="padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;" offset="0" toppadding="0" leftpadding="0">
    <?php echo $this->fetch('content'); ?>
</body>
</html>