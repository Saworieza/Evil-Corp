<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class MessageTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        if(isset($ret['from_user']))
        {
            $ret['from_user'] = $ret['from_user']['full_name'];
            $ret['kind'] = 'MSG';
        } else {
            $ret['kind'] = 'NTF';

        }

        if(isset($ret['to_user']))
        {
            $ret['to_user'] = $ret['to_user']['full_name'];
            $ret['kind'] = 'MSG';
        }

        $ret['read'] = (bool)$ret['read'];



        return $ret;
    }

} 