<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class AdminSpacesTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        unset($ret['options']);
        unset($ret['user_id']);
        return $ret;
    }

} 