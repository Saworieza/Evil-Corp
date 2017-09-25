<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;


class ContentTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;

        $ret['truncated'] = false;
        if(! \Input::has('truncate'))
        {
            if(strlen($ret['content_text']) > 450)
            {
                $ret['content_text'] = truncateHtml($ret['content_text'], 450);
                $ret['truncated'] = true;

            }
        }
        $ret['comment_count'] = count($item['comments']);
        $ret['starred']       = !empty($item['starred']);

        $ret['comments']      = array_slice($item['comments'],-3);
        $ret['task_summary'] = array_map(function($item){
            return [
                'id'    => $item['id'],
                'title' => $item['title'],
                'state' => $item['state'],
                'priority' => $item['priority'],
                'full_name' => $item['user']['full_name'],
                'due_date'  => $item['calendar']['start_date'],
                'assignments' => implode(', ',array_pluck($item['assigned_to'], 'full_name'))
            ];
        },$ret['task_summary']);

        $states = ['Not started', 'In progress', 'Suspended', 'Completed', 'Cancelled'];

        $ret['task_count'] = $this->countIf( $ret['task_summary'], 'state', [0,1,2] );

        if(isset($ret['space']))
        {
            $ret['space'] = $ret['space']['title'];
        }

        return $ret;
    }

} 