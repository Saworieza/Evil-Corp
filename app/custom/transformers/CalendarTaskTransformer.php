<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class CalendarTaskTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;
        $ret['start'] = $ret['calendar']['start_date'];
        $ret['end'] = $ret['calendar']['end_date'];
        $ret['allDay'] = $ret['calendar']['all_day'];
        $ret['start'][10]='T';
        $ret['start'] .= 'Z';
        $ret['end'][10]='T';
        $ret['end'] .= 'Z';

        unset($ret['calendar']);
        unset($ret['history']);
        unset($ret['created_at']);
        unset($ret['updated_at']);
        unset($ret['description']);
        unset($ret['state']);
        unset($ret['archived']);
        unset($ret['space_id']);
        unset($ret['assigned_to']);
        unset($ret['user']);

     //   $ret['start'] = $ret['content_data']['start_date'];
//

        return $ret;
    }

} 