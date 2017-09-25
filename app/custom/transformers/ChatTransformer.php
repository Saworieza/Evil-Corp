<?php

namespace custom\transformers;


use Illuminate\Support\Facades\Auth;

class ChatTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;
        $ret['full_name'] = $ret['user']['full_name'];
        unset($ret['user']);
        $ret['me'] = $ret['user_id'] == Auth::user()->id;



        return $ret;
    }

} 