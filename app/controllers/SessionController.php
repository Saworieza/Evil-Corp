<?php

use custom\helpers\Responder;

/**
 * Class SessionController
 */
class SessionController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        $validator = Validator::make(
            Input::all(),
            [
                'email'    => 'required',
                'password' => 'required'
            ]
        );

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }

        $userdata = [
            'email'    => Input::get('email'),
            'password' => Input::get('password'),
            'state'    => '2'
        ];

        if (!Auth::attempt($userdata, Input::get('remember'))) {
            return Responder::json(false)->withMessage('invalid_email_or_password')->send();
        }

        Auth::user()->last_login = now();
        Auth::user()->save();

        return Responder::json(true)->send();

    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');

    }


}