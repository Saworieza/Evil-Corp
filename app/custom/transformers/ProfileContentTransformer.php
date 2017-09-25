<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;


class ProfileContentTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        $ret['space'] = $item['space']['title'];
        $ret['space_code'] = $item['space']['code'];

        return $ret;
    }

} 