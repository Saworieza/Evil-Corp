<?php

use custom\helpers\Responder;

/**
 * Class HomeController
 */
class HomeController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $spaces = Auth::user()->spaces()
            ->with('users')
            ->where('role', '>=', ROLE_MEMBER);

        $spaces = $spaces->where('active',1);

        $spaces = $spaces->orderBy('title')
            ->get()->toArray();

        $userId = Auth::user()->id;

        $sql = "select space_id, count(starable_id) as stars " .
            "from content join stars on (content.id = stars.starable_id and stars.starable_type='Content') " .
            "and stars.user_id = $userId group by space_id";

        $stars = DB::select(DB::raw($sql));

        foreach ($spaces as &$space) {

            $space['total'] = DB::table('content')->where('space_id', $space['id'])->count();

            $count = DB::table('content')
                ->select(DB::raw('count(*) as content_count, DATE(created_at) as date_count'))
                // ->where('status', '<>', 1)
                ->where('space_id', $space['id'])
                ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime("-7 days")))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            if ($item = findWhere($stars, ['space_id' => $space['id']])) {
                $space['stars'] = $item->stars;
            } else {
                $space['stars'] = 0;

            }
            $space['new_posts'] = $count;
        }


        $sql = "select spaces.id, spaces.title, spaces.access, spaces.code, spaces.description from spaces " .
            "left join space_user on (spaces.id = space_user.space_id and space_user.user_id = $userId) " .
            "where active=1 and (access = 'PU' and (space_user.role is null or space_user.role < 2)) or (access = 'PR' and space_user.role = 1) " .
            "order by title";

        $joinable = DB::select(DB::raw($sql));

        return Responder::json(true)
            ->withDataTransform($spaces, 'HomeSpacesTransformer', 'spaces')
            ->withDataTransform($joinable, 'HomeJoinTransformer', 'join')
            ->send();


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContent()
    {
        $spaces = DB::table('spaces')->join('space_user', 'spaces.id', '=', 'space_user.space_id')
            ->where('active',1)
            ->where('space_user.user_id', Auth::user()->id)
            ->where('role', '>=', ROLE_MEMBER)
            ->get();


        $spaces = array_pluck($spaces, 'space_id');

        if (count($spaces) > 0) {
            $content = Content::whereIn('space_id', $spaces)
                ->with(['comments'=> function($q){
                    $q->with('user');
                }])
                ->with('space')
                ->with('user')
                ->orderBy('CREATED_AT', 'DESC')
                ->take(20)
                ->get();


            return Responder::json(true)->withDataTransform($content, 'HomeContentTransformer')->send();

        }


    }

}
