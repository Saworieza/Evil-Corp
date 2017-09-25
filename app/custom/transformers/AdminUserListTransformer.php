<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class AdminUserListTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        $ret['admin'] = (bool)$ret['admin'];

        return $ret;
    }

} 