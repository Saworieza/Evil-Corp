<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

use custom\helpers\Midrepo;
use custom\exceptions\ApiException;
use custom\helpers\Toolkit;

App::before(
    function ($request) {
        //
    }
);


App::after(
    function ($request, $response) {
        if( Midrepo::$apiLogin)
        {
            Auth::logout();
        }

    }
);

Route::filter('authApi', function () {


        if (!Auth::check()) {
            $value = Request::header('X-API-KEY');
            if(empty($value))
                $value = Request::get('X-API-KEY');

            if(empty($value))
                return Response::make('Unauthorized', 401);

            $user = User::where('api_key', $value)->first();

            if(empty($user))
                return Response::make('Unauthorized', 401);

            Midrepo::$apiLogin = true;

            Auth::login($user);

        }

    }
);

Route::filter(
    'auth',
    function () {
        if (Auth::guest()) {
            return Redirect::guest('login');
        }

    }
);

Route::filter(
    'installed',
    function () {
        if (Config::get('app.installed')) {
            return Response::make('Unauthorized', 401);
        }

    }
);

Route::filter(
    'member',
    function () {

        if (Auth::user()->state < USER_STATE_ACTIVE) {
            return Response::make('Unauthorized', 401);
        }
    }
);


Route::filter(
    'auth.basic',
    function () {
        return Auth::basic();
    }
);

Route::filter('thereisMail', function($route, $request, $response)
{
    if(Midrepo::$thereIsMail && env('NO_EMAIL_QUEUE', false))
        Artisan::call('linkr:sendmail');
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter(
    'guest',
    function () {
        if (Auth::check()) {
            return Redirect::to('/');
        }
    }
);

Route::filter(
    'notInDemo',
    function () {
        if (Config::get('app.demo')) {
            throw new ApiException('not_allowed_in_demo');
        }

    }
);

Route::filter(
    'token',
    function () {

        if(Request::header('Token') != csrf_token()){
            throw new Illuminate\Session\TokenMismatchException;
        }
    }
);

Route::filter(
    'csrf',
    function () {

//        if (Session::token() != Input::get('_token')) {
//            throw new Illuminate\Session\TokenMismatchException;
//        }
    }
);

Route::filter(
    'authUserProperties',
    function ($route, $request, $property) {

        if (!Auth::user()->$property) {
            return Response::json(['success' => false, 'msg' => 'msg_access_denied']);
        }

    }
);

Route::filter(
    'authContent',
    function ($route, $request, $onlyAuthor = null) {

        $content = Content::find($route->getParameter('contentId'));
        if (!isset($content)) {
            throw new ApiException('not_found');
        }

        if ($onlyAuthor == 'true') {
            if ($content->user_id !== Auth::user()->id) {
                throw new ApiException('access_denied');
            }

        } else {

            if (Auth::user()->inSpace($content->space_id) < ROLE_MEMBER) {
                throw new ApiException('access_denied');
            }
        }

        Midrepo::add('content', $content);

    }
);


Route::filter(
    'authFolder',
    function ($route, $request, $role = ROLE_MEMBER) {

        $spaceId = Folder::findOrFail($route->getParameter('folderId'))->space_id;

        if (Auth::user()->inSpace($spaceId) < $role) {
            throw new ApiException('access_denied');
        }

    }
);

Route::filter(
    'authFile',
    function ($route, $request, $minRole = ROLE_MEMBER) {

        $file = Attachment::with('attachable')->where('code', $route->getParameter('code'))->first();

        if (!$file || !$file->attachable) {
            throw new ApiException('not_found');
        }

        if ($file->user_id != Auth::user()->id) {
            $taskId  = null;
            $spaceId = null;

            if (get_class($file->attachable) == 'Task') {
                $taskId = $file->attachable->id;
            }

            if (get_class($file->attachable) == 'Folder') {
                $spaceId = $file->attachable->space_id;
            }

            if (get_class($file->attachable) == 'Content') {
                $spaceId = $file->attachable->space_id;
            }

            if (get_class($file->attachable) == 'Comment') {
                $commentable = $file->attachable->commentable;
                if (get_class($commentable) == 'Content') {
                    $spaceId = $commentable->space_id;
                }

                if (get_class($commentable) == 'Task') {
                    $taskId = $commentable->id;
                }
            }

            if (get_class($file->attachable) == 'Wiki') {
                $wiki = $file->attachable;
                if ($wiki->access == 'PR' && Auth::user()->inSpace($wiki->spce_id) < ROLE_MEMBER) {
                    throw new ApiException('access_denied');
                }
            } else {

                if (!isset($taskId) && !isset($spaceId)) {
                    throw new ApiException('access_denied');
                }

                if (isset($spaceId)) {
                    if (Auth::user()->inSpace($spaceId) < $minRole) {
                        throw new ApiException('access_denied');
                    }
                }

                if (isset($taskId)) {
                    if (DB::table('task_users')->where('task_id', $taskId)->where('user_id', Auth::user()->id)->count(
                        ) == 0
                    ) {
                        throw new ApiException('access_denied');
                    }
                }
            }

        }

        Midrepo::add('file', $file);

    }
);

Route::filter('authDeleteSpace', function($route, $request){

        $code = $route->getParameter('spaceCode');

        if (!isset($code)) {
            $code = Input::get('space_code', null);
        }

        if (!isset($code)) {
            throw new ApiException('not_found');
        }


        $space = Space::getByCode($code);

        if (!isset($space)) {
            throw new ApiException('not_found');
        }

        if(! Auth::user()->admin && Auth::user()->inSpace($space->id) < ROLE_MODERATOR)
                throw new ApiException('access_denied');

        // append spaceId to middle repository
        Midrepo::add('space', $space);

    });

Route::filter(
    'authSpaceByCode',
    function ($route, $request, $minRole = null) {

        $code = $route->getParameter('spaceCode');

        if (!isset($code)) {
            $code = Input::get('space_code', null);
        }

        if (!isset($code)) {
            throw new ApiException('not_found');
        }


        $space = Space::getByCode($code);

        if (!isset($space)) {
            throw new ApiException('not_found');
        }

        // filter space by users role if passed
        if (isset($minRole)) {
            if (Auth::user()->inSpace($space->id) < (int)$minRole) {
                throw new ApiException('access_denied');
            }
        }

        // append spaceId to middle repository
        Midrepo::add('space', $space);

    }
);


Route::filter(
    'authTask',
    function ($route, $request) {

        $task = Task::findOrFail($route->getParameter('taskId'));

        $task_role = null;

        if (Auth::user()->id == $task->user_id) {
            $task_role = TASK_ROLE_CREATOR;

        } elseif (in_array(Auth::user()->id, $task->assignedTo->lists('user_id'))) {
            $task_role = TASK_ROLE_ASSIGNED;
        }

        if (!isset($task_role)) {
            throw new ApiException('access_denied');
        }

        Midrepo::add('task_role', $task_role);
        Midrepo::add('task', $task);

    }
);

Route::filter(
    'authByTagId',
    function ($route, $request) {
        $tag = Tag::findOrFail($route->getParameter('tagId'));

        $role = Auth::user()->inSpace($tag->space_id);

        if ($role !== ROLE_MODERATOR) {
            throw new ApiException('access_denied');
        }

        if ($route->getParameter('withId')) {
            $withTag = Tag::findOrFail($route->getParameter('withId'));

            if ($withTag->space_id !== $tag->space_id) {
                throw new ApiException('access_denied');
            }

            Midrepo::add('withTag', $withTag);
        }

        Midrepo::add('tag', $tag);
    }
);


Route::filter(
    'authByObject',
    function ($route, $request, $interface = null) {
        $className = ucfirst($route->getParameter('className'));
        $objectId  = $route->getParameter('id');
        $object    = $className::find($objectId);

        if (!isset($object)) {
            throw new ApiException('not_found');
        }

        // if interface passed, checks if object class implements it
        if (isset($interface)) {
            if (!isset(class_implements(get_class($object))["custom\\interfaces\\" . $interface])) {
                throw new ApiException('not_allowed');
            }

            if (!$object->authorize($interface)) {
                throw new ApiException('access_denied');
            }
        }

        Midrepo::add('object', $object);

    }
);

Route::filter(
    'authWiki',
    function ($route, $request, $role = ROLE_MEMBER) {
        $wikiId = $route->getParameter('wikiId');
        $wiki   = Wiki::findOrFail($wikiId);

        if (!isset($wiki)) {
            throw new ApiException('not_found');
        }

        if ($wiki->created_by != Auth::user()->id) {
            if (Auth::user()->inSpace($wiki->space_id) < $role) {
                throw new ApiException('access_denied');
            }

        }

        Midrepo::add('wiki', $wiki);

    }
);