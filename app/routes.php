<?php

use custom\exceptions\ApiException;
use custom\helpers\Responder;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Pages
 */
Route::get(
    '/',
    [
        'before' => 'auth',
        function () {
            
            return View::make('app');
        }
    ]
);

Route::get(
    '/login',
    function () {

        if (!Config::get('app.installed')) {
            return Redirect::guest('install');
        }


        return View::make('auth.login');

    }
);

Route::get(
    '/lang',
    function () {

        $contents = View::make('lang');
        $response = Response::make($contents);
        $response->header('Content-Type', 'application/javascript');
        return $response;
    }
);


Route::get(
    '/modules',
    function () {

     //   return getModuleTemplates();
        $modules = getModuleFiles('client.js');
        $js = '';
        foreach($modules as $module){
            $js .= file_get_contents($module);
        }

        $tpls = '';
        $tplFiles = getModuleTemplates();
        foreach($tplFiles as $tplFile)
        {
            $tplContent = addslashes(file_get_contents(app_path($tplFile)));
            $tplContent = trim(preg_replace('/\s+/', ' ', $tplContent));
            $tpls .= '$templateCache.put("'.$tplFile.'", "'.$tplContent.  '");';
        }

        $tpls = 'app.run(function($templateCache) {'.$tpls. '});';

        $response = Response::make($js.'; '.$tpls);
        $response->header('Content-Type', 'application/javascript');
        return $response;
    }
);

/**
 * Install routes
 */
Route::get('/install', 'InstallController@index');
Route::post('/install', ['before' => 'installed', 'uses' => 'InstallController@install']);
Route::get('/install/servercheck', ['before' => 'installed', 'uses' => 'InstallController@serverCheck']);
Route::get('/install/testdb', ['before' => 'installed', 'uses' => 'InstallController@testDb']);
Route::get('/install/testfilesfolder', ['before' => 'installed', 'uses' => 'InstallController@testFilesFolder']);
Route::get('/install/testemail', ['before' => 'installed', 'uses' => 'InstallController@testEmail']);


/**
 * Session routes
 */
Route::post('/login', ['before' => 'csrf', 'uses' => 'SessionController@login']);
Route::get('/logout', ['before' => 'auth', 'uses' => 'SessionController@logout']);

/**
 * User activation & register routes
 */
Route::get('/activation/{activationCode}', 'ActivationController@create');
Route::post('/activation', ['before' => 'csrf', 'uses' => 'ActivationController@store']);
Route::post('/register', ['before' =>'notInDemo', 'uses' => 'UserController@register']);

/**
 * Password routes
 */
Route::get('/password/{activationCode}', 'PasswordController@update');
Route::post('/password', ['before' => 'csrf', 'uses' => 'PasswordController@store']);
Route::put('/password/{email}', 'PasswordController@forgot');


/**
 * API routes
 */
Route::group(
    ["prefix" => "/api/v1/", "before" => ["authApi", "member"], 'after'=>'thereisMail'],
    function () {

        Route::get('file/{code}/{preview?}', ['before' => 'authFile', 'uses' => 'AttachmentController@getFile']);
        Route::get(
            'csv/{contentId}',
            ['before' => 'authContent', 'uses' => 'ContentController@csv']
        );
        Route::group(
            [],
            function () {
                Route::get(
                    'chat/{spaceCode}',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'ChatController@index']
                );

                Route::get('search', [ 'uses' => 'SearchController@search']);

                /**
                 * Content routes
                 */
                Route::post('content/geturl', ['uses' => 'ContentController@getUrl']);
                Route::post('content/getStaticMap', ['uses' => 'ContentController@getStaticMap']);
                Route::post('content/{contentId}/share/{spaceCode}', ['before' => 'authContent|authSpaceByCode:2', 'uses' => "ContentController@share"]);
                Route::post(
                    'content/{spaceCode}/{classId}',
                    ['before' => 'authSpaceByCode:2', 'uses' => "ContentController@store"]
                );
                Route::delete(
                    'content/{contentId}',
                    ['before' => 'authContent:true', 'uses' => "ContentController@destroy"]
                );
                Route::get(
                    'content/space/{spaceCode}',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'ContentController@index']
                );
                Route::get(
                    'pool/{spaceCode}/{maxId}',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'ContentController@pool']
                );
                Route::get('content/{contentId}', ['before' => 'authContent', 'uses' => 'ContentController@getOne']);

                Route::get('viewmore/{contentId}', ['before' => 'authContent', 'uses' => 'ContentController@viewMore']);
                Route::get(
                    'vote/{contentId}/{optionIindex}',
                    ['before' => 'authContent', 'uses' => 'ContentController@vote']
                );
                Route::post(
                    'vote/{contentId}',
                    ['before' => 'authContent', 'uses' => 'ContentController@addVoteOption']
                );
                Route::get(
                    'assist/{contentId}/{assistValue}',
                    ['before' => 'authContent', 'uses' => 'ContentController@eventAssist']
                );

                /**
                 * Comments, likes and stars routes
                 */
                Route::post(
                    'comment/{className}/{id}',
                    ['before' => 'authByObject:Commentable', 'uses' => 'CommentController@store']
                );
                Route::delete('comment/{commentId}', 'CommentController@destroy');
                Route::get(
                    'comment/{className}/{id}',
                    ['before' => 'authByObject:Commentable', 'uses' => 'CommentController@getList']
                );
                Route::get(
                    'like/{className}/{id}',
                    ['before' => 'authByObject:Likeable', 'uses' => 'LikeController@store']
                );
                Route::get(
                    'star/{className}/{id}',
                    ['before' => 'authByObject:Starable', 'uses' => 'StarController@store']
                );

                /**
                 * Task routes
                 */
                Route::post('task', ['uses' => 'TaskController@store']);
                Route::put('task/{taskId}', ['before' => 'authTask', 'uses' => 'TaskController@update']);
                Route::get('task', ['before' => '', 'uses' => 'TaskController@getList']);
                Route::get('task/{taskId}', ['before' => '', 'uses' => 'TaskController@getOne'])->where(
                    'taskId',
                    '[0-9]+'
                );
                Route::get('task/counters', 'TaskController@getCounters');
                Route::get('task/archive/{taskId}', ['before' => 'authTask', 'uses' => 'TaskController@archive']);
                Route::delete('task/{taskId}', 'TaskController@destroy');

                /**
                 * Tag routes
                 */
                Route::get('tag/{spaceCode}', ['before' => 'authSpaceByCode', 'uses' => 'TagController@getList']);
                Route::delete('tag/{tagId}', ['before' => 'authByTagId', 'uses' => 'TagController@destroy']);
                Route::put('tag/{tagId}/{newName}', ['before' => 'authByTagId', 'uses' => 'TagController@rename']);
                Route::put(
                    'tag/{tagId}/replacewith/{withId}',
                    ['before' => 'authByTagId', 'uses' => 'TagController@replace']
                );

                /**
                 * File routes
                 */
                Route::post('file', ['uses' => 'AttachmentController@upload']);
                Route::get('file', ['uses' => 'AttachmentController@getList']);
                Route::delete('file/{code}', ['before' => 'authFile:3', 'uses' => 'AttachmentController@destroy']);
                Route::put(
                    'file/{code}/move/{folderId}',
                    ['before' => ['authFile:3', 'authFolder'], 'uses' => 'AttachmentController@move']
                );

                Route::put(
                    'file/{code}/copy/{folderId}',
                    ['before' => ['authFile', 'authFolder'], 'uses' => 'AttachmentController@copy']
                );

                /**
                 * Space  routes
                 */
                Route::get('space', ['uses' => 'SpaceController@getList']);
                Route::get('space/{spaceCode}', ['before' => 'authSpaceByCode:2', 'uses' => 'SpaceController@getOne']);
                Route::get(
                    'space/{spaceCode}/users',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'SpaceController@getUsers']
                );

                Route::get(
                    'space/{spaceCode}/allusers',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'SpaceController@getAllUsers']
                );

                Route::get(
                    'space/{spaceCode}/nousers',
                    ['before' => 'authSpaceByCode:2', 'uses' => 'SpaceController@getNoUsers']
                );


                Route::post('space', ['before'=>'authUserProperties:create_spaces', 'uses' => 'SpaceController@store']);
                Route::put('space/{spaceCode}', ['before' => 'authSpaceByCode:3', 'uses' => 'SpaceController@update']);
                Route::put(
                    'space/{spaceCode}/join',
                    ['before' => 'authSpaceByCode', 'uses' => 'SpaceController@userJoin']
                );

                Route::delete(
                    'space/{spaceCode}',
                    ['before' => 'authDeleteSpace|notInDemo', 'uses' => 'SpaceController@destroy']
                );

                /**
                 * User routes
                 */
                Route::get('user', ['uses' => 'UserController@getList']);

                Route::post(
                    'newuserform',
                    ['before' => 'authUserProperties:admin', 'uses' => 'UserController@newUserForm']
                );
                Route::get(
                    'adminuser',
                    ['before' => 'authUserProperties:admin', 'uses' => 'UserController@getAdminList']
                );

                Route::put(
                    'user/makemoderator/{spaceCode}',
                    ['before' => 'authUserProperties:admin', 'uses' => 'UserController@makeModerator']
                );

                Route::get(
                    'adminspaces',
                    ['before' => 'authUserProperties:admin', 'uses' => 'SpaceController@getAdminList']
                );
                Route::put(
                    'adminspaces/{spaceId}/activate',
                    ['before' => 'authUserProperties:admin', 'uses' => 'SpaceController@activate']
                );

                Route::get('user/{id}', ['before' => 'authUserProperties:admin', 'uses' => 'UserController@getOne']);
                Route::put(
                    'user/switchrole/{userId}/{spaceCode}',
                    ['before' => 'authSpaceByCode:3|notInDemo', 'uses' => 'UserController@switchRole']
                );
                Route::put(
                    'user/state/{userId}/{state}',
                    ['before' => 'authUserProperties:admin|notInDemo', 'uses' => 'UserController@updateState']
                );
                Route::put(
                    'user/admin/{userId}',
                    ['before' => 'authUserProperties:admin|notInDemo', 'uses' => 'UserController@updateAdmin']
                );
                Route::put(
                    'user/createspaces/{userId}',
                    ['before' => 'authUserProperties:admin|notInDemo', 'uses' => 'UserController@updateCreateSpaces']
                );
                Route::put(
                    'user/renew/{userId}',
                    ['before' => 'authUserProperties:admin', 'uses' => 'UserController@reNewInvitation']
                );
                Route::post(
                    'user/invite/{spaceCode}',
                    ['before' => 'authSpaceByCode:3', 'uses' => 'UserController@inviteToSpace']
                );
                Route::post(
                    'user',
                    ['before' => 'authUserProperties:admin|notInDemo', 'uses' => 'UserController@store']
                );
                Route::get(
                    'quit/{spaceCode}',
                    ['before' => 'authSpaceByCode:2|notInDemo', 'uses' => 'UserController@quit']
                );
                Route::delete(
                    'user/{id}',
                    ['before' => 'authSpaceByCode:3|notInDemo', 'uses' => 'UserController@destroy']
                );

                /**
                 * Profile routes
                 */
                Route::get('profile/{id}', ['uses' => 'ProfileController@getOne']);
                Route::get('profile/activity/{userid}', ['uses' => 'ProfileController@getActivity']);
                Route::get('profile', ['uses' => 'ProfileController@getList']);
                Route::post('profile', ['before' => 'notInDemo', 'uses' => 'ProfileController@update']);
                Route::post('profile/avatar', ['before' => 'notInDemo', 'uses' => 'ProfileController@updateAvatar']);
                Route::post(
                    'profile/password',
                    ['before' => 'notInDemo', 'uses' => 'ProfileController@updatePassword']
                );
                Route::post(
                    'profile/settings',
                    ['before' => 'notInDemo', 'uses' => 'ProfileController@updateSettings']
                );

                /**
                 * Folder routes
                 */
                Route::get('folder', ['before' => 'authSpaceByCode:2', 'uses' => 'FolderController@getList']);
                Route::post('folder', ['before' => 'authSpaceByCode:2', 'uses' => 'FolderController@store']);
                Route::put('folder/{folderId}', ['before' => 'authFolder:3', 'uses' => 'FolderController@update']);
                Route::post('folder/{folderId}', ['before' => 'authFolder', 'uses' => 'FolderController@upload']);
                Route::delete('folder/{folderId}', ['before' => 'authFolder:3', 'uses' => 'FolderController@destroy']);

                /**
                 * Wiki routes
                 */
                Route::get('wiki', ['before' => 'authSpaceByCode:2', 'uses' => 'WikiController@getList']);
                Route::get('wiki/{wikiId}', ['before' => 'authWiki', 'uses' => 'WikiController@getOne']);
                Route::get('wiki/{wikiId}/body', ['before' => 'authWiki', 'uses' => 'WikiController@getBody']);

                Route::post('wiki', ['before' => 'authSpaceByCode:2', 'uses' => 'WikiController@store']);
                Route::put('wiki/{wikiId}', ['before' => 'authWiki', 'uses' => 'WikiController@update']);
                Route::delete('wiki/{wikiId}', ['before' => 'authWiki:3', 'uses' => 'WikiController@destroy']);

                /**
                 * Events routes
                 */
                Route::get('event', ['uses' => 'EventController@getList']);
                Route::put('event/{id}', ['uses' => 'EventController@update']);
                Route::get('calendar', ['uses' => 'EventController@getCalendar']);

                /**
                 * Message routes
                 */
                Route::post('message', ['uses' => 'MessageController@store']);
                Route::get('message', ['uses' => 'MessageController@getList']);
                Route::put('message/{messageId}', ['uses' => 'MessageController@update']);
                Route::get(
                    'mainpool',
                    function () {
                        $msgCount = UserMessage::where('to_id', Auth::user()->id)->where('read', 0)->count();
                        return Responder::json(true)->withData(['msgs' => $msgCount])->send();
                    }
                );

                /**
                 * Home routes
                 */
                Route::get('home', ['uses' => 'HomeController@index']);
                Route::get('home/content', ['uses' => 'HomeController@getContent']);

                Route::put('config', ['before' => 'authUserProperties:admin|notInDemo', function(){

                    if( ! is_writable(base_path('.env.php')))
                        return Responder::json(false)->send();

                    $ds = Input::get('default_space');

                    if( $ds >0 && $ds !== env('DEFAULT_SPACE',0))
                    {
                        $users = DB::table('users')->where('state', '<', 3)->lists('id');

                        foreach($users as $user)
                        {
                            if(User::find($user)->inSpace($ds) < 0)
                                DB::table('space_user')->insert(['user_id'=>$user, 'space_id'=>$ds , 'role'=>ROLE_MEMBER]);

                        }
                    }

                    $input = Input::all();
                    $input = array_change_key_case($input, CASE_UPPER);

                    $input['DEBUG']           = (bool)$input['DEBUG'] ;
                    $input['HIDE_MODERATORS'] = (bool)$input['HIDE_MODERATORS'] ;
                    $input['HIDE_PEOPLE']     = (bool)$input['HIDE_PEOPLE'] ;
                    $input['HIDE_TASKS']      = (bool)$input['HIDE_TASKS'] ;
                    $input['HIDE_CALENDAR']   = (bool)$input['HIDE_CALENDAR'] ;
                    $input['HIDE_TICKETS']    = (bool)$input['HIDE_TICKETS'] ;
                    $input['NO_EMAIL_QUEUE']  = (bool)$input['NO_EMAIL_QUEUE'] ;
                    $input['DEFAULT_CRETE_SPACES']  = (bool)$input['DEFAULT_CRETE_SPACES'] ;
                    $input['PEOPLE_SHOW_PHONE']  = (bool)$input['PEOPLE_SHOW_PHONE'] ;
                    $input['PEOPLE_SHOW_ORG']  = (bool)$input['PEOPLE_SHOW_ORG'] ;
                    $input['PROFILE_GITHUB']   = (bool)$input['PROFILE_GITHUB'] ;
                    $input['PROFILE_SKYPE']    = (bool)$input['PROFILE_SKYPE'] ;
                    $input['PROFILE_GOOGLE']   = (bool)$input['PROFILE_GOOGLE'] ;
                    $input['PROFILE_LINKEDIN'] = (bool)$input['PROFILE_LINKEDIN'] ;
                    $input['PROFILE_FACEBOOK'] = (bool)$input['PROFILE_FACEBOOK'] ;
                    $input['PROFILE_TWITTER']   = (bool)$input['PROFILE_TWITTER'] ;
                    $input['DEFAULT_SPACE']     = (int)$input['DEFAULT_SPACE'] ;
                    $input['INVITATION_EXPIRE'] = (int)$input['INVITATION_EXPIRE'] ;

                    // not edited keys
                    $input['APP_TITLE'] = env('APP_TITLE', '');
                    $input['APP_SUBTITLE'] = env('APP_SUBTITLE', '');
                    $input['APP_FOOTER'] = env('APP_FOOTER', '');
                    $input['INSTALLED'] = env('INSTALLED', '');
                    $input['MAX_IMAGE_W'] = env('MAX_IMAGE_W', 960);
                    $input['MAX_IMAGE_H'] = env('MAX_IMAGE_H', 540);
                    $input['DB_HOST'] = env('DB_HOST', '');
                    $input['DB_DATABASE'] = env('DB_DATABASE', '');
                    $input['DB_USERNAME'] = env('DB_USERNAME', '');
                    $input['DB_PASSWORD'] = env('DB_PASSWORD', '');
                    $input['DB_CHARSET'] = env('DB_CHARSET', '');
                    $input['DB_COLLATION'] = env('DB_COLLATION', '');
                    $input['DB_PREFIX'] = env('DB_PREFIX', '');
                    $input['DEMO'] = env('DEMO', false);

                    unset($input['ISW']);

                    $content = "<?php \r\n return ".var_export($input, true).';';

                   // dd(base_path('.env.php'));
                    file_put_contents(base_path('.env.php'), $content);
                    file_put_contents(base_path('.env.local.php'), $content);

                    return Responder::json(true)->send();

                }]);

                Route::get('syslog', ['before' => 'authUserProperties:admin', function(){

                    $log = 'empty';
                    if(file_exists(storage_path().'/logs/linkr.log'))
                        $log = file_get_contents(storage_path().'/logs/linkr.log');

                    return Responder::json(true)->withData($log)->send();
                }]);

                Route::delete('syslog', ['before' => 'authUserProperties:admin', function(){

                    unlink(storage_path().'/logs/linkr.log');
                    return Responder::json(true)->send();

                }]);

                Route::get('admin/appinfo', ['before' => 'authUserProperties:admin', function(){
                    $ret=[
                        'isw' => is_writable(base_path('.env.php')),
                        'file'=>base_path('.env.php'),
                        'version'=>Config::get('app.version'),
                        'debug'=>(int)Config::get('app.debug'),
                        'locale'=>env('LOCALE', ''),
                        'db_host'=>env('DB_HOST', ''),
                        'db_database'=>env('DB_DATABASE', ''),
                        'mail_driver'=>env('MAIL_DRIVER', ''),
                        'files_folder'=>env('FILES_FOLDER', ''),
                        'mail_host'=>env('MAIL_HOST', ''),
                        'mail_port'=>env('MAIL_PORT', ''),
                        'mail_from_address'=>env('MAIL_FROM_ADDRESS', ''),
                        'mail_from_name'=>env('MAIL_FROM_NAME', ''),
                        'mail_encryption'=>env('MAIL_ENCRYPTION', ''),
                        'mail_username'=>env('MAIL_USERNAME', ''),
                        'mail_password'=>env('MAIL_PASSWORD', ''),
                        'mail_sendmail'=>env('MAIL_SENDMAIL', ''),
                        'mail_mandrill_secret'=>env('MAIL_MANDRILL_SECRET', ''),
                        'mail_mailgun_domain'=>env('MAIL_MAILGUN_DOMAIN', ''),
                        'mail_mailgun_secret'=>env('MAIL_MAILGUN_SECRET', ''),
                        'hide_moderators'=>(int)env('HIDE_MODERATORS', 0),
                        'hide_people'=>(int)env('HIDE_PEOPLE', 0),
                        'hide_tasks'=>(int)env('HIDE_TASKS', 0),
                        'hide_calendar'=>(int)env('HIDE_CALENDAR', 0),
                        'hide_tickets'=>(int)env('HIDE_TICKETS', 0),
                        'self_registration_domain'=>env('SELF_REGISTRATION_DOMAIN', ''),
                        'no_email_queue'=>(int)env('NO_EMAIL_QUEUE', 0),
                        'default_crete_spaces'=>(int)env('DEFAULT_CRETE_SPACES', 1),
                        'default_space'=>(int)env('DEFAULT_SPACE',0),
                        'people_show_phone'=>(int)env('PEOPLE_SHOW_PHONE',0),
                        'people_show_org'=>(int)env('PEOPLE_SHOW_ORG',0),
                        'profile_github'=>(int)env('PROFILE_GITHUB',1),
                        'profile_skype'=>(int)env('PROFILE_SKYPE',1),
                        'profile_google'=>(int)env('PROFILE_GOOGLE',1),
                        'profile_linkedin'=>(int)env('PROFILE_LINKEDIN',1),
                        'profile_facebook'=>(int)env('PROFILE_FACEBOOK',1),
                        'profile_twitter'=>(int)env('PROFILE_TWITTER',1),
                        'invitation_expire'=>(int)env('INVITATION_EXPIRE',1),
                        'theme'=>env('THEME', 'default')


                    ];
                    return Responder::json(true)->withData($ret)->send();
                }]);

                Route::get(
                    'admin/maintenance',
                    [
                        'before' => 'authUserProperties:admin|notInDemo',
                        function () {
                            Artisan::call('linkr:maintenance');
                            return Responder::json(true)->withAlert('success')->withMessage('done')->send();
                        }
                    ]
                );

                Route::get(
                    'admin/upgrade',
                    [
                        'before' => 'authUserProperties:admin|notInDemo',
                        function () {
                            Artisan::call('linkr:upgrade');
                            return Responder::json(true)->withAlert('success')->withMessage('done')->send();
                        }
                    ]
                );
            }
        );

    }
);

Route::get('sendmail', function () {
            $output = new BufferedOutput;
            Artisan::call('linkr:sendmail', [], $output);
            return Responder::json(true)->withData($output)->withMessage('done')->send();
        }
);

Config::set('app.version', '1.7');

$module_routes = getModuleFiles('routes.php');

foreach($module_routes as $route_file)
{
    include $route_file;
}