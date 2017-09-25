<?php

use custom\helpers\Responder;

/**
 * Class ProfileController
 */
class ProfileController extends BaseController
{

    public function getActivity($userid)
    {

        $userSpaces = DB::table('space_user')->where('user_id', $userid)->where('role', '>=', ROLE_MEMBER)->lists('space_id');
        $mySpaces = DB::table('space_user')->where('user_id', Auth::user()->id)->where('role', '>=', ROLE_MEMBER)->lists('space_id');

        $spaces = array_intersect($userSpaces, $mySpaces);

        if(empty($spaces))
        {
            return Responder::json(true)->withData([])->send();
        }

        $content = Content::whereIn('space_id', $spaces)
                ->where('user_id', $userid)
                ->with('space')
                ->orderBy('created_at', 'desc')
                ->take(40)
                ->get();

        return Responder::json(true)->withDataTransform($content, 'ProfileContentTransformer')->send();

    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function getOne($id)
    {
        $user = User::find($id);

        if (!isset($user)) {
            throw new ApiException('not_found');
        }

        if ($user->state < USER_STATE_ACTIVE) {
            throw new ApiException('not_found');
        }

        return Responder::json(true)->withDataTransform($user, "UserProfileTransformer:$id")->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $users = User::where('state', '>=', USER_STATE_ACTIVE)
            ->orderBy('full_name')
            ->get();

        $custom_fields = [];
        if(file_exists( base_path('profile_fields.php')))
        {
            $custom_fields = include base_path('profile_fields.php');
        }

        return Responder::json(true)->withDataTransform($users, 'PeopleTransformer')->withExtraData($custom_fields)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar()
    {
        $file = Input::file('file');

        $newName = Auth::user()->id . '.' . strtolower($file->getClientOriginalExtension());

        $file->move(public_path() . '/assets/avatar/', $newName);

        $img = public_path() . '/assets/avatar/' . $newName;

        $imgout = public_path() . '/assets/avatar/' . Auth::user()->id . '.jpg';

        if (exif_imagetype($img) > 0) {
            $img = Image::make($img);
            $img->fit(100, 100);
            $img->save($imgout);
            return Response::json(["success" => true]);

        }
        return Response::json(["success" => false]);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSettings()
    {
        $user = Auth::user();
        foreach (['content', 'content_starred', 'like', 'mention', 'invite', 'post'] as $event) {

            $user['notif_' . $event] = Input::get('notif_' . $event, false);
            $user['email_' . $event] = Input::get('email_' . $event, false);
        }

        $user['email_private_msg'] = Input::get('email_private_msg', false);

        $user->save();
        return Responder::json(true)->withAlert('success')->withMessage('done')->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {

        $validator = Validator::make(
            Input::all(), [
                "full_name" => 'required|min:4'
            ]
        );

        if (!$validator->passes()) {
            return Responder::json(false)->withValidator($validator)->send();
        }

        $user               = Auth::user();
        $user->full_name    = Input::get('full_name');
        $user->email        = Input::get('email');
        $user->organization = Input::get('organization');
        $user->position     = Input::get('position');
        $user->phone        = Input::get('phone');
        $user->skype        = Input::get('skype');
        $user->facebook     = Input::get('facebook');
        $user->github       = Input::get('github');
        $user->googleplus   = Input::get('googleplus');
        $user->twitter      = Input::get('twitter');
        $user->linkedin     = Input::get('linkedin');
        $user->showemail    = Input::get('showemail');

        // Custom fields
        if(file_exists( base_path('profile_fields.php')))
        {
            $customFields = include base_path('profile_fields.php');
            foreach($customFields as $field)
            {
                if(Input::has('custom_'.$field['name']))
                {
                    $user['custom_'.$field['name']] = Input::get('custom_'.$field['name']);
                }
            }
        }


        if (!$user->save()) {
            return Responder::json(false)->withValidator($user->validator)->send();
        }

        return Responder::json(true)->withAlert('success')->withMessage('done')->send();
    }

    /**
     * @param $folderId
     */
    public function destroy($folderId)
    {


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword()
    {

        $newPass  = Input::get('new');
        $oldPass  = Input::get('old');
        $confPass = Input::get('conf');

        $validator = Validator::make(
            array('password' => $newPass, 'password_confirm' => $confPass),
            array('password' => 'required|min:6|max:20|same:password_confirm')
        );
        if ($validator->passes()) {

            $u = User::findOrFail(Auth::user()->id);
            if (Hash::check($oldPass, $u->password)) {
                $u->password = Hash::make($newPass);
                $u->save();
                return Responder::json(true)->withAlert('success')->withMessage('done')->send();
            } else {
                return Responder::json(false)->withMessage("incorrect_password")->send();

            }

        } else {
            return Responder::json(false)->withValidator($validator)->send();
        }

    }

}