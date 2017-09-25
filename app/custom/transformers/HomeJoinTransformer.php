<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class HomeJoinTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;
        unset($ret->id);

        return $ret;
    }

} 