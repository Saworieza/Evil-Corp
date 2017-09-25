<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class AllUsersTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        $ret['id']  = $ret['pivot']['user_id'];
        $ret['role']  = $ret['pivot']['role'];
        $ret['last_visit']  = $ret['pivot']['last_visit'];
        unset($ret['pivot']);
        return $ret;
    }

} 