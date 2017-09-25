<?php

use custom\exceptions\ApiException;
use custom\helpers\Midrepo;
use custom\helpers\Responder;

class ChatController extends BaseController
{


    public function index()
    {
        $spaceId = Midrepo::getOrFail('space')->id;

        if(Input::has('message') && Input::get('message') != '')
        {
            $msg = new Chat;
            $msg->user_id = Auth::user()->id;
            $msg->space_id = $spaceId;
            $msg->message = Input::get('message');
            $msg->save();
        }

        $fromId = Input::get('fromid',0);
        $chat = [];

        if($fromId == 0)
        {
            $fromId = Chat::where('space_id', $spaceId)->max('id');
            if(isset($fromId))
                $chat = Chat::where('space_id', $spaceId)->where('id', '>=', $fromId)->with('user')->get();

        } else {
            $chat = Chat::where('space_id', $spaceId)->where('id', '>', $fromId)->with('user')->get();

        }

        return Responder::json(true)->withDataTransform($chat,'ChatTransformer')->send();
    }

}