<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;

/**
 * Class SpaceController
 */
class SpaceController extends BaseController
{

    /**
     * Delete space
     * Route: delete:/space/{spaceCode}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $space   = Midrepo::getOrFail('space');
        $spaceId = $space->id;

        $content = Content::where('space_id', $spaceId);

        // delete observers fired this way
        foreach ($content as $cnt) {
            $cnt->delete();
        }

        $space->delete();

        return Responder::json(true)->send();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $spaces = Auth::user()->spaces()
            ->where('role', '>=', ROLE_MEMBER);

        if(!Auth::user()->admin)
            $spaces = $spaces->where('active',1);

        $spaces = $spaces->orderBy('title')
               ->get()->toArray();

        return Responder::json(true)->withDataTransform($spaces, 'SpaceListTransformer')->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne()
    {

        $space = Midrepo::getOrFail('space');

        if(! $space->active && !Auth::user()->admin)
            return Responder::json(false)->withMessage('unauthorized')->send();


        $space = $space->load('users');
        $space = $space->load(
            [
                'tags' => function ($q) {
                        $q->orderBy('counter', 'DESC');
                    }
            ]
        );

        DB::table('space_user')->where('user_id', Auth::user()->id)->where('space_id', $space->id)->update(
            ['last_visit' => now()]
        );


        $space->role = Auth::user()->inSpace($space->id);

        return Responder::json(true)->withDataTransform($space, 'SpaceTransformer')->send();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {

        $space = Midrepo::getOrFail('space');

        $users = $space->users()->where('users.id', '<>', Auth::user()->id)->get();

        return Responder::json(true)->withDataTransform($users, 'AllUsersTransformer')->send();

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function userJoin()
    {
        $space = Midrepo::getOrFail('space');

        if ($space->access == 'PU' || ($space->access == 'PR' && Auth::user()->inSpace($space->id) == ROLE_INVITED)) {
            $user = $space->users()->where('user_id', Auth::user()->id)->first();
            if (!$user) {
                $space->users()->attach(Auth::user()->id, ['role' => ROLE_MEMBER]);
            } else {
                $user->pivot->role = ROLE_MEMBER;
                $user->pivot->save();
            }

            return Responder::json(true)->send();
        }

        return Responder::json(false)->withMessage('access_denied')->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNoUsers()
    {

        $spaceId = Midrepo::getOrFail('space')->id;

        $sql   = "select users.id, users.full_name " .
            "from users left join space_user on (users.id = space_user.user_id and space_id = $spaceId) " .
            "where users.state >= 2 and space_user.role is null order by full_name";
        $users = DB::select(DB::raw($sql));

        return Responder::json(true)->withData($users)->send();

    }

    /**
     * Activate/Deactivate a space
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate($spaceId)
    {
        $space = Space::findOrFail($spaceId);

        $space->active = ! $space->active;
        $space->save();

        return Responder::json(true)->withData($space->active)->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {

        $space = Midrepo::getOrFail('space');

        $ini   = Input::get('ini') . '%';
        $users = User::where('full_name', 'like', $ini)
            ->where('state', '=', USER_STATE_ACTIVE)
            ->whereHas(
                'spaces', function ($query) use ($space) {
                    $query->where('space_id', $space->id)->where('role', '>=', Input::get('role', ROLE_MEMBER));

                }
            )
            ->where('id', '<>', Auth::user()->id)
            ->with('spaces')
            ->orderBy('full_name')
            ->get();

        return Responder::json(true)->withData($users)->send();

    }

    /**
     * Update space
     * Route: put:/api/space/{spaceCode}
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function update()
    {

        $space = Midrepo::getOrFail('space');

        $space->title       = Input::get('title');
        $space->description = Input::get('description');
        $space->setFromInput('access');
        $space->setFromInput('active');
        $space->options = json_encode(Input::get('options'));

        if (!$space->save()) {
            return Responder::json(false)->withValidator($space->validator)->send();
        }

        return Responder::json(true)->withData($space)->send();
    }


    /**
     * Stores a new space
     *
     * @throws custom\exceptions\ApiException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $space = new Space;
        $space->setFromInput('title');
        $space->setFromInput('description');

        $space->setFromInput('access');
        $space->setFromInput('active');
        $space->user_id = Auth::user()->id;
        $space->options = json_encode(Input::get('options'));

        if(preg_match('/[^\\p{Common}\\p{Latin}]/u', $space->title))
        {
            $space->code = md5(date('U'));
        } else {
            $space->code = makeSlugs($space->title);

        }

        if (!$space->save()) {
            return Responder::json(false)->withValidator($space->validator)->send();
        }

        $space->users()->attach(Auth::user()->id, ['role' => ROLE_MODERATOR]);

        return Responder::json(true)->withData($space->code)->send();

    }


    public function getAdminList()
    {
        $spaces = Space::all()->toArray();
        foreach($spaces as &$space)
        {
            $space['content_count'] = Content::where('space_id', $space['id'])->count();

            $space['user_count'] =  DB::table('space_user')->where('space_id', $space['id'])->where('role', '>=', ROLE_INVITED)->count();

            $space['last_visit'] =  DB::table('space_user')->where('space_id', $space['id'])->max('last_visit');

            $space['created_by'] =  User::where('id', $space['user_id'])->pluck('full_name');

        }

        return Responder::json(true)->withDataTransform($spaces, 'AdminSpacesTransformer')->send();
    }


}
