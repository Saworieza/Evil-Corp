<?php

use custom\helpers\Midrepo;
use \custom\helpers\Responder;

/**
 * Class UserController
 */
class UserController extends BaseController
{

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne($id)
    {
        $user = User::findOrFail($id);

        return Responder::json(true)->withData($user)->send();

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function quit()
    {
        $spaceId = Midrepo::getOrFail('space')->id;

        if( env('DEFAULT_SPACE',0) == $spaceId)
            return Responder::json(false)->withMessage('cant_quit')->send();

        if (Auth::user()->inSpace($spaceId) == ROLE_MODERATOR) {
            $count = DB::table('space_user')->where('space_id', $spaceId)->where('role', ROLE_MODERATOR)->count();
            if ($count == 1) {
                return Responder::json(false)->withMessage('only_moderator_cannot_quit')->send();

            }
        }

        Auth::user()->spaces()->detach($spaceId);

        return Responder::json(true)->send();
    }

    /**
     * User list for combos
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $users = User::state(USER_STATE_ACTIVE)
            ->initials(Input::get('ini'))
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return Responder::json(true)->withData($users)->send();
    }


    /**
     * User list for site administration module
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdminList()
    {
        $users = User::orderBy('full_name')
            ->initials(Input::get('ini'))
            ->get(['id', 'full_name', 'last_login', 'state', 'email', 'admin', 'create_spaces', 'activation_expire']);

        $counter = DB::select(DB::raw('select state, count(*) as cnt from users group by state'));
        $counter_desc = [
            'active' => 0,
            'invited' => 0
        ];

        foreach($counter as $count)
        {
            if($count->state == USER_STATE_ACTIVE) $counter_desc['active']=$count->cnt;
            if($count->state == USER_STATE_INVITED) $counter_desc['invited']=$count->cnt;

        }
        return Responder::json(true)->withDataTransform($users, 'AdminUserListTransformer')->withExtraData($counter_desc)->send();
    }


    public function makeModerator($spaceCode)
    {
        $space = Space::where('code', $spaceCode)->first();
        if(empty($space))
            return Responder::json(false)->withMessage("not_found")->send();

        $role = Auth::user()->inSpace($space->id);

        if($role < ROLE_MODERATOR)
        {
            if($role == -1)
            {
                DB::table('space_user')->insert(['user_id'=>Auth::user()->id, 'space_id'=>$space->id, 'role'=>ROLE_MODERATOR]);
            } else {
                DB::table('space_user')
                    ->where('user_id', Auth::user()->id)
                    ->where('space_id', $space->id)
                    ->update(['role'=>ROLE_MODERATOR]);
            }
        }

        return Responder::json(true)->withAlert('success')->withMessage('done')->send();

    }

    public function updateCreateSpaces($userId)
    {

        $user = User::findOrFail($userId);

        $user->create_spaces = !$user->create_spaces;
        $user->save();

        return Responder::json(true)->withData($user->create_spaces)->send();
    }

    /**
     * @param $userId
     * @param $spaceCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchRole($userId, $spaceCode)
    {
        $spaceId = Midrepo::getOrFail('space')->id;
        $user    = User::findOrFail($userId);
        $role    = $user->inSpace($spaceId);

        switch ($role) {
            case 0:
                $role = 2;
                break;
            case 2:
                $role = 3;
                break;
            case 3:
                $role = 0;
                break;
        }

        if ($role == 1) {
            DB::table('space_user')->where('user_id', $user->id)->where('space_id', $spaceId)->delete();

        } else {
            DB::table('space_user')->where('user_id', $user->id)->where('space_id', $spaceId)->update(
                ['role' => $role]
            );

        }

        return Responder::json(true)->withData($role)->send();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteToSpace()
    {
        $space = Midrepo::getOrFail('space');
        foreach (Input::get('users') as $user) {
            $space->users()->attach($user, ['role' => ROLE_INVITED]);
            \custom\helpers\Notificator::invite($user, $space->title);
        }
        return Responder::json(true)->send();

    }


    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAdmin($userId)
    {
        if ($userId == Auth::user()->id) {
            return Responder::json(false)->withMessage('operation_denied')->send();
        }

        $user = User::findOrFail($userId);

        $user->admin = !$user->admin;
        $user->save();

        return Responder::json(true)->withData($user->admin)->send();

    }


    /**
     * Update User State from site admin
     * @param $userId
     * @param $state
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateState($userId, $state)
    {
        if ($userId == Auth::user()->id) {
            return Responder::json(false)->withMessage('operation_denied')->send();
        }

        $user = User::findOrFail($userId);

        $result = false;



        if ($state == USER_STATE_ACTIVE && $user->state == USER_STATE_SUSPENDED) {
            $user->state = USER_STATE_ACTIVE;
            $user->save();
            $result = true;
        }

        if ($state == USER_STATE_SUSPENDED  && ($user->state == USER_STATE_ACTIVE || $user->state ==USER_STATE_INVITED)) {

            try {
                $user->delete();
                $result = true;
                $state  = USER_STATE_DELETED;
            } catch (Exception $ex) {
                $user->state = USER_STATE_SUSPENDED;
                $user->save();
                $result = true;

            }
        }


        return Responder::json($result)->withData($state)->send();

    }


    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reNewInvitation($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->state == USER_STATE_ACTIVE) {
            return Responder::json(false)->withData('operation_denied')->send();
        }

        $user->invite();
        $user->save();

        $view = checkForCustomView('emails.welcome');
        Mail::send(
            $view, ["token" => $user->activation_code], function ($message) use ($user) {
                $message->to($user->email)->subject(
                    trans('email.welcome', ['site_name' => Config::get('app.app_title', 'Linkr')])
                );
            }
        );

        return Responder::json(true)->withData($user)->send();
    }

    /**
     * User self registration
     * Route: post:/user/register
     *
     * @throws custom\exceptions\ApiException
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {

        $selfRegistration = Config::get('app.self_registration_domain', false);

        if (!$selfRegistration || $selfRegistration == '') {
            return Responder::json(false)->withMessage('operation_denied')->send();
        }

        $validator = Validator::make(Input::all(), ['email' => 'required|email|unique:users']);

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }


        if($selfRegistration != '*')
        {
            $email = getEmailDomain(Input::get('email'));

            $allowed = array_map('trim', explode(',', $selfRegistration));

            if(! in_array($email, $allowed)){
                return Responder::json(false)->withMessage('domain_not_allowed', ['domain' => $selfRegistration])->send();
            }
        }


        return $this->store();

    }


    public function newUserForm()
    {
        $validator = Validator::make(Input::all(), [
            'full_name' => 'required',
            'email'     => 'required|email|unique:users',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        $niceNames = [
            "full_name" => trans('fieldnames.full_name')
        ];
        $validator->setAttributeNames($niceNames);

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }

        $user = new User;

        $user->email     = Input::get('email');
        $user->full_name = ucwords(strtolower(Input::get('full_name')));
        $user->password  = Hash::make(Input::get('password'));
        $user->code              = preg_replace('/\s+/', '', $user->full_name);
        $user->state             = USER_STATE_ACTIVE;
        $user->activation_date   = now();
        $user->activation_code   = null;
        $user->activation_expire = null;
        $user->admin             = Input::get('admin');
        $user->create_spaces     = Input::get('create_spaces');
        $user->save();

        copy(public_path('assets/avatar/default.jpg'), public_path('assets/avatar/' . $user->id . '.jpg'));

        $defaultSpace = env('DEFAULT_SPACE',0 );
        if($defaultSpace > 0 )
        {
            Space::find($defaultSpace)->users()->attach($user, ['role' => ROLE_MEMBER]);
        }

        return Responder::json(true)->send();
    }
    /**
     * Create a new user
     * Route: post:/user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {

        $user = new User;
        $user->email = Input::get('email');
        $user->invite();

        if (!$user->save()) {
            return Responder::json(false)->withValidator($user->validator)->send();
        }

        $view = checkForCustomView('emails.welcome');

        $defaultSpace = env('DEFAULT_SPACE',0 );
        if($defaultSpace > 0 )
        {
            Space::find($defaultSpace)->users()->attach($user, ['role' => ROLE_MEMBER]);
        }

        Mail::send(
            $view, ["token" => $user->activation_code], function ($message) use ($user) {
                $message->to($user->email)->subject(
                    trans('email.welcome', ['site_name' => Config::get('app.app_title', 'Linkr')])
                );
            }
        );

        copy(public_path('assets/avatar/default.jpg'), public_path('assets/avatar/' . $user->id . '.jpg'));

        if (!Auth::check()) {
            $user->login();
        }

        return Responder::json(true)->withData($user)->send();

    }


}