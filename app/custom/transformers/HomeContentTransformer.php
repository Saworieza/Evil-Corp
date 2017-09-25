<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;


class HomeContentTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        unset($ret['space_id']);
        unset($ret['content_data']);
        unset($ret['updated_at']);

        $ret['space_code'] = $ret['space']['code'];
        $ret['space'] = $ret['space']['title'];

        if(strlen($ret['content_text']) > 450)
        {
            $ret['content_text'] = truncateHtml($ret['content_text'], 450);
            $ret['truncated'] = true;

        }

        return $ret;
    }

} 