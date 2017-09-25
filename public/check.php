<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Linkr Check</title>
    <link rel="stylesheet" href="build/bootstrap.css">
<!--    <link rel="stylesheet" href="build/app.min.css">-->
    <style>
        h5 {
            font-size: 16px;
            color: #008000;
            font-weight: bold;
            font-family: "Courier New", Courier, monospace;

        }
        h6 {
            font-family: "Courier New", Courier, monospace;
            color: red;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            font-size: 16px;
        }

    </style>
</head>
<body style="padding: 10px">
<div style="padding: 20px" class="text-center">
    <h1>System Check</h1>

    <?php

    function info($s, $d)
    {
        $s = str_pad($s, 52-strlen($d), "-").' '.$d;
        echo '<h5>'.$s.'</h5>';


    }

    function xecho($s, $b)
    {
        $s = str_pad($s, 50 , "-");
        if($b)
        {
            echo '<h5>'.$s.' OK</h5>';
        } else {
            echo '<h6>'.$s.' NO</h6>';

        }
    }

    $ver = explode('.', PHP_VERSION);

    xecho('PHP Version:'.$ver[0].'.'. $ver[1],version_compare(PHP_VERSION, '5.4.0') >= 0 );

    $ext = ['pdo', 'pdo_mysql', 'gd', 'mcrypt', 'json', 'curl', 'mbstring', 'exif', 'fileinfo'];

    foreach ($ext as $e) {
        xecho("PHP extension " . $e, extension_loaded($e));
    }

    xecho("../app/srtorage writable", is_writable('../app/storage/'));

    xecho("assets/avatar/ writable", is_writable('assets/avatar/'));

    if(file_exists('../.env.php'))
    {
        xecho(".env.php exists", true);
        xecho(".env.php writable", is_writable("../.env.php"));
        $env = include('../.env.php');
        xecho("Linkr installed", isset($env['INSTALLED']));
        if(isset($env['DEBUG']))
            info("Debug Mode", $env['DEBUG']);

        if(isset($env['FILES_FOLDER'])) {
            info("Uploads folder", $env['FILES_FOLDER']);
            xecho("Uploads folder writable",  is_writable($env['FILES_FOLDER'].'/'));

        }

    } else {
        xecho("Linkr installed", false);

    }

    echo "<br>";
    phpinfo();
?>
</div>
</body>
</html>
