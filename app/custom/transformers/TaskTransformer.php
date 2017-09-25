<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class TaskTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        if(isset($item['calendar']))
        {

           $ret['due_date'] = $item['calendar']['start_date'] ? $item['calendar']['start_date']: '';
            unset($ret['calendar']);
        }

        $ret['archived'] = (boolean)$ret['archived'];

        $ret['assignments'] = implode(', ', array_pluck($ret['assigned_to'], 'full_name'));

        $ret['my'] = false;
        $ret['delegated'] = false;

        if(count($ret['assigned_to'])==0 )
        {
            $ret['my'] = true;

        } else {
            foreach($ret['assigned_to'] as $usr)
            {
                if($usr['user_id'] == \Auth::user()->id)
                {
                    $ret['my'] = true;
                } else {
                    $ret['delegated'] = true;
                }
            }

        }

        $this->removePivot($ret['assigned_to']);

        $this->renameKey($ret['assigned_to'], 'user_id', 'id');

        if(isset($ret['space']))
        {
            if(Auth::user()->inSpace($ret['space_id'])>=ROLE_MEMBER)
            {
                $ret['space'] = $item['space']['title'];

            } else {
                $ret['space']= '';
                unset($ret['conent_id']);
            }

        } else {
            $ret['space']= '';

        }

        unset($ret['space_id']);


            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('tomorrow'));
            $next_week = date('Y-m-d', strtotime('tomorrow + 7 day'));
            $dte = substr($item['calendar']['start_date'],0,10);

            if(empty($dte))
                $ret['due_date_group'] = "5"; //some day

            elseif($dte < $today)
                $ret['due_date_group'] = "0"; // overdue
            elseif($dte == $today)
                $ret['due_date_group'] =  "1"; //Today
            elseif($dte == $tomorrow)
                $ret['due_date_group'] =  "2"; //tomorrow
            elseif($dte > $tomorrow && $dte <= $next_week)
                $ret['due_date_group'] = "3"; // Next 7 days
            else
                $ret['due_date_group'] = "4"; // later


        return $ret;
    }

} 