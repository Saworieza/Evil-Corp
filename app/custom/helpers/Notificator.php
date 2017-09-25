<?php

namespace custom\helpers;
use custom\exceptions\ApiException;
use custom\exceptions\DebugException;

class Notificator {



    private static function filterUsers($flag, $list, $merge = [])
    {
        if(count($list) == 0 ) return $merge;

        $fieldNotif = 'notif_'.strtolower($flag);
        $fieldEmail = 'email_'.strtolower($flag);

        $users = \DB::table('users')->whereRaw("($fieldNotif = 1 or $fieldEmail = 1)")
                ->whereIn('id', $list)
                ->where('state', USER_STATE_ACTIVE)
                ->get(['id','email', $fieldEmail, $fieldNotif] );

        $ret = $merge;

        foreach($users as $user)
        {

            if( $item = findWhere($ret, ['user_id'=>$user->id]))
            {
                $item['em'] = $item['em'] || $user->$fieldEmail;
                $item['nt'] = $item['nt'] || $user->$fieldNotif;
            } else {
                if($user->id != \Auth::user()->id)
                    $ret[]= ['user_id'=>$user->id, 'email'=>$user->email, 'nt'=> $user->$fieldNotif == 1, 'em'=> $user->$fieldEmail == 1];

            }
        }


        return $ret;
    }

    public static function comment($comment)
    {

        //commentable author
        $userList = static::filterUsers($comment->commentable_type, [$comment->commentable->user_id]);

        if($comment->commentable_type == 'Content')
        {
            //starred
            $starred = array_unique(\DB::table('stars')->where('starable_id', $comment->commentable->id)
                ->where('starable_type', $comment->commentable_type)
                ->lists('user_id'));

            $userList = static::filterUsers('content_starred', $starred, $userList);
            $link = \URL::to('/').'/#/post/'.$comment->commentable_id;

        } else {
            $link = '#';
        }


        $body = trans('messages.event_new_comment', [
            'user_name' => \Auth::user()->full_name,
            'type'=> trans('messages.'.($comment->commentable_type == 'Content' ? 'post' : 'task')),
            'link'=>"<a href='$link'>$comment->commentable_id</a>"]);

        $body = $body.'<br><br>"'.$comment->body.'"';

        static::dispatch($userList, $body);

    }

    public static function starred($type, $id)
    {
        $starred = array_unique(\DB::table('stars')->where('starable_id', $id)
                ->where('starable_type', $type)
                ->lists('user_id'));

        return $starred;

    }

    public static function privateMsg($userId, $body)
    {
        $user = \DB::table('users')->where('id', $userId)->where('state', USER_STATE_ACTIVE)->first();
        if($user->email_private_msg)
        {
            $message = trans('messages.event_private_msg', ['user_name'=>\Auth::user()->full_name]);
            $message .= '<br>"'.$body.'"';

            $userList[] = ['nt'=>false, 'em'=>true, 'email'=>$user->email];
            static::dispatch($userList, $message);
        }


    }

    public static function invite($userId, $spaceName)
    {
        $userList = static::filterUsers("invite", [$userId]);

        $message = trans('messages.event_invite', ['user_name'=>\Auth::user()->full_name, 'space_name'=>$spaceName]);

        static::dispatch($userList, $message);


    }


    public static function mention($userId, $contentId)
    {

        $userList = static::filterUsers('mention', [ $userId ]);

        $message = trans('messages.mentioned');

        $message .= link_to('/#post/'.$contentId, ' '.$contentId);

        static::dispatch($userList, $message);

    }

    public static function like($likeable)
    {
        $type = get_class($likeable);
        $id = ($type == 'Content') ? $likeable->id : $likeable->commentable_id;

        $link = \URL::to('/').'/#/post/'.$id;

        //likeable author
        $userList = static::filterUsers('like', [ $likeable->user_id]);

        $body = trans('messages.event_like', [
            'user_name' => \Auth::user()->full_name,
            'type'=> trans('messages.'.($type == 'Content' ? 'post' : 'comment')),
            'link'=>"<a href='$link'>$id</a>"]);

        static::dispatch($userList, $body);

    }

    public static function dispatch($userList, $message)
    {

        foreach($userList as $user){

            if($user['nt'])
                \UserMessage::create(['to_id'=>$user['user_id'], "body"=>$message]);

            if($user['em'])
            {
                \Mailer::create(['to'=>$user['email'], 'body'=>$message ]);
                Midrepo::$thereIsMail = true;
                
            }
        }
    }

    public static function newPost($content)
    {
        $spaceId = $content->space_id;

        $types = ['MESSAGE', 'LINK', 'TABLE', 'CHART', 'POLL','EVENT', 'LOCATION', 'WIKI'];

        $users = \User::where('id', '<>', \Auth::user()->id)
            ->whereHas('spaces', function($q) use($spaceId){
                $q->where('space_id', $spaceId)->where('role', '>=', ROLE_MEMBER);
            })
            ->whereRaw('(notif_post >0 or email_post >0)')->get();

        $notiList=[];

        $message = trans('messages.new_post_notification',[
            'user'=>$content->user->full_name,
            'type'=> strtolower(trans('client.'.$types[$content->class_id])),
            'space'=>$content->space->title]);

        $message .= link_to('/#space/'.$content->space->code, trans('client.GO_TO_SPACE'));

        foreach($users as $user) {
            $notiList[] = [
                'user_id' => $user->id,
                'email'   => $user->email,
                'nt'      => $user->notif_post == 1 || ($user->notif_post == 2 && $content->class_id == CONTENT_EVENT),
                'em'      => $user->email_post == 1 || ($user->email_post == 2 && $content->class_id == CONTENT_EVENT)
            ];
        }

        static::dispatch($notiList, $message);


    }

}