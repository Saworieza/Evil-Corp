<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class SpaceTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        for($i=0; $i<count($ret['users']); $i++ )
        {
            $ret['users'][$i]['id'] = $ret['users'][$i]['pivot']['user_id'];
            $ret['users'][$i]['role'] = $ret['users'][$i]['pivot']['role'];
            $ret['users'][$i]['last_visit'] = $ret['users'][$i]['pivot']['last_visit'];
            unset($ret['users'][$i]['pivot']);
            unset($ret['users'][$i]['code']);
        }

        $ret['default_space'] = $ret['id']== env('DEFAULT_SPACE',0);
        return $ret;
    }

} 