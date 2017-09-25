<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class SpaceListTransformer extends Transformer {

    public function transform($item)
    {
        $ret['id']       = $item['id'];
        $ret['code']       = $item['code'];
        $ret['title']      = $item['title'];
        $ret['access']     = $item['access'];
        $ret['active']     = $item['active'];
//        $ret['last_visit'] = $item['pivot']['last_visit'];
//        $ret['new_posts']  = $item['new_posts'];

        return $ret;
    }

} 