<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;

class UserTransformer extends Transformer {

    public function transform($item)
    {
        $ret['full_name'] = $item['full_name'];
        $ret['email'] = $item['email'];



        return $ret;
    }

} 