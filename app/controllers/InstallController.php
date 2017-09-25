<?php

/**
 * Class InstallController
 */
class InstallController extends BaseController
{

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (Config::get('app.installed')) {
            return Redirect::guest('login');
        }

        return View::make('install.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function install()
    {

        try {
            $file = $myfile = @fopen(base_path('.env.php'), "w");
            fwrite($file, "<?php" . PHP_EOL);
            fwrite($file, "return array(" . PHP_EOL);
            fwrite($file, "\t'APP_TITLE'=>'" . Input::get('app_title') . "'," . PHP_EOL);
            fwrite($file, "\t'APP_SUBTITLE'=>'" . Input::get('app_subtitle') . "'," . PHP_EOL);
            fwrite($file, "\t'APP_FOOTER'=>'" . Input::get('app_copyright') . "'," . PHP_EOL);
            fwrite($file, "\t'SELF_REGISTRATION_DOMAIN'=>'" . Input::get('self_registration') . "'," . PHP_EOL);
            fwrite($file, "\t'FILES_FOLDER'=>'" . Input::get('files_folder') . "'," . PHP_EOL);
            fwrite($file, "\t'LOCALE'=>'" . Input::get('locale') . "'," . PHP_EOL);
            fwrite($file, "\t'INSTALLED'=>True," . PHP_EOL);

            fwrite($file, "\t'MAX_IMAGE_W'=>" . Input::get('image_w') . "," . PHP_EOL);
            fwrite($file, "\t'MAX_IMAGE_H'=>" . Input::get('image_h') . "," . PHP_EOL);

            fwrite($file, "\t'DB_HOST'=>'" . Input::get('db_host') . "'," . PHP_EOL);
            fwrite($file, "\t'DB_DATABASE'=>'" . Input::get('db_database') . "'," . PHP_EOL);
            fwrite($file, "\t'DB_USERNAME'=>'" . Input::get('db_username') . "'," . PHP_EOL);
            fwrite($file, "\t'DB_PASSWORD'=>'" . Input::get('db_password') . "'," . PHP_EOL);
            fwrite($file, "\t'DB_CHARSET'=>'utf8'," . PHP_EOL);
            fwrite($file, "\t'DB_COLLATION'=>'utf8_unicode_ci'," . PHP_EOL);
            fwrite($file, "\t'DB_PREFIX'=>'" . Input::get('db_prefix') . "'," . PHP_EOL);

            fwrite($file, "\t'MAIL_DRIVER'=>'" . Input::get('mail_driver') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_HOST'=>'" . Input::get('mail_host') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_PORT'=>" . Input::get('mail_port') . "," . PHP_EOL);
            fwrite($file, "\t'MAIL_FROM_ADDRESS'=>'" . Input::get('mail_from_address') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_FROM_NAME'=>'" . Input::get('mail_from_name') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_ENCRYPTION'=>'" . Input::get('mail_encryption') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_USERNAME'=>'" . Input::get('mail_username') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_PASSWORD'=>'" . Input::get('mail_password') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_SENDMAIL'=>'" . Input::get('mail_sendmail') . "'," . PHP_EOL);

            fwrite($file, "\t'MAIL_MANDRILL_SECRET'=>'" . Input::get('mail_mandrill_secret') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_MAILGUN_DOMAIN'=>'" . Input::get('mail_mailgun_domain') . "'," . PHP_EOL);
            fwrite($file, "\t'MAIL_MAILGUN_SECRET'=>'" . Input::get('mail_mailgun_secret') . "'," . PHP_EOL);

            fwrite($file, ");" . PHP_EOL);

            fclose($file);

            Config::set('database.connections.mysql.host', Input::get('db_host'));
            Config::set('database.connections.mysql.database', Input::get('db_database'));
            Config::set('database.connections.mysql.username', Input::get('db_username'));
            Config::set('database.connections.mysql.password', Input::get('db_password'));

            define('STDIN', fopen("php://stdin", "r"));
            Artisan::call('key:generate');
            Artisan::call('migrate', array('--force' => true));
          


            $id = DB::table('users')->insertGetId(
                [

                    'full_name' => ucwords(strtolower(Input::get('admin_fullname'))),
                    'code'      => preg_replace('/\s+/', '', Input::get('admin_fullname')),
                    'email'     => Input::get('admin_email'),
                    'password'  => Hash::make(Input::get('admin_password')),
                    'state'     => 2,
                    'create_spaces' => 1,
                    'admin'     => 1
                ]
            );

        
            copy(public_path('assets/avatar/default.jpg'), public_path('assets/avatar/' . $id . '.jpg'));
        
            if(function_exists('proc_open')) {
                Artisan::call('optimize');
            }
        
            return Response::json(['success' => true]);

        } catch (Exception $e) {
            return Response::json(['success' => false, 'msg' => $e->getMessage()]);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmail()
    {

        if (!Input::has('mail_to')) {
            return Response::json(['success' => false, 'msg' => 'No destination address provided for testing.']);
        }

        if (Input::has('mail_password')) {
            if (Input::get('mail_password') != Input::get('mail_password_conf')) {
                return Response::json(
                    ['success' => false, 'msg' => 'Password and password confirmation does not match.']
                );
            }
        }

        Config::set('mail.driver', Input::get('mail_driver'));
        Config::set('mail.host', Input::get('mail_host'));
        Config::set('mail.port', Input::get('mail_port'));
        Config::set('mail.encryption', Input::get('mail_encryption'));
        Config::set('mail.username', Input::get('mail_username'));
        Config::set('mail.password', Input::get('mail_password'));
        Config::set('mail.from.address', Input::get('mail_from_address'));
        Config::set(
            'mail.from', ['address' => Input::get('mail_from_address'), 'name' => Input::get('mail_from_name')]
        );
        Config::set('mail.from.address', Input::get('mail_from_address'));
        Config::set('mail.sendmail', Input::get('mail_sendmail'));
        Config::set('services.mandrill.secret', Input::get('mail_mandrill_secret'));
        Config::set('services.mailgun.domain', Input::get('mail_mailgun_domain'));
        Config::set('services.mailgun.secret', Input::get('mail_mailgun_secret'));

        try {
            Mail::send(
                'emails.mailtest', [], function ($message) {
                    $message->to(Input::get('mail_to'))->subject('Mail test');
                }
            );
            return Response::json(['success' => true]);


        } catch (Exception $e) {
            return Response::json(['success' => false, 'msg' => $e->getMessage()]);

        }


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function testFilesFolder()
    {
        $fname = checkSlash(Input::get('files_folder')) . 'testwrite';
        $file  = $myfile = @fopen($fname, "a+");
        if (!$file) {
            $ret = false;
        } else {
            $ret = true;
            fclose($file);
        }
        return Response::json(['success' => $ret]);

    }

    /**
     * @return mixed
     */
    public function testDb()
    {

        $ret['db'] = true;

        try {
            $connection = mysqli_connect(
                Input::get('db_host'),
                Input::get('db_username'),
                Input::get('db_password'),
                Input::get('db_database')
            );

        } catch (Exception $e) {
            $ret['db']  = false;
            $ret['msg'] = $e->getMessage();
        }

        return $ret;

    }

    /**
     * @return array
     */
    public function serverCheck()
    {
        $ext = ['pdo', 'pdo_mysql', 'gd', 'mcrypt', 'json', 'curl'];
        $ret = [];
        foreach ($ext as $e) {
            $ret[$e] = extension_loaded($e);
        }

        $ret['php'] = version_compare(PHP_VERSION, '5.4.0') >= 0;

        $ret['env'] = true;

        $file = $myfile = @fopen(base_path('.env.php'), "w+");
        if (!$file) {
            $ret['env']      = false;
            $ret['env_file'] = base_path('.env.php');
        } else {
            fwrite($file, "<?php" . PHP_EOL);
            fwrite($file, "return array();" . PHP_EOL);
            fclose($file);
        }

        $ret['avatar'] = true;

        $file = $myfile = @fopen(public_path('assets/avatar/testwrite'), "a+");
        if (!$file) {
            $ret['avatar']     = false;
            $ret['avatar_dir'] = public_path('assets/avatar/');
        } else {
            fclose($file);
        }


        return $ret;
    }
} 