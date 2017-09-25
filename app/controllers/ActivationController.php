<?php

use custom\exceptions\ApiException;
use custom\helpers\Responder;

/**
 * Class ActivationController
 *
 * User activation logic
 */
class ActivationController extends BaseController
{


    /**
     * User activation Form
     * Route: get:/activation/{activationCode}
     *
     * @param $activationCode
     * @return $this|\Illuminate\View\View
     */
    public function create($activationCode)
    {
        $user = User::where('activation_code', $activationCode)
            ->where('state', USER_STATE_INVITED)
            ->first();

        if (empty($user)) {
            return View::make('errors.404');
        }

        if ($user->activation_expire < now()) {
            return View::make('errors.message')->with(['msg' => trans('messages.action_expired')]);
        }

        return View::make('auth.activate')->with(['user' => $user]);
    }


    /**
     * Activate user
     * Route: post:/activation
     *
     * @throws custom\exceptions\ApiException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        if (!Input::has('activation_code')) {
            return Responder::json(false)->withMessage('bad_request')->send();
        }

        $user = User::where('activation_code', Input::get('activation_code'))
            ->where('state', USER_STATE_INVITED)
            ->first();

        if (empty($user)) {
            return Responder::json(false)->withMessage('bad_request')->send();
        }

        if ($user->activation_expire < now()) {
            return Responder::json(false)->withMessage('bad_request')->send();
        }

        $validator = Validator::make(
            Input::all(), [
                'full_name'             => 'required',
                'email'                 => 'required|email',
                'password'              => 'required|min:6',
                'password_confirmation' => 'required|same:password'
            ]
        );

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }

//        $user->first_name        = ucwords(strtolower(Input::get('full_name')));
//        $user->last_name         = ucwords(strtolower(Input::get('last_name')));

        $user->full_name = ucwords(strtolower(Input::get('full_name')));
        $user->password  = Hash::make(Input::get('password'));

        $code = preg_replace('/\s+/', '', $user->full_name);

        $user->code              = $code;
        $user->state             = USER_STATE_ACTIVE;
        $user->activation_date   = now();
        $user->activation_code   = null;
        $user->activation_expire = null;
        $user->save();


        $user->login();

        return Responder::json(true)->send();

    }


}