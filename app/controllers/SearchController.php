<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;

class SearchController extends BaseController
{

    public function search()
    {
        $query = Input::get('query');
        $userId = Auth::user()->id;
        $result = DB::select(DB::raw(
            "select content.id as id, spaces.title, spaces.code, users.full_name, users.id as user_id, content.class_id, content.created_at, content.content_text, MATCH(body) AGAINST ('$query' in boolean mode) as ".
            "relevance FROM ftindex ".
            "join content on (ftindex.indexable_id = content.id and ftindex.indexable_type='Content') ".
            "join users on (content.user_id = users.id) ".
            "join spaces on (content.space_id = spaces.id) ".
            "join space_user on (spaces.id = space_user.space_id and space_user.user_id = $userId and role >=2) ".
            "WHERE MATCH(body) AGAINST ('$query' ".
            "in boolean mode) order by relevance DESC LIMIT 100"));




        return Responder::json(true)->withData($result)->send();

    }
}