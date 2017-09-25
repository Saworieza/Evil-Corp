<?php
/**
 * Created by PhpStorm.
 * User: lautarosrur
 * Date: 01/10/14
 * Time: 08:51
 */

namespace custom\transformers;



use Illuminate\Support\Facades\Auth;

class WikiTransformer extends Transformer {

    public function transform($item)
    {
        $ret = $item;


        $ret['created_by'] = ["id"=>$ret['created_by']['id'], "full_name"=>$ret['created_by']['full_name']];
        $ret['updated_by'] = ["id"=>$ret['updated_by']['id'], "full_name"=>$ret['updated_by']['full_name']];
        $ret['body'] = null;

        for($i=0; $i<count($ret['attachments']);$i++)
        {
           $ret['attachments'][$i]['icon'] =  getFileTypeIconUrl($ret['attachments'][$i]['file_ext']);


        }
        return $ret;
    }

} 