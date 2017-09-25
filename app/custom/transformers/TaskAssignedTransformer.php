<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 02/10/14
 * Time: 17:36
 */

namespace custom\transformers;


class TaskAssignedTransformer extends Transformer {

    public function transform($item)
    {
//        var_dump($item);
//        dd();
        $ret['assigned_to'] = array_map(function($i){
            return ['id'=>$i['user_id'], 'full_name'=>$i['full_name']];
        },$item['assigned_to']->toArray());

        $ret['assignments'] = implode(', ', array_pluck($ret['assigned_to'], 'full_name'));

//        $ret['full_name'] = $item['full_name'];
 //       $ret['id'] = $item['user_id'];

        return $ret;
    }

} 