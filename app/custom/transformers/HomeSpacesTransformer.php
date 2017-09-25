<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class HomeSpacesTransformer extends Transformer {

    public function transform($item)
    {

        $ret['code']    = $item['code'];
        $ret['title']   = $item['title'];
        $ret['access']  = $item['access'];
        $ret['stars']   = $item['stars'];
        $ret['role']    = $item['pivot']['role'];
        $ret['user_count'] = count($item['users']);
        $ret['last_visit'] = $item['pivot']['last_visit'];
        $ret['total'] = $item['total'];

        $ret['counter'] = [];

        for($i=6; $i>=0;$i--)
        {
            $dte = date( 'Y-m-d', strtotime("-$i days"));

            if( $found = findWhere( $item['new_posts'], ['date_count'=>$dte]) )
            {
                $ret['counter'][]= $found->content_count;

            } else {
                $ret['counter'][]= 0;
            }
        }


        return $ret;
    }

} 