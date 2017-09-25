<?php
use custom\exceptions\ApiException;
use custom\helpers\Responder;

/*
 *
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds'

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/linkr.log');

Blade::setContentTags('<%', '%>');
Blade::setEscapedContentTags('<%%', '%%>');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/


App::error(function(Exception $exception, $code)
{

    $route = \Route::getCurrentRoute();
    $uri = isset($route) ? \Route::getCurrentRoute()->getUri() : '';
    Log::error($uri.'->'.$exception->getMessage());

    if (Request::is('api/*'))
    {
        if(Config::get('app.debug'))
        {
            return Response::json(['success'=>false, "error"=>["code"=>$code, "class"=>get_class($exception),
                                                               "msg"=>$exception->getMessage(), "trace"=>$exception->getTrace() ]]);
        } else {
            return Response::json(['success'=>false, "error"=>["code"=>$code]]);

        }
    }

});


App::error(function(ApiException $exception )
{

    $msg = $exception->msg;

    if($msg instanceof Illuminate\Validation\Validator)
    {
        return Responder::json(false)->withValidator($exception->msg)->send();

    }

    return Responder::json(false)->withMessage($exception->msg, $exception->params)->send();

});

App::error(function(\custom\exceptions\DebugException $exception )
{

    return Responder::json(false)->withData($exception->var)->send();

});


/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right badck!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/



require app_path().'/filters.php';
require app_path().'/custom/Constants.php';
require app_path().'/custom/Functions.php';
require app_path().'/custom/Macros.php';

