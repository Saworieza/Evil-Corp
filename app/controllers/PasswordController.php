<?php
use custom\exceptions\ApiException;
use custom\helpers\Responder;

/**
 * Class PasswordController
 */
class PasswordController extends BaseController
{

    /**
     * Forgot password
     * Route: put:/password/{email}
     *
     * @param $email
     * @throws custom\exceptions\ApiException
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot($email)
    {
        $user = User::where('email', $email)
            ->whereState(USER_STATE_ACTIVE)
            ->first();

        if (empty($user)) {
            throw new ApiException('user_not_found');
        }

        $user->activation_code   = md5(rand(10, 100) . Input::get('email')) . md5(microtime());
        $user->activation_expire = date('Y-m-d H:i:s', strtotime("+1 hour"));

        if (!$user->save()) {
            throw new ApiException($user->errors);
        }

        Mail::send(
            checkForCustomView('emails.reminder'), ["token" => $user->activation_code], function ($message) use ($user) {

                $message->to($user->email)->subject(
                    trans('email.password_reset', ['site_name' => Config::get('app.app_title', 'Linkr')])
                );
            }
        );


        return Responder::json(true)->withMessage('forgot_password_mail_sent')->send();
    }


    /**
     * Password reset Form
     * Route: get:/password/{activationCode}
     *
     * @param $activationCode
     * @return $this|\Illuminate\View\View
     */
    public function update($activationCode)
    {
        $user = User::where('activation_code', $activationCode)
            ->whereState(USER_STATE_ACTIVE)
            ->first();

        if (empty($user)) {
            return View::make('errors.404');
        }

        if ($user->activation_expire < now()) {
            return View::make('errors.message')->with('msg', trans('messages.action_expired'));
        }

        arrayFilterByPath($user, 'hold', ['.activation_code', '.email']);

        return View::make('auth.passreset')->withUser($user);

    }

    /**
     * Password reset
     * Route: post:/password
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
            ->where('state', USER_STATE_ACTIVE)
            ->first();

        if (empty($user)) {
            return Responder::json(false)->withMessage('bad_request')->send();
        }

        if ($user->activation_expire < now()) {
            return Responder::json(false)->withMessage('action_expired')->send();
        }

        $validator = Validator::make(
            Input::all(), [
                'password'              => 'required|min:6',
                'password_confirmation' => 'required|same:password'
            ]
        );

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }

        $user->activation_code   = null;
        $user->activation_expire = null;
        $user->password          = Hash::make(Input::get('password'));
        $user->save();


        $user->login();

        return Responder::json(true)->send();

    }
}