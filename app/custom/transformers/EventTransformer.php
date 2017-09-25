<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class EventTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;


        $ret['start_date'] = $item['content_data']['start_date'] ? $item['content_data']['start_date']: '';
        $ret['end_date'] = $item['content_data']['end_date'] ? $item['content_data']['end_date']: '';
        $ret['all_day'] = $item['content_data']['all_day'] ;
        $ret['type'] = $item['content_data']['type'] ;

        unset($ret['content_data']);
        unset($ret['user_id']);
        unset($ret['class_id']);
        unset($ret['content_text']);
        unset($ret['space_id']);

//        $this->removePivot($ret['assigned_to']);


        return $ret;
    }

} 