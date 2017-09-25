<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class CalendarEventTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        $ret['title']  = $ret['content_data']['type'];

        $ret['start'] = $ret['calendar']['start_date'];
        $ret['end'] = $ret['calendar']['end_date'];
        $ret['allDay'] = $ret['calendar']['all_day'];

        $ret['start'][10]='T';
        $ret['start'] .= 'Z';
        $ret['end'][10]='T';
        $ret['end'] .= 'Z';
        unset($ret['content_data']);
        unset($ret['class_id']);

//        foreach($item['events'] as $event)
//        {
//            dd($event);
//        }



        return $ret;
    }

} 