<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UserProfileTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        unset($ret['activation_code']);
        unset($ret['activation_expire']);
        unset($ret['activation_date']);
        unset($ret['create_spaces']);
        unset($ret['remember_token']);
        unset($ret['created_at']);
        unset($ret['updated_at']);

        unset($ret['state']);


        if($this->param != \Auth::user()->id)
        {
            if(!$ret['showemail']) unset($ret['email']);
            unset($ret['admin']);
            unset($ret['notif_content']);
            unset($ret['notif_content_starred']);
            unset($ret['notif_invite']);
            unset($ret['notif_like']);
            unset($ret['notif_mention']);
            unset($ret['notif_task']);

            unset($ret['email_content']);
            unset($ret['email_content_starred']);
            unset($ret['email_invite']);
            unset($ret['email_like']);
            unset($ret['email_mention']);
            unset($ret['email_task']);
            unset($ret['email_private_msg']);

        } else {
            foreach(['content', 'content_starred', 'like', 'mention', 'invite'] as $event)
            {
                $ret['notif_'.$event] = (bool)$ret['notif_'.$event];
                $ret['email_'.$event] = (bool)$ret['email_'.$event];
            }
            $ret['email_private_msg'] = (bool)$ret['email_private_msg'];

        }
        $ret['showemail'] = (bool)$ret['showemail'];

        if(file_exists( base_path('profile_fields.php')))
        {
            $ret['custom_fields'] = include base_path('profile_fields.php');
        }


        return $ret;
    }

} 